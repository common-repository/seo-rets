<?php

header("Content-Type: application/xml");

ini_set('memory_limit', '256M');
ini_set('max_execution_time', '1200');
set_time_limit(60);

$last = get_option("sr_lastsitemap");

$last = false;

if (!$last || $last < strtotime("-1 day")) {//current sitemap is too old or doesn't exist, we need a new one
    $types = array();

    foreach ($this->metadata as $type => $val) {
        if ($this->is_type_hidden($type)) {
            continue;
        }
        $types[$type] = 0;//types serves as a count of the listings we've downloaded for each type
    }                                        //we use this count to determine an offset to resume downloading at


    $site = home_url();
    $done = false;//done is set to true after we loop through all types meaning we've downloaded all types
    $x = 1;

    while (!$done) {

        $n = 0;//n is the current row we're on for the current sitemap file
        $fh = fopen($this->server_plugin_dir . "/sitemaps/sitemap-" . $x++ . ".xml", "w");//open up a new sitemap and shift the next sitemap index up by one
        fwrite($fh, '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');//write the header for every sitemap


        while ($n < 50000 && !$done) {//while the current sitemap has space available (i.e. less than 50000 URLS)

            $offset = current($types);//get the count of downloaded listings for the current type and use it as offset
            $curtype = key($types);//get the name of the current type


            $response = $this->api_request('get_listings', array(//make an API request to get listing information
                'type' => $curtype,
                'query' => array(
                    'boolopr' => 'AND',
                    'conditions' => array()
                ),
                'limit' => array(
                    'offset' => $offset,
                    'range' => min(500, 50000 - $n)//only get 1000 or less if 1000 would cause us to go over our 50000 url limit for the current sitemap
                )
            ));

            //var_dump($response);

            $listings = $response->result;

            foreach ($listings as $l) {
                $url = $site . $this->listing_to_url($l, $curtype);//get the url for each listing

                fwrite($fh, '<url><loc>' . $url . '</loc><lastmod>' . date(DATE_W3C, strtotime($l->date_modified)) . '</lastmod><changefreq>daily</changefreq><priority>0.5</priority></url>');//write out the url to the sitemap with the modification timestamp

            }

            $count = count($listings);//count how many listings we got from the last API request

            $types[$curtype] += $count;//add that to the global count for the current type

            if ($count < min(1000, 50000 - $n)) {//if we got less results from the API than we asked for then that must mean that we're out of listings for that type
                if (next($types) === false) $done = true;//advance to the next type and if the next type doesn't exist then that must mean that we're done
            }
            $n += $count;//add the count from the last API request to the total for the current sitemap
        }

        fwrite($fh, "</urlset>");//write the closing sitemap tag
        fclose($fh);//close the current sitemap
    }

    update_option('sr_lastsitemap', time()); // update the time we generated the last sitemap to the correct time
}
$n = isset($_GET['n']) ? intval($_GET['n']) : 1; // if the request explicitly requested a particular sitemap give them it or give them the first sitemap
$loc = $this->server_plugin_dir . "/sitemaps/sitemap-" . $n . ".xml"; // get the location of the sitemap they requested

if (file_exists($loc)) echo file_get_contents($this->server_plugin_dir . "/sitemaps/sitemap-" . $n . ".xml");//if we can find the sitemap then echo it to the client

exit;//end script execution to make sure WordPress doesn't do anything funky after our sitemap is output

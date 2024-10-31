<?php


class SEO_Rets_Widget extends WP_Widget
{

    function SEO_Rets_Widget()
    {
        global $seo_rets_plugin;

        $this->sr = $seo_rets_plugin;

        $widget_ops = array('classname' => 'sr-listings-widget', 'description' => 'Displays listings in sidebar from your SEO Rets feed.');
        $control_ops = array('width' => 300, 'height' => 350, 'id_base' => 'seo-rets-widget');
        $this->WP_Widget('seo-rets-widget', 'SEO Rets Listings', $widget_ops, $control_ops);
    }

    function widget($args, $instance)
    {
        echo $args['before_widget'];
        ?>
        <h3 class="widget-title"><?php echo $instance['title'] ?></h3>
        <?php

        $instance['num_homes'] = $instance['num_homes'] ? $instance['num_homes'] : "5";

        if (!empty($instance['city'])) {
            foreach ($instance['city'] as $city) {
                $conditions2[] = array(
                    'operator' => 'LIKE',
                    'field' => 'city',
                    'loose' => true,
                    'value' => $city
                );
            }
            $conditions[] = array(
                "boolopr" => "OR",
                "conditions" => $conditions2
            );
        }


        if ($instance['minimum_price']) {
            $conditions[] = array(
                'operator' => '>=',
                'field' => 'price',
                'value' => $instance['minimum_price']
            );
        }
        if ($instance['maximum_price']) {
            $conditions[] = array(
                'operator' => '<=',
                'field' => 'price',
                'value' => $instance['maximum_price']
            );
        }
        $prioritization = get_option('sr_prioritization');
        $prioritization = ($prioritization === false) ? array() : $prioritization;

        /* WTF were you thinking John?
        if ( count($prioritization) > 0 ) {

                  foreach ( $prioritization as $p ) {
                            $cs[] = array(
                                      'operator' => '=',
                                      'field' => $p['field'],
                                      'value' => $p['id']
                            );
                  }

                  $conditions[] = array(
                            'boolopr' => 'OR',
                            'conditions' => $cs
                  );
        }*/

        if ($instance['display_prop'] == "agentid") {
            $conditions[] = array(
                'field' => 'agent_id',
                'operator' => '=',
                'value' => $instance['agent_id']
            );
        }
        // BRETT NEVILLE Addition of Office Listings Option
        if ($instance['display_prop'] == "officeid") {
            $conditions[] = array(
                'field' => 'office_id',
                'operator' => '=',
                'value' => $instance['office_id']
            );
        }

        if ($instance['display_prop'] == "mlsid") {
            $mlsids = explode(",", $instance['mls_id']);
            if (count($mlsids) == 1) {
                $conditions[] = array(
                    'field' => 'mls_id',
                    'operator' => '=',
                    'value' => $instance['mls_id']
                );
            } else {
                $subconditions = array();
                foreach ($mlsids as $mlsid) {
                    $subconditions[] = array(
                        'field' => 'mls_id',
                        'operator' => '=',
                        'value' => trim($mlsid)
                    );
                }
                $conditions[] = array(
                    "boolopr" => "OR",
                    "conditions" => $subconditions
                );
            }
        }

        $type = isset($instance['class']) ? $instance['class'] : "res";
        $query = $this->sr->prioritize(array(
            'type' => $type,
            //'order' => $order,
            'query' => array(
                'boolopr' => 'AND',
                'conditions' => !empty($conditions) ? $conditions : array()
            )
        ), $prioritization);

        $only = ($instance['display_prop'] == "onlymine") ? true : false;

        if ($only && count($prioritization) > 0) {
            array_pop($query);
        }

        $count = $this->sr->api_request('get_listings', array(//TODO limit the fields returned to only 1 non existant field or fix the API to allow querying for 0 range
            'query' => $query,
            'count' => true,
            'limit' => array(
                'range' => 1
            )
        ));

        if ($instance['num_homes'] >= $count->count) {
            $offset = 0;
        } else {
            $max = $count->count - $instance['num_homes'];
            $offset = rand(0, $max);
        }

        $request = $this->sr->api_request('get_listings', array(
            'query' => $query,
            'limit' => array(
                'range' => $instance['num_homes'],
                'offset' => $offset
            )
        ));


        if (isset($request->result) && count($request->result) > 0):
            shuffle($request->result);
            foreach ($request->result as $listing):
                $url = $this->sr->listing_to_url($listing, $type);

                if (isset($instance['use_div']) && $instance['use_div']): ?>
                    <div class="row">
                        <div class="col-md-4 col-sm-4">
                            <?php if ($listing->photos > 0): ?>
                                <a href="<?php echo get_bloginfo('url') ?><?php echo $url ?>"><img
                                        class="img-responsive"
                                        src="http://img.seorets.com/<?php echo $this->sr->feed->server_name ?>/<?php echo $listing->seo_url ?>-<?php echo $listing->mls_id ?>-1.jpg"/></a>
                            <?php else: ?>
                                <div class="srm-photo-none-small">No<br/>Photo</div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8 col-sm-8">
                            <a href="<?php echo get_bloginfo('url') ?><?php echo $url ?>"><strong><?php echo ucwords(strtolower($listing->address)) ?></strong></a>
                            <p class="srm-sidebar-price">Price: $<?php echo number_format($listing->price) ?></p>
                            <p class="srm-sidebar-beds">Beds: <?php echo $listing->bedrooms ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="srm-listing-sidebar">
                        <table>
                            <tr>
                                <td>
                                    <?php if ($listing->photos > 0): ?>
                                        <a href="<?php echo get_bloginfo('url') ?><?php echo $url ?>"><img
                                                src="http://img.seorets.com/<?php echo $this->sr->feed->server_name ?>/<?php echo $listing->seo_url ?>-<?php echo $listing->mls_id ?>-1.jpg"
                                                width="65" height="65"/></a>
                                    <?php else: ?>
                                        <div class="srm-photo-none-small">No<br/>Photo</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo get_bloginfo('url') ?><?php echo $url ?>"><strong><?php echo ucwords(strtolower($listing->address)) ?></strong></a>
                                    <p class="srm-sidebar-price">Price:
                                        $<?php echo number_format($listing->price) ?></p>
                                    <p class="srm-sidebar-beds">Beds: <?php echo $listing->bedrooms ?> | Baths: <?php echo $listing->baths_full ?> </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                endif;
            endforeach;
        else: ?>
            No Results
        <?php endif;

        echo $args['after_widget'];
    }

    function update($new_instance, $old_instance)
    {
        return $new_instance;
    }

    function form($instance) // This function outputs to admin panel
    {
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>"
                   value="<?php echo isset($instance['title']) ? $instance['title'] : "" ?>" style="width:95%;"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('class') ?>">Property Type:</label>
            <select id="<?php echo $this->get_field_id('class') ?>" name="<?php echo $this->get_field_name('class') ?>"
                    style="width:95%;">
                <?php
                foreach (get_object_vars($this->sr->metadata) as $object) {
                    $class = $object->system_name;
                    $name = $object->pretty_name;
                    if (SEO_RETS_Plugin::is_type_hidden($class)) {
                        continue;
                    }

                    if (isset($instance['class']) && $instance['class'] == $class): ?>
                        <option value="<?php echo htmlentities($class) ?>"
                                selected><?php echo htmlentities($name) ?></option>
                    <?php else: ?>
                        <option value="<?php echo htmlentities($class) ?>"><?php echo htmlentities($name) ?></option>
                    <?php endif;
                }
                ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('num_homes') ?>">Number Of Homes To Display:</label><br/>
            <input id="<?php echo $this->get_field_id('num_homes') ?>"
                   name="<?php echo $this->get_field_name('num_homes') ?>"
                   value="<?php echo isset($instance['num_homes']) ? $instance['num_homes'] : "" ?>"
                   style="width:30px;"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('city') ?>">City:</label><br/>
            <select multiple id="<?php echo $this->get_field_id('city') ?>"
                    name="<?php echo $this->get_field_name('city') ?>[]" style="width:95%;">
                <option value="">Any</option>
                <?php
                $cities = array();
                foreach (get_object_vars($this->sr->metadata) as $object) {
                    $fields = $object->fields;
                    if (isset($fields->city->values) && is_array($fields->city->values)) {
                        $cities = array_merge($cities, $fields->city->values);
                    }
                }
                sort($cities);
                foreach (array_unique($cities) as $city): ?>
                    <?php if (isset($instance['city']) && in_array($city, $instance['city'])): ?>
                        <option value="<?php echo htmlentities($city) ?>"
                                selected><?php echo htmlentities($city) ?></option>
                    <?php else: ?>
                        <option value="<?php echo htmlentities($city) ?>"><?php echo htmlentities($city) ?></option>
                    <?php endif;
                endforeach; ?>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('minimum_price') ?>">Minimum Price:</label><br/>
            <input id="<?php echo $this->get_field_id('minimum_price') ?>"
                   name="<?php echo $this->get_field_name('minimum_price') ?>"
                   value="<?php echo isset($instance['minimum_price']) ? $instance['minimum_price'] : "" ?>"
                   style="width:100px;"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('maximum_price') ?>">Maximum Price:</label><br/>
            <input id="<?php echo $this->get_field_id('maximum_price') ?>"
                   name="<?php echo $this->get_field_name('maximum_price') ?>"
                   value="<?php echo isset($instance['maximum_price']) ? $instance['maximum_price'] : "" ?>"
                   style="width:100px;"/>
        </p>

        <p>
            Display properties:<br/>
            <input type="radio" name="<?php echo $this->get_field_name('display_prop') ?>"
                   value="all"<?php echo (!isset($instance['display_prop']) || $instance['display_prop'] == "all") ? " checked" : "" ?> />
            All<br/>
            <input type="radio" name="<?php echo $this->get_field_name('display_prop') ?>"
                   value="onlymine"<?php echo (isset($instance['display_prop']) && $instance['display_prop'] == "onlymine") ? " checked" : "" ?> />
            My properties<br/>
            <input type="radio" name="<?php echo $this->get_field_name('display_prop') ?>"
                   value="agentid"<?php echo (isset($instance['display_prop']) && $instance['display_prop'] == "agentid") ? " checked" : "" ?> />
            From this agent ID: <input type="text" name="<?php echo $this->get_field_name('agent_id') ?>"
                                       value="<?php echo isset($instance['agent_id']) ? htmlentities($instance['agent_id']) : "" ?>"
                                       style="width:8em;"/><br/>
            
            <?php // BRETT NEVILLE Addition of Office Listings Option ?>
            <input type="radio" name="<?php echo $this->get_field_name('display_prop') ?>"
                   value="officeid"<?php echo (isset($instance['display_prop']) && $instance['display_prop'] == "officeid") ? " checked" : "" ?> />
            From this Office ID: <input type="text" name="<?php echo $this->get_field_name('office_id') ?>"
                                       value="<?php echo isset($instance['office_id']) ? htmlentities($instance['office_id']) : "" ?>"
                                       style="width:8em;"/><br/>                           
            
            <input type="radio" name="<?php echo $this->get_field_name('display_prop') ?>"
                   value="mlsid"<?php echo (isset($instance['display_prop']) && $instance['display_prop'] == "mlsid") ? " checked" : "" ?> />
            With this MLS ID: <input type="text" name="<?php echo $this->get_field_name('mls_id') ?>"
                                     value="<?php echo isset($instance['mls_id']) ? htmlentities($instance['mls_id']) : "" ?>"
                                     style="width:8em;"/>
        </p>
        <input type="hidden" name="<?php echo $this->get_field_name('use_div') ?>" value=""/>
        <p>
            <input type="checkbox" value="checked"
                   name="<?php echo $this->get_field_name('use_div') ?>"<?php echo (isset($instance['use_div']) && $instance['use_div'] == "checked") ? " checked" : "" ?> />
            Use div tags for layout
        </p>
        <?php
    }
}

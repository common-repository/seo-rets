function addCommas(nStr) {nStr += '';x = nStr.split('.');x1 = x[0];x2 = x.length > 1 ? '.' + x[1] : '';var rgx = /(\d+)(\d{3})/;while (rgx.test(x1)) {x1 = x1.replace(rgx, '$1' + ',' + '$2');}return x1 + x2;}

function initialize() {
    var myLatlng = new google.maps.LatLng(39.83, -98.58);
    var myOptions = {
        zoom: 4,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    bounds = new google.maps.LatLngBounds();

    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    for ( var n in listings ) {
        if ( listings[n].lat !== "" ) {
            markers.push(new SR_InfoMarker(listings[n]));
        }
    }

    var geocoder = new google.maps.Geocoder();

    for ( var p in geocode ) {
        geocoder.geocode({'address': geocode[p].address + " " + geocode[p].city + ", " + geocode[p].state}, (function(x) {
            return function(results, status) {
                if ( status == google.maps.GeocoderStatus.OK ) {
                    geocode[x].lat = results[0].geometry.location.lat();
                    geocode[x].lng = results[0].geometry.location.lng();
                    markers.push(new SR_InfoMarker(geocode[x]));
                }
            }})(p));
    }


    if ( us_bounds != null ) {
        map.fitBounds(new google.maps.LatLngBounds(new google.maps.LatLng(parseFloat(us_bounds[0]), parseFloat(us_bounds[1])), new google.maps.LatLng(parseFloat(us_bounds[2]), parseFloat(us_bounds[3]))));
    }

    //markers[0].open();
}

function SR_InfoMarker(listing) {

    var infowindow = new google.maps.InfoWindow({
        content: '<table><tr><td><a target="_parent" href="'+blogURL + listing.url + '"><img style="width:130px;height:86px;" src="http://img.seorets.com/'+serverName+'/' + listing.seo_url + '-' + listing.mls_id + '-1.jpg" /></a></td><td valign="top"><strong><a target="_parent" href="'+blogURL + listing.url + '">' + listing.address + '</a></strong><br />Price: $' + addCommas(listing.price) + '<br />Bedrooms: ' + listing.bedrooms + '<br />Baths: ' + listing.baths_full + '</td></tr></table>'
    });


    var myLatLng = new google.maps.LatLng(listing.lat, listing.lng);

    var marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
        title: listing.address
    });

    google.maps.event.addListener(marker, 'click', function() {
        for ( var n in markers ) {
            markers[n].close();
        }
        infowindow.open(map, marker);
    });

    this.open = function() {
        infowindow.open(map, marker);
    };

    this.close = function() {
        infowindow.close();
    };

    bounds.extend(myLatLng);
    map.fitBounds(bounds);



}
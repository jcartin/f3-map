(function( $ ) {
    // https://www.google.com/maps/place//@33.9981442,-80.9907453,14.79z/data=!4m5!3m4!1s0x0:0x97f699dd46da92e9!8m2!3d33.9991702!4d-80.9940058
    var map, infoWindows;
    function F3_InitMap(selector) {

        // the maps js file is not loaded if the plugin does not have an API key defined.
        if (!google || !google.maps || !google.maps.Map) return;

        map = new google.maps.Map(document.getElementById('f3-map'), {
            center: { lat: 34.0088279, lng: -80.99547369999999},
            zoom: 12
        });

        infoWindow = new google.maps.InfoWindow();

        var data = [];
        selector = selector || '.ao-location';
        $(selector).each(function(index, item) {
            data.push($(item).data());
        });
        data = groupLocations(data);
        $.each(data, function(i, data) {
            mapao(
                data.lat, 
                data.lng, 
                data.location, 
                data.workout,    
                data.line1, 
                data.line2, 
                data.day, 
                data.starttime, 
                data.endtime, 
                data.items
            );
        });
    }

    function handleLocationError(browserHasGeoLocation, infoWindow, pos) {
        infoWindow.setPosition(pos);
        infoWindow.setContent(browserHasGeoLocation ? 'Error: GeoLocation service failed.' : 'Error: Browser doesn\'t support goelocation');
        infoWindow.open(map);
    }

    function toTitleCase(str) {
        return str.replace(/\w\S*/g, function(txt) {
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        });
    }

    function groupLocations(items) {
        // group each item by the lat/lng coordinates.
        var keys = [];

        $.each(items, function(i, data) {
            var existing = LatLngExists(keys, data.lat, data.lng);
            if (!existing) {
                keys.push({
                    lat: data.lat, 
                    lng: data.lng, 
                    location: data.location, 
                    workouts: [toTitleCase(data.workout)],
                    workout: '',
                    line1: data.line1, 
                    line2: data.line2, 
                    items: [data]
                });
            } 
            else {
                // lat/lng already exists. concatenate the workout(s)
                existing.workouts.push(toTitleCase(data.workout));
                existing.items.push(data);
            }
        });

        _.each(keys, function(key) {
            key.workout = _.uniq(key.workouts).join();
        });

        return keys;
    }

    function LatLngExists(items, lat, lng) {
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            if (item.lat == lat && item.lng == lng) {
                return item;
            }
        }

        return null;
    }

    function mapao(lat, lng, name, workout, line1, line2, dayOfTheWeek, startTime, endTime, items) {
        if (!map) return;

        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(lat, lng), 
            map: map, 
            title: name
        });

        marker.addListener('click', function(evt) {

            $('#f3-map-details-location').text(name);
            $('#f3-map-details-address').text(line1 + ' ' + line2);

            var m = [];
            var t = [];
            var w = [];
            
            $.each(items, function(index, item) {
                
            });

            $('.f3-map-details').show();

            return;

    var txt = '<div id="content">';
    txt += '<h1>' + workout + '</h1>';
    txt += '<div class="info-body">';
    txt += '<p>';
    txt += name + '<br />';
    txt += line1 + '<br />';
    txt += line2 + '<br />';
    txt += '</p></div></div>';

            var iw = new google.maps.InfoWindow({
                content: txt
            });
            iw.open(map, marker);
        });
    }

    // expose the main entry point globally. This is probably a bad idea and should be refactored soon! 
    window.F3_InitMap = F3_InitMap;

})(jQuery);
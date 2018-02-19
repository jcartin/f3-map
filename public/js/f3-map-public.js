(function( $ ) {
    // https://www.google.com/maps/place//@33.9981442,-80.9907453,14.79z/data=!4m5!3m4!1s0x0:0x97f699dd46da92e9!8m2!3d33.9991702!4d-80.9940058
    var map, infoWindows;
    function F3_InitMap(selector, lat, lng) {

        // the maps js file is not loaded if the plugin does not have an API key defined.
        if (!google || !google.maps || !google.maps.Map) return;

        var pos = {
            latitude: lat, 
            longitude: lng
        };

        // attempt to center the map on the current user's location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                pos.latitude = position.coords.latitude;
                pos.longitude = position.coords.longitude;

                setupMap(selector, pos);
            });
        } else {
            setupMap(selector, pos);
        }
    }

    function setupMap(selector, pos) {
        map = new google.maps.Map(document.getElementById('f3-map'), {
            center: { lat: pos.latitude, lng: pos.longitude},
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

            var count = 0;
            count += evaluateWeekday(items, 'Sunday', '#f3-map-detail-sunday');
            count += evaluateWeekday(items, 'Monday', '#f3-map-detail-monday');
            count += evaluateWeekday(items, 'Tuesday', '#f3-map-detail-tuesday');
            count += evaluateWeekday(items, 'Wednesday', '#f3-map-detail-wednesday');
            count += evaluateWeekday(items, 'Thursday', '#f3-map-detail-thursday');
            count += evaluateWeekday(items, 'Friday', '#f3-map-detail-friday');
            count += evaluateWeekday(items, 'Saturday', '#f3-map-detail-saturday');

            if (count > 0) {
                $('.f3-map-details').show();
                $('.f3-map-details')[0].scrollIntoView({
                    behavior: "smooth", 
                    block: "end"
                });
            }
        });
    }

    function evaluateWeekday(items, day, selector) {
        var aos = [];

        $.each (items, function(i, item) {
            if (!item || !item.day) return;
            if (item.day === day) aos.push(item);
        });

        $(selector).text('');

        $.each(aos, function(i, item) {
            $(selector).append('<div><div class="f3-map-details-workout">' + item.workout + '</div><div class="f3-map-details-time">(' + item.starttime + ' - ' + item.endtime + ')</div></div>');
        });

        return aos.length;
    }

    // expose the main entry point globally. This is probably a bad idea and should be refactored soon! 
    window.F3_InitMap = F3_InitMap;

})(jQuery);
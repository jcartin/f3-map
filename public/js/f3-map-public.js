(function( $ ) {
    var map, infoWindows;
    var markers = [];

    function F3SetupMap() {
        $(function() {
            var config = $("#f3-map-details-config");
            if (config) {
                var selector = config.data("selector");
                var lat = config.data("lat");
                var lng = config.data("lng");

                F3_InitMap(selector, lat, lng);
            }
        });
    }

    function F3_InitMap(selector, lat, lng) {

        // the maps js file is not loaded if the plugin does not have an API key defined.
        if (!google || !google.maps || !google.maps.Map) return;

        var pos = {
            latitude: lat, 
            longitude: lng
        };

        // attempt to center the map on the current user's location
        if (!lat && !lng && navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                pos.latitude = position.coords.latitude;
                pos.longitude = position.coords.longitude;

                console.log(pos);

                setupMap(selector, pos);
            });
        } else {
            setupMap(selector, pos);
        }
    }

    function setupMap(selector, pos) {

        var mapElement = document.getElementById('f3-map');

        if (mapElement === null) return;

        map = new google.maps.Map(mapElement, {
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
            mapao(data);
        });

        // now setup the marker links
        $.each($(".f3-table-cell-marker"), function(i, data) {
            $(data).click(function() {
                var d = $(data).data();
                
                var center = new google.maps.LatLng(d.lat, d.lng);
                map.panTo(center);

                // now see if there is an existing marker at this position, and if so
                // let's click on it.
                for (var i = 0; i < markers.length; i++) {
                    if (markers[i].getPosition().equals(center)) {
                        google.maps.event.trigger(markers[i], 'click');
                    }
                }
            });
        });

        console.log(map);
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
        if (!items || !lat || !lng) return null;

        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            if (item.lat == lat && item.lng == lng) {
                return item;
            }
        }

        return null;
    }

    function mapao(data) {
        if (!map) return;

        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(data.lat, data.lng), 
            map: map, 
            title: data.name, 
            draggable: false, 
            animation: google.maps.Animation.DROP
        });
        markers.push(marker);

        marker.addListener('click', function(evt) {

            if (marker.getAnimation() !== null) {
                marker.setAnimation(null);
            } else {
                marker.setAnimation(google.maps.Animation.BOUNCE);
            }

            setTimeout(function() {
                if (marker.getAnimation() !== null) {
                    marker.setAnimation(null);
                }
            }, 1000);

            $('#f3-map-details-location').text(data.name);
            $('#f3-map-details-address').text(data.line1 + ' ' + data.line2);

            var count = 0;
            count += evaluateWeekday(data.items, 'Sunday', '#f3-map-detail-sunday');
            count += evaluateWeekday(data.items, 'Monday', '#f3-map-detail-monday');
            count += evaluateWeekday(data.items, 'Tuesday', '#f3-map-detail-tuesday');
            count += evaluateWeekday(data.items, 'Wednesday', '#f3-map-detail-wednesday');
            count += evaluateWeekday(data.items, 'Thursday', '#f3-map-detail-thursday');
            count += evaluateWeekday(data.items, 'Friday', '#f3-map-detail-friday');
            count += evaluateWeekday(data.items, 'Saturday', '#f3-map-detail-saturday');

            if (count > 0) {
                $('.f3-map-details').show();

                // only scroll the calendar into view if the mobile style sheet 
                // is being used.
                if (window.innerWidth <= 768) {
                    $('html, body').animate({
                        scrollTop: $(".f3-map-details").offset().top
                    }, 1000);
                } 
                
            } else {
                $('.f3-map-details').hide();
            }
        });
    }

    function evaluateWeekday(items, day, selector) {
        if (!items || !day || !selector) return 0;

        var aos = [];

        $.each (items, function(i, item) {
            if (!item || !item.day) return;
            if (item.day === day) aos.push(item);
        });

        if (aos.length === 0) {
            // no AOs to report. we need to still draw the divs below
            aos.push({workout: '', starttime: '', endtime: ''});
        }

        $(selector).text('');

        $.each(aos, function(i, item) {
            var timeframe = item.starttime;
            if (item.endtime || item.endtime.length > 0) {
                timeframe = timeframe + ' - ' + item.endtime;
            }

            var txt = '<div>';
                txt += '<div class="f3-map-details-workout">';
                    txt += toTitleCase(item.workout) + '&nbsp;';
                txt += '</div>';
                txt += '<div class="f3-map-details-time">';
                    txt += timeframe + '&nbsp;';
                txt += '</div>';
                txt += '<div>';
                    txt += item.style || '';
                txt += '</div>';
            txt += '</div>';

            //$(selector).append('<div><div class="f3-map-details-workout">' + toTitleCase(item.workout) + '&nbsp;</div><div class="f3-map-details-time">' + timeframe + '&nbsp;</div></div>');
            $(selector).append(txt);
        });

        return aos.length;
    }

    // expose the main entry point globally. This is probably a good idea and should be refactored soon! 
    window.F3SetupMap = F3SetupMap;

})(jQuery);
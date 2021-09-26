<script>
<?php
    $defaultLatLng = '';

    if (isset($marker) && $marker->get_coordinates()) {
        $defaultLatLng = $marker->get_coordinates();
    } else if (isset($editing) && $editing) {
        $defaultLatLng = '52,19';
    }
?>
    const defaultLatLng = [<?= $defaultLatLng; ?>];
    const map = L.map('marker-location-map').setView(defaultLatLng, 6);
    L.tileLayer('https://{s}.osm.rrze.fau.de/osmhd/{z}/{x}/{y}.png').addTo(map);
    const marker = L.marker(defaultLatLng);
    let isMarkerAdded = false;
    <?php if (isset($marker) && $marker->get_coordinates()): ?>
    marker.addTo(map);
    isMarkerAdded = true;
    <?php endif; ?>
    <?php if (isset($editing) && $editing): ?>
    map.on('click', (event) => {
        marker.setLatLng(event.latlng);
        jQuery('#coordinates').val(event.latlng.lat + ',' + event.latlng.lng);

        if (!isMarkerAdded) {
            marker.addTo(map);
            isMarkerAdded = true;
        }
    });
    jQuery('#marker-get-location-button').on('click', function () {
        if (navigator.geolocation) {
            const $self = jQuery(this);
            $self.toggleClass('disabled').text('Pobieram lokalizację...');
            navigator.geolocation.getCurrentPosition(function (position) {
                $self.toggleClass('disabled').text('Pobierz aktualną lokalizację');
                const { latitude, longitude } = position.coords;
                marker.setLatLng({
                    lat: latitude,
                    lng: longitude,
                });
                jQuery('#coordinates').val(latitude + ',' + longitude);
                map.setView([latitude, longitude], 15);

                if (!isMarkerAdded) {
                    marker.addTo(map);
                    isMarkerAdded = true;
                }
            }, null, {
                enableHighAccuracy: true
            });
        } else {
            jQuery(this).addClass('disabled').text('Twoja przeglądarka nie wspiera geolokalizacji.');
        }
    })
    <?php endif; ?>
</script>
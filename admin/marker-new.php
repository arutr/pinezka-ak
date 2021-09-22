<div class="wrap">
    <h1 class="wp-heading">Nowa pinezka</h1>
    <?php if (isset($errors) && is_wp_error($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors->get_error_messages() as $err): ?>
                    <li><?= $err ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $msg): ?>
            <div id="message" class="updated notice is-dismissible">
                <p><?= $msg ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (isset($add_user_errors) && is_wp_error($add_user_errors)): ?>
        <div class="error">
            <?php foreach ($add_user_errors->get_error_messages() as $message): ?>
                <p><?= $message ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div id="ajax-response"></div>
    <form method="post" name="createmarker" id="createmarker" class="validate" novalidate="novalidate"
          enctype="multipart/form-data">
        <input name="action" type="hidden" value="createmarker" />
        <?php wp_nonce_field('create-marker', '_wpnonce_create-marker'); ?>
        <?php
        // Load up the passed data, else set to a default.
        $creating = isset($_POST['createmarker']);

        $new_marker_name = $creating && isset($_POST['name']) ? wp_unslash($_POST['name']) : '';
        $new_marker_description = $creating && isset($_POST['description']) ? wp_unslash($_POST['description']) : '';
        $new_marker_type = $creating && isset($_POST['type']) ? wp_unslash($_POST['type']) : '';
        $new_marker_city = $creating && isset($_POST['city']) ? wp_unslash($_POST['city']) : '';
        $new_marker_region = $creating && isset($_POST['region']) ? wp_unslash($_POST['region']) : '';
        ?>
        <table class="form-table" role="presentation">
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="name">
                        <?= __('Name'); ?>
                        <span class="description"><?= __('(required)'); ?></span>
                    </label>
                </th>
                <td>
                    <input name="name" type="text" id="name" value="<?= esc_attr($new_marker_name); ?>"
                           aria-required="true" maxlength="60" />
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="description">
                        <?= __('Description'); ?>
                    </label>
                </th>
                <td>
                    <textarea class="textarea-wrap" name="description" id="description" rows="10"
                              maxlength="1000"><?= esc_attr($new_marker_description); ?></textarea>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="coordinates">
                        Miejsce pinezki
                    </label>
                </th>
                <td>
                    <div id="marker-location-map" style="height: 400px; width: 95%;"></div>
                    <input name="coordinates" type="hidden" id="coordinates" />
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="city">
                        <?= __('City'); ?>
                    </label>
                </th>
                <td>
                    <input name="city" type="text" id="city" value="<?= esc_attr($new_marker_city); ?>"
                           maxlength="255" />
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="region">
                        Województwo
                    </label>
                </th>
                <td>
                    <input name="region" type="text" id="region" value="<?= esc_attr($new_marker_region); ?>"
                           maxlength="255" />
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="type">
                        Rodzaj
                    </label>
                </th>
                <td>
                    <select name="type" id="type">
                        <option value="">--- Wybierz rodzaj pinezki ---</option>
                        <option value="grave">Mogiła</option>
                        <option value="statue">Pomnik</option>
                        <option value="exterior_memorial">Tablica pamiątkowa na zewnątrz</option>
                        <option value="interior_memorial">Tablica pamiątkowa wewnątrz</option>
                        <option value="other">Inne</option>
                    </select>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="image">
                        <?= __('Image'); ?>
                    </label>
                </th>
                <td>
                    <input name="marker-image" type="file" id="image" accept="image/png, image/jpeg" />
                </td>
            </tr>
        </table>
        <?php submit_button('Dodaj nową pinezkę', 'primary', 'createmarker', true, ['id' => 'createmarkersub']); ?>
    </form>
</div>
<script>
    const defaultLatLng = [52, 19];
    const map = L.map('marker-location-map').setView(defaultLatLng, 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    const marker = L.marker(defaultLatLng);
    let isMarkerAdded = false;
    map.on('click', (event) => {
        marker.setLatLng(event.latlng);
        jQuery('#coordinates').val(event.latlng.lat + ',' + event.latlng.lng);

        if (!isMarkerAdded) {
            marker.addTo(map);
            isMarkerAdded = true;
        }
    });
</script>
<?php
if (!class_exists('Pinezka_Ak_Marker')) {
    require_once __DIR__ . '/includes/Pinezka_Ak_Marker.php';
}

if (!isset($marker)) {
    if (!isset($_REQUEST['id'])) {
        wp_die('Pinezka nie istnieje.');
    }

    $marker = new Pinezka_Ak_Marker();
    $marker = $marker->load($_REQUEST['id']);

    if (is_wp_error($marker)) {
        wp_die($marker);
    }
}

$editing = get_current_user_id() == $marker->get_user_id() || current_user_can('edit_users');
$readonly = !$editing ? 'readonly' : '';
?>
<div class="wrap">
    <h1 class="wp-heading"><?= $marker->get_name(); ?></h1>
    <?php if (isset($errors) && is_wp_error($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors->get_error_messages() as $err): ?>
                    <li><?= $err ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="updated">
            <ul>
                <li><?= $success; ?></li>
            </ul>
        </div>
    <?php endif; ?>
    <div id="ajax-response"></div>
    <form method="post" name="editmarker" id="editmarker" class="validate" novalidate="novalidate"
          enctype="multipart/form-data">
        <?php if ($editing): ?>
            <input name="action" type="hidden" value="editmarker" />
            <?php wp_nonce_field('edit-marker', '_wpnonce_edit-marker'); ?>
        <?php endif; ?>

        <table class="form-table" role="presentation">
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="name">
                        <?= __('Name'); ?>
                        <span class="description"><?= __('(required)'); ?></span>
                    </label>
                </th>
                <td>
                    <input name="name" type="text" id="name" value="<?= $marker->get_name(); ?>"
                           <?= $readonly ?> aria-required="true" maxlength="60" />
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="description">
                        <?= __('Description'); ?>
                    </label>
                </th>
                <td>
                    <textarea class="textarea-wrap" name="description" id="description" maxlength="255"
                              <?= $readonly ?>><?= $marker->get_description(); ?></textarea>
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
                    <?php if ($editing): ?>
                        <input name="coordinates" type="hidden" id="coordinates"
                               value="<?= $marker->get_coordinates() ?>" />
                    <?php endif; ?>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="city">
                        <?= __('City'); ?>
                    </label>
                </th>
                <td>
                    <input name="city" type="text" id="city" value="<?= $marker->get_city(); ?>"
                        <?= $readonly ?> maxlength="60" />
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="region">
                        Województwo
                    </label>
                </th>
                <td>
                    <input name="region" type="text" id="region" value="<?= $marker->get_region(); ?>"
                        <?= $readonly ?> maxlength="60" />
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="type">
                        Rodzaj
                    </label>
                </th>
                <td>
                    <select name="type" id="type" <?= !$editing ? 'disabled' : '' ?>>
                        <?php if ($editing): ?>
                            <option value="">--- Wybierz rodzaj pinezki ---</option>
                            <?php foreach (Pinezka_Ak_Marker::get_types() as $value => $label): ?>
                                <option value="<?= $value ?>"
                                    <?= $marker->get_type() == $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="<?= $marker->get_type(); ?>"><?= Pinezka_Ak_Marker::get_type_label($marker->get_type()); ?></option>
                        <?php endif; ?>
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
                    <?php if ($marker->get_image()): ?>
                        <img src="<?= wp_get_attachment_url($marker->get_image()); ?>" alt="Marker image" style="max-height: 400px; max-width: 95%;" />
                    <?php endif; ?>
                    <?php if ($editing): ?>
                        <input name="marker-image" type="file" id="image" accept="image/png, image/jpeg" />
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <?php if ($editing): ?>
            <?php submit_button('Aktualizuj pinezkę', 'primary', 'editmarker', true, ['id' => 'editmarkersub']); ?>
        <?php endif; ?>
    </form>
</div>
<script>
    <?php
    $defaultLatLng = '';

    if ($marker->get_coordinates()) {
        $defaultLatLng = $marker->get_coordinates();
    } else if ($editing) {
        $defaultLatLng = '52,19';
    }
    ?>
    const defaultLatLng = [<?= $defaultLatLng; ?>];
    const map = L.map('marker-location-map').setView(defaultLatLng, 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    const marker = L.marker(defaultLatLng);
    let isMarkerAdded = false;
    <?php if ($marker->get_coordinates()): ?>
    marker.addTo(map);
    isMarkerAdded = true;
    <?php endif; ?>
    <?php if ($editing): ?>
    map.on('click', (event) => {
        marker.setLatLng(event.latlng);
        jQuery('#coordinates').val(event.latlng.lat + ',' + event.latlng.lng);

        if (!isMarkerAdded) {
            marker.addTo(map);
            isMarkerAdded = true;
        }
    });
    <?php endif; ?>
</script>
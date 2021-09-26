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
            <?php Pinezka_Ak_Marker::get_name_form_html($new_marker_name, true); ?>
            <?php Pinezka_Ak_Marker::get_description_form_html($new_marker_description, true); ?>
            <?php Pinezka_Ak_Marker::get_coordinates_form_html('', true); ?>
            <?php Pinezka_Ak_Marker::get_city_form_html($new_marker_city, true); ?>
            <?php Pinezka_Ak_Marker::get_region_form_html($new_marker_region, true); ?>
            <?php Pinezka_Ak_Marker::get_type_form_html($new_marker_type, true); ?>
            <?php Pinezka_Ak_Marker::get_image_form_html('', true); ?>
            <?php Pinezka_Ak_Marker::get_agreement_form_html(); ?>
        </table>
        <?php submit_button('Dodaj nową pinezkę', 'primary', 'createmarker', true, ['id' => 'createmarkersub']); ?>
    </form>
</div>
<?php
$editing = true;
include_once __DIR__ . '/marker-map.php';
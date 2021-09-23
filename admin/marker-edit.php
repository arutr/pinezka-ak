<?php
if (!class_exists('Pinezka_Ak_Marker')) {
    require_once __DIR__ . '/includes/Pinezka_Ak_Marker.php';
}

if (isset($_REQUEST['action'])) {
    if ($_REQUEST['action'] === 'createmarker') {
        check_admin_referer('create-marker', '_wpnonce_create-marker');

        $marker = new Pinezka_Ak_Marker();
        $marker->set_name(sanitize_text_field($_POST['name']));
        $marker->set_description(sanitize_textarea_field($_POST['description']));
        $marker->set_coordinates(sanitize_text_field($_POST['coordinates']));
        $marker->set_type(sanitize_text_field($_POST['type']));
        $marker->set_city(sanitize_text_field($_POST['city']));
        $marker->set_region(sanitize_text_field($_POST['region']));
        $updated_marker = $marker->create();

        if (is_wp_error($updated_marker)) {
            $errors = $updated_marker;

            require_once __DIR__ . '/marker-new.php';
        } else {
            $success = 'Pinezka stworzona.';

            require_once __DIR__ . '/marker-view.php';
        }
    } else if ($_REQUEST['action'] === 'editmarker') {
        check_admin_referer('edit-marker', '_wpnonce_edit-marker');

        $marker = new Pinezka_Ak_Marker();
        $marker = $marker->load($_REQUEST['id']);

        if (is_wp_error($marker)) {
            wp_die($marker);
        }

        if (get_current_user_id() != $marker->get_user_id() && !current_user_can('edit_users')) {
            wp_die('Nie masz uprawnień.');
        }

        $marker->set_name(sanitize_text_field($_POST['name']));
        $marker->set_description(sanitize_textarea_field($_POST['description']));
        $marker->set_coordinates(sanitize_text_field($_POST['coordinates']));
        $marker->set_type(sanitize_text_field($_POST['type']));
        $marker->set_city(sanitize_text_field($_POST['city']));
        $marker->set_region(sanitize_text_field($_POST['region']));
        $updated_marker = $marker->update();

        if (is_wp_error($updated_marker)) {
            $errors = $updated_marker;
        } else {
            $success = 'Pinezka zaktualizowana.';
        }

        require_once __DIR__ . '/marker-view.php';
    }
} else if (isset($_REQUEST['id'])) {
    require_once __DIR__ . '/marker-view.php';
} else {
    require_once __DIR__ . '/marker-new.php';
}

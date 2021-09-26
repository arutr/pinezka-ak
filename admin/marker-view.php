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
        <?php if (!isset($_REQUEST['id'])): ?>
            <input type="hidden" name="id" value="<?= $marker->get_id(); ?>" />
        <?php endif; ?>
        <?php if ($editing): ?>
            <input name="action" type="hidden" value="editmarker" />
            <?php wp_nonce_field('edit-marker', '_wpnonce_edit-marker'); ?>
        <?php endif; ?>
        <table class="form-table" role="presentation">
            <?php Pinezka_Ak_Marker::get_name_form_html($marker->get_name(), $editing); ?>
            <?php Pinezka_Ak_Marker::get_description_form_html($marker->get_description(), $editing); ?>
            <?php Pinezka_Ak_Marker::get_coordinates_form_html($marker->get_coordinates(), $editing); ?>
            <?php Pinezka_Ak_Marker::get_city_form_html($marker->get_city(), $editing); ?>
            <?php Pinezka_Ak_Marker::get_region_form_html($marker->get_region(), $editing); ?>
            <?php Pinezka_Ak_Marker::get_type_form_html($marker->get_type(), $editing); ?>
            <?php Pinezka_Ak_Marker::get_image_form_html($marker->get_image(), $editing); ?>
            <?php if (current_user_can('edit_users')): ?>
                <?php Pinezka_Ak_Marker::get_points_criteria_form_html($marker->get_points_criteria()); ?>
            <?php endif; ?>
        </table>
        <?php if ($editing): ?>
            <?php submit_button(
                    'Aktualizuj pinezkę',
                    'primary',
                    'editmarker',
                    true,
                    ['id' => 'editmarkersub']);
            ?>
        <?php endif; ?>
    </form>
</div>
<?php
include_once __DIR__ . '/marker-map.php';
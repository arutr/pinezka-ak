<?php
require_once __DIR__ . '/includes/Pinezka_Ak_Markers_List_Table.php';

if (isset($_REQUEST['action'])) {
    if ($_REQUEST['action'] === 'delete_marker' && isset($_REQUEST['id'])) {
        $marker = new Pinezka_Ak_Marker();
        $marker = $marker->load($_REQUEST['id']);

        if (is_wp_error($marker)) {
            wp_die($marker);
        }

        if ($marker->get_user_id() != get_current_user_id()) {
            wp_die();
        }

        $result = $marker->delete();

        if (is_wp_error($result)) {
            wp_die($result);
        }

        $success = 'Pinezka ' . $marker->get_name() . ' została skasowana.';
        unset($marker);
    }
}

$pinezka_ak_markers_list_table = new Pinezka_Ak_Markers_List_Table();
$pinezka_ak_markers_list_table->prepare_items();
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Pinezki</h1>
    <a href="<?php menu_page_url('marker-edit') ?>" class="page-title-action">Dodaj nową</a>
    <hr class="wp-header-end" />
    <?php if (isset($success)): ?>
        <div class="updated">
            <ul>
                <li><?= $success; ?></li>
            </ul>
        </div>
    <?php endif; ?>
    <?php $pinezka_ak_markers_list_table->views(); ?>
    <form method="post">
        <input type="hidden" name="page" value="pinezka_ak_markers_search">
        <?php $pinezka_ak_markers_list_table->search_box('Szukaj', 'markers_search'); ?>
        <?php $pinezka_ak_markers_list_table->display(); ?>
    </form>
</div>
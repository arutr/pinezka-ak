<?php
require_once __DIR__ . '/includes/Pinezka_Ak_Markers_List_Table.php';

$pinezka_ak_markers_list_table = new Pinezka_Ak_Markers_List_Table();
$pinezka_ak_markers_list_table->prepare_items();
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Pinezki</h1>
    <a href="<?php menu_page_url('marker-edit') ?>" class="page-title-action">Dodaj nową</a>
    <hr class="wp-header-end" />
    <?php $pinezka_ak_markers_list_table->views(); ?>
    <form method="post">
        <input type="hidden" name="page" value="pinezka_ak_markers_search">
        <?php $pinezka_ak_markers_list_table->search_box('Szukaj', 'markers_search'); ?>
        <?php $pinezka_ak_markers_list_table->display(); ?>
    </form>
</div>
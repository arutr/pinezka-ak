<?php
require_once __DIR__ . '/includes/Pinezka_Ak_Teams_List_Table.php';

if (isset($_REQUEST['action'])) {
    if ($_REQUEST['action'] === 'join_team' && isset($_REQUEST['team_id'])) {
        if (!Pinezka_Ak_Team::is_user_in_team(get_current_user_id())) {
            $team = new Pinezka_Ak_Team();
            $team = $team->load($_REQUEST['team_id']);

            if (is_wp_error($team)) {
                wp_die($team);
            }

            $result = $team->add_team_member(get_current_user_id());

            if (is_wp_error($result)) {
                wp_die($result);
            }

            $success = 'Jesteś w zespole ' . $team->get_name() . '!';
        }
    } else if ($_REQUEST['action'] === 'leave_team') {
        if (Pinezka_Ak_Team::is_user_in_team(get_current_user_id())) {
            Pinezka_Ak_Team::leave_team();
            $success = 'Nie należysz już do zespołu.';
        }
    }
}

$pinezka_ak_teams_list_table = new Pinezka_Ak_Teams_List_Table();
$pinezka_ak_teams_list_table->prepare_items();
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Zespoły</h1>
    <a href="<?php menu_page_url('team-edit') ?>" class="page-title-action">Stwórz nowy</a>
    <?php if (Pinezka_Ak_Team::is_user_in_team(get_current_user_id())): ?>
        <?php $leaveTeamUrl = add_query_arg(['action' => 'leave_team'], menu_page_url('teams', false)); ?>
        <a href="<?= $leaveTeamUrl; ?>" class="page-title-action">Opuść zespół</a>
    <?php endif; ?>
    <hr class="wp-header-end" />
    <?php if (isset($success)): ?>
        <div class="updated">
            <ul>
                <li><?= $success; ?></li>
            </ul>
        </div>
    <?php endif; ?>
    <?php $pinezka_ak_teams_list_table->views(); ?>
    <form method="post">
        <input type="hidden" name="page" value="pinezka_ak_teams_search">
        <?php $pinezka_ak_teams_list_table->search_box('Szukaj', 'teams_search'); ?>
        <?php $pinezka_ak_teams_list_table->display(); ?>
    </form>
</div>
<?php
if (!class_exists('Pinezka_Ak_Team')) {
    require_once __DIR__ . '/includes/Pinezka_Ak_Team.php';
}

if (isset($_REQUEST['action'])) {
    if ($_REQUEST['action'] === 'createteam') {
        check_admin_referer('create-team', '_wpnonce_create-team');

        $team = new Pinezka_Ak_Team();
        $team->set_name(sanitize_text_field($_POST['name']));
        $team->set_leader_id(get_current_user_id());
        $team = $team->create();

        if (is_wp_error($team)) {
            $errors = $team;

            require_once __DIR__ . '/team-new.php';
        } else {
            require_once __DIR__ . '/team-view.php';
        }
    } else if ($_REQUEST['action'] === 'editteam') {
        check_admin_referer('edit-team', '_wpnonce_edit-team');

        $team = new Pinezka_Ak_Team();
        $team = $team->load($_REQUEST['id']);

        if (is_wp_error($team)) {
            wp_die($team);
        }

        if (get_current_user_id() != $team->get_leader_id() && !current_user_can('edit_users')) {
            wp_die('Nie masz uprawnień.');
        }

        $team->set_name(sanitize_text_field($_POST['name']));
        $team = $team->update();

        if (is_wp_error($team)) {
            $errors = $team;
        }

        require_once __DIR__ . '/team-view.php';
    }
} else if (isset($_REQUEST['id'])) {
    require_once __DIR__ . '/team-view.php';
} else {
    require_once __DIR__ . '/team-new.php';
}

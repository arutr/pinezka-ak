<?php
if (!class_exists('Pinezka_Ak_Team')) {
    require_once __DIR__ . '/includes/Pinezka_Ak_Team.php';
}

if (!isset($team)) {
    if (!isset($_REQUEST['id'])) {
        wp_die('Zespół nie istnieje.');
    }

    $team = new Pinezka_Ak_Team();
    $team = $team->load($_REQUEST['id']);

    if (is_wp_error($team)) {
        wp_die($team);
    }
}

$editing = get_current_user_id() == $team->get_leader_id() || current_user_can('edit_users');
$readonly = !$editing ? 'readonly' : '';
?>
<div class="wrap">
    <h1 class="wp-heading"><?= $team->get_name(); ?></h1>
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
    <form method="post" name="editteam" id="editteam" class="validate" novalidate="novalidate"
          enctype="multipart/form-data">
        <?php if ($editing): ?>
            <input name="action" type="hidden" value="editteam" />
            <?php wp_nonce_field('edit-team', '_wpnonce_edit-team'); ?>
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
                    <input name="name" type="text" id="name" value="<?= $team->get_name(); ?>"
                           <?= $readonly ?> aria-required="true" maxlength="60" />
                </td>
            </tr>
        </table>
        <?php if ($editing): ?>
            <?php submit_button('Aktualizuj zespół', 'primary', 'editteam', true, ['id' => 'editteamsub']); ?>
        <?php endif; ?>
    </form>
</div>
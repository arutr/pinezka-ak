<div class="wrap">
    <h1 class="wp-heading">Nowy zespół</h1>
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
    <form method="post" name="createteam" id="createteam" class="validate" novalidate="novalidate"
          enctype="multipart/form-data">
        <input name="action" type="hidden" value="createteam" />
        <?php wp_nonce_field('create-team', '_wpnonce_create-team'); ?>
        <?php
        // Load up the passed data, else set to a default.
        $creating = isset($_POST['createteam']);

        $new_team_name = $creating && isset($_POST['name']) ? wp_unslash($_POST['name']) : '';
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
                    <input name="name" type="text" id="name" value="<?= esc_attr($new_team_name); ?>"
                           aria-required="true" maxlength="60" />
                </td>
            </tr>
        </table>
        <?php submit_button('Stwórz nowy zespół', 'primary', 'createteam', true, ['id' => 'createteamsub']); ?>
    </form>
</div>
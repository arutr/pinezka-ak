<div class="wrap">
    <h1 class="wp-heading">Ustawienia zespołów</h1>
    <form method="POST" action="options.php">
        <?php
        settings_fields( 'team-settings' );
        do_settings_sections( 'team-settings' );
        submit_button();
        ?>
    </form>
</div>
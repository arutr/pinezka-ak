<?php

class Pinezka_Ak_Team
{
    /**
     * @var int
     */
    private int $ID;

    /**
     * @var int
     */
    private int $leader_id;

    /**
     * @var string
     */
    private string $name = '';

    /**
     * @param int $id
     *
     * @return $this|WP_Error
     */
    public function load(int $id)
    {
        global $wpdb;

        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM `pinezka_ak_team` WHERE ID = %d;", $id)
        );

        if (!$row) {
            return new WP_Error('invalid_team', 'Zespół nie istnieje.');
        }

        foreach (get_object_vars($row) as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }

    /**
     * @return $this|int|WP_Error
     */
    public function create()
    {
        global $wpdb;

        if (empty($this->name)) {
            return new WP_Error('empty_name', 'Nazwa zespołu jest wymagana.');
        }

        if (self::is_user_in_team(get_current_user_id())) {
            return new WP_Error('user_is_in_team', 'Uczestnik należy już do zespołu.');
        }

        $rows = $wpdb->insert(PINEZKA_AK_TEAM_TABLE, wp_unslash([
            'name'      => $this->name,
            'leader_id' => $this->leader_id
        ]));

        if ($rows == false) {
            return new WP_Error('db_error', 'Nie udało się stworzyć zespołu. Spróbuj ponownie lub skontaktuj się z nami!');
        }

        $this->ID = $wpdb->insert_id;

        $this->add_team_member(get_current_user_id());

        return $this;
    }

    /**
     * @return $this|WP_Error
     */
    public function update()
    {
        global $wpdb;

        if (empty($this->name)) {
            return new WP_Error('empty_name', 'Nazwa zespołu jest wymagana.');
        }

        $rows = $wpdb->update(PINEZKA_AK_TEAM_TABLE, wp_unslash([
            'name'  => $this->name,
        ]), [
            'ID' => strval($this->ID),
        ]);

        if ($rows == false) {
            return new WP_Error('db_error', 'Nie udało się zaktualizować zespołu. Spróbuj ponownie lub skontaktuj się z nami!');
        }

        return $this;
    }

    /**
     * @param int $user_id
     *
     * @return true|WP_Error
     */
    public function add_team_member(int $user_id)
    {
        global $wpdb;

        if (self::is_user_in_team($user_id)) {
            return new WP_Error('user_is_in_team', 'Należysz już do zespołu.');
        }

        $wpdb->insert(PINEZKA_AK_TEAM_MEMBER_TABLE, [
            'team_id' => $this->ID,
            'user_id' => $user_id,
        ]);

        return true;
    }

    /**
     *
     */
    public static function leave_team()
    {
        global $wpdb;

        $wpdb->delete(PINEZKA_AK_TEAM_MEMBER_TABLE, [
            'user_id' => get_current_user_id()
        ]);
    }

    /**
     * @param int $user_id
     *
     * @return bool
     */
    public static function is_user_in_team(int $user_id): bool
    {
        global $wpdb;

        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM `pinezka_ak_team_member` WHERE `user_id` = %d;", $user_id)
        );

        return $row != null;
    }

    /**
     * @return int
     */
    public function get_id(): int
    {
        return $this->ID;
    }

    /**
     * @return string
     */
    public function get_name(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function set_name(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function get_leader_id(): int
    {
        return $this->leader_id;
    }

    /**
     * @param int $leader_id
     */
    public function set_leader_id(int $leader_id): void
    {
        $this->leader_id = $leader_id;
    }
}
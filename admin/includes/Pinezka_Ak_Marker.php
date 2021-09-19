<?php

class Pinezka_Ak_Marker
{
    /**
     * @var int
     */
    private int $ID;

    /**
     * @var int
     */
    private int $user_id;

    /**
     * @var string
     */
    private string $name = '';

    /**
     * @var string
     */
    private string $description = '';

    /**
     * @var string
     */
    private string $coordinates = '';

    /**
     * @var string
     */
    private string $type = '';

    /**
     * @var string
     */
    private string $city = '';

    /**
     * @var string
     */
    private string $region = '';

    /**
     * @var string
     */
    private string $image = '';

    /**
     * @param int $id
     *
     * @return $this|WP_Error
     */
    public function load(int $id)
    {
        global $wpdb;

        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM `pinezka_ak_markers` WHERE ID = %d;", $id)
        );

        if (!$row) {
            return new WP_Error('invalid_marker', 'Pinezka nie istnieje.');
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
            return new WP_Error('empty_name', 'Nazwa pinezki jest wymagana.');
        }

        if ($_FILES['marker-image']['size']) {
            $image_attachment_id = media_handle_upload($this->image, 0);

            if (is_wp_error($image_attachment_id)) {
                return $image_attachment_id;
            }
        }

        $wpdb->insert(PINEZKA_AK_MARKERS_TABLE, wp_unslash([
            'user_id' => get_current_user_id(),
            'name' => $this->name,
            'description' => $this->description,
            'coordinates' => $this->coordinates,
            'type' => $this->type,
            'city' => $this->city,
            'region' => $this->region,
            'image' => $image_attachment_id ?? ''
        ]));
        $this->user_id = get_current_user_id();
        $this->ID = $wpdb->insert_id;

        return $this;
    }

    /**
     * @return $this|WP_Error
     */
    public function update()
    {
        global $wpdb;

        if (empty($this->name)) {
            return new WP_Error('empty_name', 'Nazwa pinezki jest wymagana.');
        }

        if ($_FILES['marker-image']['size']) {
            $image_attachment_id = media_handle_upload('marker-image', 0);

            if (is_wp_error($image_attachment_id)) {
                return $image_attachment_id;
            }

            $this->image = $image_attachment_id;
        }

        $rows = $wpdb->update(PINEZKA_AK_MARKERS_TABLE, wp_unslash([
            'name' => $this->name,
            'description' => $this->description,
            'coordinates' => $this->coordinates,
            'type' => $this->type,
            'city' => $this->city,
            'region' => $this->region,
            'image' => strval($this->image) ?? ''
        ]), [
            'ID' => strval($this->ID)
        ]);

        return $this;
    }

    /**
     * @return int
     */
    public function get_id(): int
    {
        return $this->ID;
    }

    /**
     * @return int
     */
    public function get_user_id(): int
    {
        return $this->user_id;
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
     * @return string
     */
    public function get_description(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function set_description(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function get_coordinates(): string
    {
        return $this->coordinates;
    }

    /**
     * @param string $coordinates
     */
    public function set_coordinates(string $coordinates): void
    {
        $this->coordinates = $coordinates;
    }

    /**
     * @return string
     */
    public function get_type(): string
    {
        return $this->type;
    }

    /**
     * @return string[]
     */
    public static function get_types(): array
    {
        return [
            'grave'             => 'Mogiła',
            'statue'            => 'Pomnik',
            'exterior_memorial' => 'Tablica pamiątkowa na zewnątrz',
            'interior_memorial' => 'Tablica pamiątkowa wewnątrz',
            'other'             => 'Inne',
        ];
    }

    /**
     * @param $type
     *
     * @return string
     */
    public static function get_type_label($type): string
    {
        $markerTypes = self::get_types();

        return $markerTypes[$type] ?? '';
    }

    /**
     * @param string $type
     */
    public function set_type(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function get_city(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function set_city(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function get_region(): string
    {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function set_region(string $region): void
    {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function get_image(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function set_image(string $image): void
    {
        $this->image = $image;
    }
}
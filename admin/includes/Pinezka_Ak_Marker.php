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
     * @var int
     */
    private int $points = 0;

    /**
     * @var string
     */
    private string $points_criteria = '';

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
            $this->$key = $value ?? 0;
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
            $image_attachment_id = media_handle_upload('marker-image', 0);

            if (is_wp_error($image_attachment_id)) {
                return $image_attachment_id;
            }
        }

        $rows = $wpdb->insert(PINEZKA_AK_MARKERS_TABLE, wp_unslash([
            'user_id' => get_current_user_id(),
            'name' => $this->name,
            'description' => $this->description,
            'coordinates' => $this->coordinates,
            'type' => $this->type,
            'city' => $this->city,
            'region' => $this->region,
            'image' => $image_attachment_id ?? '',
            'points' => $this->points,
            'points_criteria' => $this->points_criteria
        ]));

        if ($rows == false) {
            return new WP_Error('db_error', 'Nie udało się stworzyć pinezki. Spróbuj ponownie lub skontaktuj się z nami!');
        }

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

        if ($this->points_criteria) {
            $this->calculate_points();
        }

        $rows = $wpdb->update(PINEZKA_AK_MARKERS_TABLE, wp_unslash([
            'name' => $this->name,
            'description' => $this->description,
            'coordinates' => $this->coordinates,
            'type' => $this->type,
            'city' => $this->city,
            'region' => $this->region,
            'image' => strval($this->image) ?? '',
            'points' => $this->points,
            'points_criteria' => $this->points_criteria
        ]), [
            'ID' => $this->ID
        ]);

        if ($rows == false) {
            return new WP_Error('db_error', 'Nie udało się zaktualizować pinezki. Spróbuj ponownie lub skontaktuj się z nami!');
        }

        return $this;
    }

    /**
     * @return bool|WP_Error
     */
    public function delete()
    {
        global $wpdb;

        $rows = $wpdb->delete(PINEZKA_AK_MARKERS_TABLE, [
            'ID' => $this->ID
        ]);

        if ($rows == false) {
            return new WP_Error('db_error', 'Nie udało się skasować pinezki. Spróbuj ponownie lub skontaktuj się z nami!');
        }

        return true;
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
     * @param string $value
     * @param bool $editing
     */
    public static function get_name_form_html(string $value, bool $editing)
    {
        $label = __('Name');
        $required = __('(required)');
        $value = esc_attr($value);
        $readonly = !$editing ? 'readonly' : '';

        echo <<<HTML
<tr class="form-field form-required">
    <th scope="row">
        <label for="name">
            $label
            <span class="description">$required</span>
        </label>
    </th>
    <td>
        <input name="name" type="text" id="name" value="$value" $readonly aria-required="true" maxlength="255" />
    </td>
</tr>
HTML;
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
     * @param string $value
     * @param bool $editing
     */
    public static function get_description_form_html(string $value, bool $editing)
    {
        $descriptionLabel = __('Description');
        $value = esc_attr($value);
        $readonly = !$editing ? 'readonly' : '';
        ?>
        <tr class="form-field">
            <th scope="row">
                <label for="description">
                    <?= $descriptionLabel; ?>
                </label>
            </th>
            <td>
                <textarea class="textarea-wrap" name="description"
                    id="description" maxlength="1000" rows="10" <?= $readonly; ?>><?= $value; ?></textarea>
            </td>
        </tr>
        <?php
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
     * @param string $value
     * @param bool $editing
     */
    public static function get_coordinates_form_html(string $value, bool $editing)
    {
        $value = esc_attr($value);
        ?>
        <tr class="form-field">
            <th scope="row">
                <label for="coordinates">
                    Miejsce pinezki
                </label>
            </th>
            <td>
                <div id="marker-location-map" style="height: 400px; width: 95%;"></div>
                <?php if ($editing): ?>
                <br />
                <a id="marker-get-location-button" class="button">Pobierz aktualną lokalizację</a>
                <input name="coordinates" type="hidden" id="coordinates"
                       value="<?= $value; ?>" />
                <?php endif; ?>
            </td>
        </tr>
        <?php
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
     * @param string $value
     * @param bool $editing
     */
    public static function get_type_form_html(string $value, bool $editing)
    {
        $value = esc_attr($value);
        $valueLabel = Pinezka_Ak_Marker::get_type_label($value);
        $disabled = !$editing ? 'disabled' : '';

        ?>
        <tr class="form-field">
            <th scope="row">
                <label for="type">
                    Rodzaj
                </label>
            </th>
            <td>
                <select name="type" id="type" <?= $disabled ?>>
                <?php if ($editing): ?>
                    <option value="">--- Wybierz rodzaj pinezki ---</option>
                    <?php foreach (self::get_types() as $typeValue => $typeLabel): ?>
                        <?php $selected = $value == $typeValue ? 'selected' : ''; ?>
                        <option value="<?= $typeValue ?>" <?= $selected ?>>
                            <?= $typeLabel ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="<?= $value ?>"><?= $valueLabel ?></option>
                <?php endif; ?>
                </select>
            </td>
        </tr>
        <?php
    }

    /**
     * @return string[]
     */
    public static function get_types(): array
    {
        return [
            'grave'             => 'Mogiła / Grób',
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
     * @param string $value
     * @param bool $editing
     */
    public static function get_city_form_html(string $value, bool $editing)
    {
        $label = __('City');
        $value = esc_attr($value);
        $readonly = !$editing ? 'readonly' : '';

        echo <<<HTML
<tr class="form-field">
    <th scope="row">
        <label for="city">
            $label
        </label>
    </th>
    <td>
        <input name="city" type="text" id="city" value="$value" $readonly maxlength="255" />
    </td>
</tr>
HTML;
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

    public static function get_regions(): array
    {
        return [
            'Łódzkie' => 'Województwo łódzkie',
            'Poza łódzkiem' => 'Poza województwem łódzkim'
        ];
    }

    /**
     * @param $type
     *
     * @return string
     */
    public static function get_region_label($region): string
    {
        $regionTypes = self::get_regions();

        return $regionTypes[$region] ?? '';
    }

    /**
     * @param string $value
     * @param bool $editing
     */
    public static function get_region_form_html(string $value, bool $editing)
    {
        $value = esc_attr($value);
        $valueLabel = self::get_region_label($value);
        $disabled = !$editing ? 'disabled' : '';
        ?>
        <tr class="form-field">
            <th scope="row">
                <label for="region">
                    Województwo
                </label>
            </th>
            <td>
                <select name="region" id="region" <?= $disabled ?>>
                <?php if ($editing): ?>
                    <option value="">--- Wybierz województwo ---</option>
                    <?php foreach (self::get_regions() as $regionValue => $regionLabel): ?>
                        <?php $selected = $value == $regionValue ? 'selected' : ''; ?>
                        <option value="<?= $regionValue ?>" <?= $selected ?>>
                            <?= $regionLabel ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="<?= $value ?>"><?= $valueLabel ?></option>
                <?php endif; ?>
                </select>
            </td>
        </tr>
        <?php
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
     * @param string $value
     * @param bool $editing
     */
    public static function get_image_form_html(string $value, bool $editing)
    {
        echo <<<HTML
<tr class="form-field">
    <th scope="row">
        <label for="image">Zdjęcie miejsca</label>
    </th>
    <td>
HTML;
        if ($value) {
            $attachment_url = wp_get_attachment_url($value);

            echo <<<HTML
        <img src="$attachment_url" alt="Marker image" style="max-height: 400px; max-width: 95%;" />
        <br />
HTML;
        }

        if ($editing) {
            echo <<<HTML
        <input name="marker-image" type="file" id="image" accept="image/png, image/jpeg" />
HTML;
        }

        echo <<<HTML
    </td>
</tr>
HTML;
    }

    /**
     * @param string $image
     */
    public function set_image(string $image): void
    {
        $this->image = $image;
    }

    public static function get_agreement_form_html()
    {
        $required = __('(required)');
        ?>
        <tr class="form-field form-required">
            <th scope="row">
                <label for="agreements">
                    Oświadczenia
                    <span class="description"><?= $required ?></span>
                </label>
            </th>
            <td>
                <label for="agreement_copyright">
                    <input type="checkbox" name="agreement_copyright" id="agreement_copyright" aria-required="true" />
                    <span>
                        Oświadczam, że jestem autor(em/ką) dostarczonego opisu i zdjęcia do konkursu, a wszelkie treści
                        przepisane ze zdjęcia umieszczam w cudzysłowie. Nie naruszam praw autorskich oraz dóbr
                        osobistych innych osób.
                    </span>
                </label>
                <br />
                <br />
                <label for="agreement_tc">
                    <input type="checkbox" name="agreement_tc" id="agreement_tc" aria-required="true" />
                    <span>
                        Oświadczam, że zapoznał(em/am) się z treścią
                        <a target="_blank" href="https://80ak.pl/pinezka-ak/">regulaminu konkursu</a>, który rozumiem i
                        akceptuję.
                    </span>
                </label>
            </td>
        </tr>
        <?php
    }

    /**
     * @return int
     */
    public function get_points(): int
    {
        return $this->points;
    }

    /**
     * @return int
     */
    public function calculate_points(): int
    {
        $options = self::get_points_criteria_options();
        $this->points = $options[$this->points_criteria]['points'];

        return $this->points;
    }

    /**
     * @param int $points
     */
    public function set_points(int $points): void
    {
        $this->points = $points;
    }

    /**
     * @return string
     */
    public function get_points_criteria(): string
    {
        return $this->points_criteria;
    }

    /**
     * @return array[]
     */
    public static function get_points_criteria_options(): array
    {
        return [
            'first_unique' => [
                'points' => 10,
                'label' => 'Pierwsza unikalna prawidłowo zgłoszona pinezka.'
            ],
            'next_unique' => [
                'points' => 4,
                'label' => 'Druga i następna unikalna prawidłowo zgłoszona pinezka.'
            ],
            'nearby' => [
                'points' => 2,
                'label' => 'Pinezka zgłoszona w odległości mniejszej niż 100m od innych zgłoszonych przez siebie pinezek.'
            ],
//            'verification' => [
//                'points' => 4,
//                'label' => 'Merytoryczna weryfikacja cudzej pinezki – korekta nazwy; korekta opisu; korekta miejsca.'
//            ],
            'unique_incomplete' => [
                'points' => 1,
                'label' => 'Pinezka unikalna wybrakowana.'
            ],
            'duplicate_other' => [
                'points' => 1,
                'label' => 'Pinezka będąca zdublowaniem pinezki zgłoszonej wcześniej przez innego użytkownika.'
            ],
            'duplicate_own' => [
                'points' => 0,
                'label' => 'Pinezka będąca zdublowaniem własnej pinezki.'
            ]
        ];
    }

    /**
     * @param string $value
     */
    public static function get_points_criteria_form_html(string $value)
    {
        $value = esc_attr($value);
        ?>
        <tr class="form-field">
            <th scope="row">
                <label for="points_criteria">
                    Punktacja
                </label>
            </th>
            <td>
                <?php foreach (self::get_points_criteria_options() as $key => $optionValue): ?>
                    <?php $checked = $value == $key ? 'checked' : ''; ?>
                    <label for="<?= $key ?>">
                        <input type="radio" name="points_criteria" id="<?= $key ?>" value="<?= $key ?>" <?= $checked ?> />
                        <strong><?= $optionValue['points'] ?> pkt</strong>
                        <span><?= $optionValue['label'] ?></span>
                    </label>
                    <br />
                <?php endforeach; ?>
            </td>
        </tr>
        <?php
    }

    /**
     * @param string $points_criteria
     */
    public function set_points_criteria(string $points_criteria): void
    {
        $this->points_criteria = $points_criteria;
    }
}
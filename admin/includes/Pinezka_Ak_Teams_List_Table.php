<?php

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if (!class_exists('Pinezka_Ak_Team')) {
    require_once __DIR__ . '/Pinezka_Ak_Team.php';
}

class Pinezka_Ak_Teams_List_Table extends WP_List_Table
{
    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct([
            'singular' => 'Zespół',
            'plural' => 'Zespoły',
            'ajax' => false,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'ID':
            case 'name':
            case 'score':
                return $item[$column_name];
            default:
                return '';
        }
    }

    /**
     * @param $item
     *
     * @return string|void
     */
    protected function column_action($item)
    {
        if (!Pinezka_Ak_Team::is_user_in_team(get_current_user_id())) {
            $url = add_query_arg(
                ['action' => 'join_team', 'team_id' => $item['ID']],
                menu_page_url('teams', false)
            );

            return '<a href="' . $url .'"><strong>Dołącz</strong></a>';
        }
    }

    /**
     * @param $item
     *
     * @return string
     */
    protected function column_name($item)
    {
        $url = add_query_arg('id', $item['ID'], menu_page_url('team-edit', false));

        return '<strong><a class="row-title" href="' . $url . '">' . $item['name'] . '</a></strong>';
    }

    /**
     * @param $item
     *
     * @return bool|string
     */
    protected function get_team_score($item)
    {
        global $wpdb;

        $result = $wpdb->get_row("
SELECT COUNT(*)
FROM pinezka_ak_markers Markers
INNER JOIN pinezka_ak_team_member TeamMember
ON Markers.user_id = TeamMember.user_id
AND TeamMember.team_id = {$item['ID']};", ARRAY_N);

        return $result[0];
    }

    /**
     * @inheritDoc
     */
    public function get_columns(): array
    {
        return [
            'name'    => __('Name'),
            'ID'      => __('ID')
        ];
    }

    /**
     * @inheritDoc
     */
    protected function get_sortable_columns(): array
    {
        return [
            'ID'     => ['ID', false],
            'name'   => ['name', false],
            'score'  => ['score', false]
        ];
    }

    /**
     * @inheritDoc
     */
    public function prepare_items()
    {
        global $wpdb;

        $per_page = 20;
        $current_page = $this->get_pagenum();

        if ($current_page > 1) {
            $offset = $per_page * ($current_page - 1);
        } else {
            $offset = 0;
        }

        $search = '';

        // search by team name
        if (!empty($_REQUEST['s'])) {
            $search = "AND name LIKE '%" . esc_sql($wpdb->esc_like($_REQUEST['s'])) . "%'";
        }

        $column_keys = join(',', array_keys($this->get_columns()));
        $sql = "SELECT $column_keys FROM pinezka_ak_team WHERE 1=1 $search"
               . $wpdb->prepare("ORDER BY id DESC LIMIT %d OFFSET %d;", $per_page, $offset);
        $items = $wpdb->get_results($sql, ARRAY_A);
        $items = array_map(function ($item) {
            $item['score'] = $this->get_team_score($item);

            return $item;
        }, $items);

        $columns = $this->get_columns();
        $columns['score'] = 'Punkty';
        $columns['action'] = '';
        $hidden = [];
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [$columns, $hidden, $sortable];
        usort($items, [&$this, 'usort_reorder']);
        $count = $wpdb->get_var("SELECT COUNT(id) FROM pinezka_ak_team WHERE 1=1 {$search};");

        $this->items = $items;

        // Set the pagination
        $this->set_pagination_args([
            'total_items' => $count,
            'per_page'    => $per_page,
            'total_pages' => ceil($count / $per_page),
        ]);
    }

    /**
     * @param $a
     * @param $b
     *
     * @return float|int
     */
    private function usort_reorder($a, $b)
    {
        // If no sort, default to score
        $orderby = !empty($_GET['orderby']) ? $_GET['orderby'] : 'score';
        // If no order, default to asc
        $order = !empty($_GET['order']) ? $_GET['order'] : 'desc';

        if (in_array($orderby, ['ID', 'score'])) {
            $result = intval($a[$orderby]) - intval($b[$orderby]);
        } else {
            // Determine sort order
            $result = strcmp($a[$orderby], $b[$orderby]);
        }

        // Send final sort direction to usort
        return ($order === 'asc') ? $result : - $result;
    }
}
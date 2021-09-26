<?php

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if (!class_exists('Pinezka_Ak_Marker')) {
    require_once __DIR__ . '/Pinezka_Ak_Marker.php';
}

class Pinezka_Ak_Markers_List_Table extends WP_List_Table
{
    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct([
            'singular' => 'Pinezka',
            'plural' => 'Pinezki',
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
            case 'user_id':
            case 'type':
            case 'city':
            case 'region':
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
        if ($item['user_id'] == get_current_user_id() || current_user_can('delete_users')) {
            $url = add_query_arg(
                ['action' => 'delete_marker', 'id' => $item['ID']],
                menu_page_url('markers', false)
            );

            return '<a href="' . $url .'"><strong>Usuń</strong></a>';
        }
    }

    /**
     * @param $item
     *
     * @return string
     */
    protected function column_name($item)
    {
        $url = add_query_arg('id', $item['ID'], menu_page_url('marker-edit', false));

        return '<strong><a class="row-title" href="' . $url . '">' . $item['name'] . '</a></strong>';
    }

    /**
     * @param $item
     *
     * @return string
     */
    protected function column_user_id($item)
    {
        $author = new WP_User($item['user_id']);

        return $author->nickname;
    }

    /**
     * @param $item
     *
     * @return mixed
     */
    protected function column_type($item)
    {
        return Pinezka_Ak_Marker::get_type_label($item['type']);
    }

    /**
     * @inheritDoc
     */
    public function get_columns(): array
    {
        return [
            'name'    => __('Name'),
            'ID'      => 'Nr',
            'user_id' => __('Author'),
            'type'    => __('Type'),
            'city'    => __('City'),
            'region'  => 'Województwo',
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
            'type'   => ['type', false],
            'city'   => ['city', false],
            'region' => ['region', false],
        ];
    }

    /**
     * @return array
     */
    protected function get_views()
    {
        $views = [
            '' => __('All'),
            'mine' => 'Moje'
        ];

        array_walk($views, function (&$view, $class) {
            $html = '<a href="' . add_query_arg('type', $class) . '"';

            if ((empty($_REQUEST['type']) && empty($class)) ||
                (!empty($_REQUEST['type']) && $_REQUEST['type'] == $class)) {
                $html .= ' class="current"';
            }

            $html .= '>' . $view . '</a>';
            $view = $html;
        });

        return $views;
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

        // search by marker name
        if (!empty($_REQUEST['s'])) {
            $search = "AND name LIKE '%" . esc_sql($wpdb->esc_like($_REQUEST['s'])) . "%'";
        }

        $type = '';

        if (!empty($_REQUEST['type']) && $_REQUEST['type'] == 'mine') {
            $type = 'AND user_id = ' . get_current_user_id() . ' ';
        }

        $column_keys = join(',', array_keys($this->get_columns()));
        $sql = "SELECT $column_keys FROM pinezka_ak_markers WHERE 1=1 $search $type"
               . $wpdb->prepare("ORDER BY id DESC LIMIT %d OFFSET %d;", $per_page, $offset);
        $items = $wpdb->get_results($sql, ARRAY_A);

        $columns = $this->get_columns();
        $columns['action'] = '';
        $hidden = [];
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [$columns, $hidden, $sortable];
        usort($items, [&$this, 'usort_reorder']);
        $count = $wpdb->get_var("SELECT COUNT(id) FROM pinezka_ak_markers WHERE 1=1 {$search};");

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
        // If no sort, default to title
        $orderby = !empty($_GET['orderby']) ? $_GET['orderby'] : 'ID';
        // If no order, default to asc
        $order = !empty($_GET['order']) ? $_GET['order'] : 'desc';

        if ($orderby == 'ID') {
            $result = intval($a[$orderby]) - intval($b[$orderby]);
        } else {
            // Determine sort order
            $result = strcmp($a[$orderby], $b[$orderby]);
        }

        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }
}
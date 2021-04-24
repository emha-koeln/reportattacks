<?php 
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
class reportattacks_List_Table extends WP_List_Table
{

    public function __construct()
    {
        // Set parent defaults.
        parent::__construct(array(
            'singular' => 'failedlogin', // Singular name of the listed records.
            'plural' => 'failedlogins', // Plural name of the listed records.
            'ajax' => false, // Does this table support ajax?
            ));
    }


    public function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', // Render a checkbox instead of text.
            'time' => _x('date and time', 'Column label', 'reportattacks'),
            'ip' => _x('ip', 'Column label', 'reportattacks'),
            'user' => _x('user', 'Column label', 'reportattacks'),
            'reported' => _x('reported', 'Column label', 'reportattacks'),


            );

        return $columns;
    }

    protected function get_sortable_columns()
    {
        $sortable_columns = array(
            'ip' => array('ip', true),
            'user' => array('user', true),
            'time' => array('time', true),
            'reported' => array('reported', true),
            );

        return $sortable_columns;
    }

    protected function column_default($item, $column_name)
    {

        switch ($column_name) {
            case 'ip':
            case 'user':
            case 'time':
            case 'reported':
                return $item[$column_name];
            default:
                return print_r($item, true); // Show the whole array for troubleshooting purposes.
        }
    }


    protected function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->
            _args['singular'], // Let's simply repurpose the table's singular label ("movie").
            $item['id'] // The value of the checkbox should be the record's ID.
            );
    }


    
    protected function get_bulk_actions()
    {
        $actions = array(
            'delete' => _x('Delete', 'List table bulk action', 'reportattacks'),
            );

        return $actions;
    }    
    


    protected function process_bulk_action()
    {
        
        // Detect when a bulk action is being triggered.
        global $wpdb;
        if ('delete' === $this->current_action()) {


            if (isset($_GET['failedlogin'])) {
                $ctd = 0;
   
                $current_table = $wpdb->prefix . 'reportattacks_loginlog';

                $report_attacks_failedlogin =  $_GET['failedlogin'];

                if (gettype($report_attacks_failedlogin) != 'array')
                    $report_attacks_failedlogin = array($report_attacks_failedlogin);

                
                foreach ($_GET['failedlogin'] as $ip) {
            
                   $ctd++;
                   $wpdb->show_errors();
                  
                   $ip = sanitize_text_field($ip);
                  
                   $ip = trim($ip);
                   if (! is_numeric($ip))
                     continue;
                     
               
                   $query = "DELETE FROM " .$current_table .  " WHERE id = '".$ip ."' Limit 1";
                
                   $result = $wpdb->query( $query );
   

                    if (gettype($result) != 'integer')
                        if (gettype($result) != 'boolean')
                         //   if (!result)
                         //       $wpdb->print_error();

                    $wpdb->flush();
                }
                if ($ctd > 0)
                    echo '<h4>' . $ctd . ' updated!</h4>';

            }

        }

    }
   
    
    
    function reportattacks_prepare_items()
    {
        global $wpdb;
        global $option;

        $per_page = get_site_option( 'reportattacks_per_page', 20 );
 

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array(
            $columns,
            $hidden,
            $sortable);

        $this->process_bulk_action();


        $current_table = $wpdb->prefix . 'reportattacks_loginlog';

        if (isset($_GET['order']))
            $order = sanitize_text_field($_GET['order']);
        else
            $order = 'asc';


        if (isset($_GET['orderby']))
            $orderby = sanitize_text_field($_GET['orderby']);
        else
            $orderby = 'ip';


        if (isset($_GET['s'])) {

            $my_search = sanitize_text_field($_GET['s']);

            $results = $wpdb->get_results("SELECT * FROM $current_table  WHERE 
            `ip` LIKE  '%" . $my_search . "%'
             order by " . $orderby . " " . $order);

        } else {

            $results = $wpdb->get_results("SELECT * FROM $current_table order by " . $orderby .
                " " . $order);

        }


        $data = array();
        $i = 0;

        foreach ($results as $querydatum) {
            array_push($data, (array )$querydatum);
        }
        $current_page = $this->get_pagenum();

        $total_items = count($data);

        $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);

        $this->items = $data;

        $this->set_pagination_args(array(
            'total_items' => $total_items, // WE have to calculate the total number of items.
            'per_page' => $per_page, // WE have to determine how many items to show on a page.
            'total_pages' => ceil($total_items / $per_page), // WE have to calculate the total number of pages.
            ));
    }


    protected function usort_reorder($a, $b)
    {
        // If no sort, default to title.
        $orderby = !empty($_REQUEST['orderby']) ? wp_unslash(sanitize_text_field($_REQUEST['orderby'])) :
            'ip'; // WPCS: Input var ok.

        // If no order, default to asc.
        $order = !empty($_REQUEST['order']) ? wp_unslash(sanitize_text_field($_REQUEST['order'])) : 'asc'; // WPCS: Input var ok.

        // Determine sort order.
        $result = strcmp($a[$orderby], $b[$orderby]);

        return ('asc' === $order) ? $result : -$result;
    }

}

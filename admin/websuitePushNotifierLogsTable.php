<?php
    
    namespace WSPN\admin;
    
    use WP_List_Table;
    
    if (!class_exists('WP_List_Table')) {
        require_once(ABSPATH . '/wp-admin/includes/class-wp-list-table.php');
    }
    
    /**
     * A custom WP_List_Table class for the Push Notifier logs.
     *
     * @package WordPress
     * @subpackage List_Table
     */
    class websuitePushNotifierLogsTable extends WP_List_Table {
        /**
         * The ID of this plugin.
         *
         * @since 1.0.0
         * @access private
         * @var string $pluginName The ID of this plugin.
         */
        private string $pluginName;
        
        /**
         * The version of this plugin.
         *
         * @since 1.0.0
         * @access private
         * @var string $pluginVersion The current version of this plugin.
         */
        private string $pluginVersion;
        
        /**
         * The type of logs to display.
         *
         * @var string
         */
        private string $type;
        
        /**
         * @param string $pluginName
         * @param string $pluginVersion
         * @param string $type
         * @param array $args
         */
        public function __construct(string $pluginName, string $pluginVersion, string $type, $args = []) {
            $this->pluginName    = $pluginName;
            $this->pluginVersion = $pluginVersion;
            $this->type          = $type;
            
            parent::__construct($args);
        }
        
        /**
         * Prepare the items for the table to process
         *
         * @return Void
         */
        public function prepare_items() {
            $columns  = $this->get_columns();
            $hidden   = $this->getHiddenColumns();
            $sortable = $this->getSortableColumns();
            
            $data = $this->tableData();
            usort($data, [&$this, 'sort_data']);
            
            $perPage     = 10;
            $currentPage = $this->get_pagenum();
            $totalItems  = count($data);
            
            $this->set_pagination_args([
                'total_items' => $totalItems,
                'per_page'    => $perPage,
            ]);
            
            $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);
            
            $this->_column_headers = [$columns, $hidden, $sortable];
            $this->items           = $data;
        }
        
        /**
         * Override the parent columns method. Defines the columns to use in your listing table
         *
         * @return array
         */
        public function get_columns(): array {
            return (new websuitePushNotifierAdmin($this->pluginName, $this->pluginVersion))->getLogsFromTable($this->type, 'columns');
            
            return [
                'id'             => 'ID',
                'message'        => 'MESSAGE',
                'error_message'  => 'ERROR MESSAGE',
                'post_id'        => 'POST ID',
                'time_submitted' => 'TIME SUBMITTED',
            ];
        }
        
        /**
         * Define which columns are hidden
         *
         * @return array
         */
        public function getHiddenColumns(): array {
            return ['status'];
        }
        
        /**
         * Define the sortable columns
         *
         * @return array
         */
        public function getSortableColumns(): array {
            return ['time_submitted' => ['time_submitted', false]];
        }
        
        /**
         * Get the table data
         *
         * @return array
         */
        private function tableData(): array {
            return (new websuitePushNotifierAdmin($this->pluginName, $this->pluginVersion))->getLogsFromTable($this->type);
        }
        
        /**
         * Define what data to show on each column of the table
         *
         * @param array $item Data
         * @param string $column_name - Current column name
         *
         * @return mixed
         */
        public function column_default($item, $column_name) {
            switch ($column_name) {
                case 'message':
                    return "<pre style='white-space: pre-line;'>$item[$column_name];</pre>";
                case 'id':
                case 'post_id':
                case 'status':
                case 'error_message':
                case 'time_submitted':
                    return $item[$column_name];
                default:
                    return print_r($item, true);
            }
        }
        
        /**
         * Allows you to sort the data by the variables set in the $_GET
         *
         * @return float|int
         */
        private function sort_data($a, $b) {
            // Set defaults
            $orderby = 'time_submitted';
            $order   = 'desc';
            
            // If orderby is set, use this as the sort column
            if (!empty($_GET['orderby'])) {
                $orderby = sanitize_text_field($_GET['orderby']);
            }
            
            // If order is set use this as the order
            if (!empty($_GET['order'])) {
                $order = sanitize_text_field($_GET['order']);
            }
            
            
            $result = strcmp($a[$orderby], $b[$orderby]);
            
            if ($order === 'asc') {
                return $result;
            }
            
            return -$result;
        }
    }

<?php
    
    namespace WSPN\admin;
    
    use Aws\Exception\AwsException;
    use Aws\Sns\SnsClient;
    
    use JsonException;
    use Kreait\Firebase\Exception\FirebaseException;
    use Kreait\Firebase\Exception\MessagingException;
    use Kreait\Firebase\Factory;
    use Kreait\Firebase\Messaging\CloudMessage;
    use WP_HTTP_Proxy;
    use WSPN\wspn\websuitePushNotifierFields;
    use WSPN\wspn\websuitePushNotifierGlobal;
    
    if (!defined('ABSPATH')) {
        exit();
    }
    
    /**
     * The admin-specific functionality of the plugin.
     *
     * Defines the plugin name, version, and two examples hooks for how to
     * enqueue the admin-specific stylesheet and JavaScript.
     *
     * @package    WebsuitePushNotifier
     * @subpackage WebsuitePushNotifier/admin
     * @author     Johan Pretorius <johan.pretorius@afrozaar.com>
     */
    class websuitePushNotifierAdmin {
        
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
         * The database table associated with this plugin's logs.
         *
         * @var string
         */
        private string $pluginLogTable;
        
        /**
         * The fields class.
         *
         * @var websuitePushNotifierFields
         */
        private websuitePushNotifierFields $wspnFields;
        
        /**
         * Initialize the class and set its properties.
         *
         * @param string $pluginName The name of this plugin.
         * @param string $pluginVersion The version of this plugin.
         *
         * @since 1.0.0
         */
        public function __construct(string $pluginName, string $pluginVersion) {
            global $wpdb;
            
            $this->pluginName     = $pluginName;
            $this->pluginVersion  = $pluginVersion;
            $this->pluginLogTable = $wpdb->prefix . str_replace('-', '_', $this->pluginName) . "_logs";
            $this->wspnFields     = new websuitePushNotifierFields($this->pluginName, $this->pluginVersion);
        }
        
        /**
         * Register the JavaScript for the admin area.
         *
         * @since 1.0.0
         */
        public function enqueueScripts($hookSuffix): void {
            // Check if the page is the plugin page
            $pluginPageCheck = isset($_GET['page']) && stripos($_GET['page'], $this->pluginName) !== false;
            
            // Check if the page is the edit.php (Posts) page
            $postsPageCheck = ($hookSuffix === 'edit.php' || $hookSuffix === 'post.php');
            
            if ($pluginPageCheck || $postsPageCheck) {
                wp_enqueue_style($this->pluginName, plugin_dir_url(__FILE__) . 'css/websuite-push-notifier-admin-style.min.css', [], $this->pluginVersion);
                
                /**
                 * Libraries used for admin screen.
                 */
                if (!$postsPageCheck) {
                    wp_enqueue_style($this->pluginName . '-minicolors', plugin_dir_url(__FILE__) . 'assets/lib/minicolors.min.css', [], $this->pluginVersion);
                    wp_enqueue_style($this->pluginName . '-select2', plugin_dir_url(__FILE__) . 'assets/lib/select2.min.css', [], $this->pluginVersion);
                }
                wp_enqueue_style($this->pluginName . '-iziToast', plugin_dir_url(__FILE__) . 'assets/lib/iziToast.min.css', [], $this->pluginVersion);
                wp_enqueue_style($this->pluginName . '-tooltip', plugin_dir_url(__FILE__) . 'assets/lib/tooltip.min.css', [], $this->pluginVersion);
                
                /**
                 * Enqueue WP files.
                 */
                wp_enqueue_media();
                wp_enqueue_script('editor');
                
                /**
                 * Libraries used for admin screen.
                 */
                if (!$postsPageCheck) {
                    wp_enqueue_script($this->pluginName . '-dialog', plugins_url('assets/lib/dialog.min.js', __FILE__), [], $this->pluginVersion, true);
                    wp_enqueue_script($this->pluginName . '-minicolors', plugins_url('assets/lib/minicolors.min.js', __FILE__), [], $this->pluginVersion, true);
                    wp_enqueue_script($this->pluginName . '-select2', plugins_url('assets/lib/select2.full.min.js', __FILE__), [], $this->pluginVersion, true);
                    wp_enqueue_script($this->pluginName . '-html5sortable', plugins_url('assets/lib/html5sortable.min.js', __FILE__), [], $this->pluginVersion, true);
                    wp_enqueue_script($this->pluginName . '-select2sortable', plugins_url('assets/lib/select2.sortable.min.js', __FILE__), [], $this->pluginVersion, true);
                }
                wp_enqueue_script($this->pluginName . '-iziToast', plugins_url('assets/lib/iziToast.min.js', __FILE__), [], $this->pluginVersion, true);
                wp_enqueue_script($this->pluginName . '-tooltip', plugins_url('assets/lib/tooltip.min.js', __FILE__), [], $this->pluginVersion, true);
                
                /**
                 * Register script for plugin admin.
                 */
                wp_enqueue_script($this->pluginName, plugin_dir_url(__FILE__) . 'js/websuite-push-notifier-admin-script.min.js', [
                    'jquery',
                    'jquery-ui-slider',
                    'wp-plugins',
                    'wp-edit-post',
                    'wp-element',
                    'wp-components',
                ], $this->pluginVersion, false);
                
                /**
                 * Ajax Libraries
                 */
                wp_localize_script($this->pluginName, 'wspnOptionsObject', [
                    'ajax_url' => admin_url('admin-ajax.php'),
                    '_nonce'   => wp_create_nonce($this->pluginName),
                ]);
            }
        }
        
        /**
         * Register the administration menu for this
         * plugin into the WordPress Dashboard menu.
         *
         * @since 1.0.0
         */
        public function wspnAddPluginAdminMenu(): void {
            /**
             * Add a settings page for this plugin to the Settings menu.
             *
             * Alternative menu locations are available via WordPress administration menu functions.
             *
             * Administration Menus: http://codex.wordpress.org/Administration_Menus
             */
            add_menu_page(__('WebSuite Push Notifier Menu', $this->pluginName), __('WebSuite Push Notifier', $this->pluginName), 'manage_options', $this->pluginName, [
                $this,
                'wspnDisplayPluginSetupPage',
            ], plugin_dir_url(__FILE__) . 'assets/img/menu-icon.png');
            
            /**
             * Menu slug locations.
             *
             * For Dashboard: add_submenu_page('index.php',...)
             * For Posts: add_submenu_page('edit.php',...)
             * For Media: add_submenu_page('upload.php',...)
             * For Pages: add_submenu_page('edit.php?post_type=page',...)
             * For Comments: add_submenu_page('edit-comments.php',...)
             * For Custom Post Types: add_submenu_page('edit.php?post_type=your_post_type',...)
             * For Appearance: add_submenu_page('themes.php',...)
             * For Plugins: add_submenu_page('plugins.php',...)
             * For Users: add_submenu_page('users.php',...)
             * For Tools: add_submenu_page('tools.php',...)
             * For Settings: add_submenu_page('options-general.php',...)
             */
            add_submenu_page($this->pluginName, __('WebSuite Push Notifier Sub Menu', $this->pluginName), __('Logs', $this->pluginName), 'manage_options', $this->pluginName . '-sub', [
                $this,
                'wspnDisplayPluginSetupPageSub',
            ]);
        }
        
        /**
         * Add settings action link to the plugins page.
         *
         * Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
         *
         * @param $links
         *
         * @return array
         *
         * @since 1.0.0
         */
        public function wspnAddActionLinks($links): array {
            $settingsLink[] = (new websuitePushNotifierGlobal($this->pluginName, $this->pluginVersion))->getSettingsLink();
            
            return array_merge($settingsLink, $links);
        }
        
        /**
         * Render the settings page for this plugin.
         *
         * @since 1.0.0
         */
        public function wspnDisplayPluginSetupPage(): void {
            /**
             * Get setup options.
             */
            $wspnOptions = (new websuitePushNotifierGlobal($this->pluginName, $this->pluginVersion))->getPluginOptions();
            
            /**
             * Use this variable for Premium feature checks.
             */
            $checkActiveStatus = true;
            
            /**
             * Load the fields object.
             */
            $wspnFields = $this->wspnFields;
            
            /**
             * Setup pages selection and selected.
             */
            $pagesActive   = $this->wspnSetPages($wspnOptions);
            $pagesInactive = $this->wspnSetPages($wspnOptions, 'inactive');
            
            /**
             * Setup category selection and selected.
             */
            $categoriesActive   = $this->wspnSetCategories($wspnOptions);
            $categoriesInactive = $this->wspnSetCategories($wspnOptions, 'inactive');
            
            /**
             * Include the admin page
             */
            include_once 'partials/websuite-push-notifier-admin-display.php';
        }
        
        /**
         * Render sub settings page for this plugin.
         *
         * @since 1.0.0
         */
        public function wspnDisplayPluginSetupPageSub(): void {
            /**
             * Get setup options.
             */
            $wspnOptions = (new websuitePushNotifierGlobal($this->pluginName, $this->pluginVersion))->getPluginOptions();
            
            /**
             * Use this variable for Premium feature checks.
             */
            $checkActiveStatus = true;
            
            /**
             * Load the fields object.
             */
            $wspnFields = $this->wspnFields;
            
            /**
             * Include the admin page
             */
            include_once 'partials/websuite-push-notifier-admin-sub-display.php';
        }
        
        /**
         * Saves the settings for the plugin.
         *
         * @since 1.0.0
         */
        public function wspnAjaxAdmin() {
            /**
             * Do security check first.
             */
            if (wp_verify_nonce($_REQUEST['security'], $this->pluginName) === false) {
                wp_send_json_error();
                wp_die('Invalid Request!');
            }
            
            switch ($_REQUEST['data']['action']) {
                case 'save':
                    /**
                     * Parse the ajax string with data.
                     */
                    parse_str($_REQUEST['data']['content'], $outputOptions);
                    
                    if ($this->wspnUpdatePluginOptions($outputOptions)) {
                        
                        /**
                         * Get previously saved data.
                         */
                        $themeOptions = (new websuitePushNotifierGlobal($this->pluginName, $this->pluginVersion))->getPluginOptions();
                        
                        /**
                         * Set the changed option to true.
                         *
                         * If this is true, you can perform once off tasks
                         * like flushing rewrite rules.
                         */
                        update_option($this->pluginName . '-changed', ['changed' => true]);
                        
                        /**
                         * Return json response
                         */
                        wp_send_json_success(['active' => array_key_exists('active', $outputOptions)]);
                    } else {
                        wp_send_json_error();
                    }
                    break;
                case 'send':
                    $isPost       = false;
                    $errorMessage = false;
                    $message      = null;
                    
                    $location = sanitize_text_field($_REQUEST['data']['content']['location']);
                    $title    = $_REQUEST['data']['content']['title'];
                    $body     = $_REQUEST['data']['content']['body'];
                    $image    = sanitize_text_field($_REQUEST['data']['content']['image']);
                    $postID   = sanitize_text_field($_REQUEST['data']['content']['post']);
                    $topic    = sanitize_text_field($_REQUEST['data']['content']['topic']);
                    
                    // If the location is the main plugin
                    if ($location === 'main') {
                        if (isset($title) && $title !== '' && isset($body) && $body !== '' && isset($postID) && $postID !== '') {
                            // If all fields are filled in, display general error message
                            $errorMessage = 'Please ensure only one message type is filled in.';
                        } else if (isset($title) && $title === '' && isset($body) && $body === '' && isset($postID) && $postID === '') {
                            // If all fields are empty, display general error message
                            $errorMessage = 'Please enter your message, or select a post.';
                        } else if (isset($title) && $title !== '' && $body === '') {
                            // If title is entered but body is empty, display relevant error
                            $errorMessage = 'Please enter your message body.';
                        } else if (isset($body) && $body !== '' && $title === '') {
                            // If body is entered but title is empty, display relevant error
                            $errorMessage = 'Please enter your message title.';
                        } else if (isset($body) && $body !== '' && isset($title) && $title !== '') {
                            // If body and title are entered, generate message object
                            $message = (object)[
                                'title' => stripcslashes($title),
                                'body'  => substr(wp_strip_all_tags(stripcslashes($body), true), 0, 120),
                                'image' => $image,
                            ];
                        } else if (isset($postID) && $postID !== '') {
                            // If post is chosen, generate message object
                            $isPost  = true;
                            $post    = get_post($postID);
                            $message = (object)[
                                'messageTitle' => $post->post_title,
                                'messageBody'  => substr(wp_strip_all_tags($post->post_content, true), 0, 120),
                                'messageUrl'   => get_the_permalink($post),
                                'messageIcon'  => get_the_post_thumbnail_url($post, ['512', '256']) ?: '',
                                'messageId'    => $post->ID,
                            ];
                        }
                    } else if ($location === 'posts') {
                        $isPost  = true;
                        $post    = get_post($postID);
                        $message = (object)[
                            'messageTitle' => $post->post_title,
                            'messageBody'  => substr(wp_strip_all_tags($post->post_content, true), 0, 120),
                            'messageUrl'   => get_the_permalink($post),
                            'messageIcon'  => get_the_post_thumbnail_url($post, ['512', '256']) ?: '',
                            'messageId'    => $post->ID,
                        ];
                    } else if ($location === 'single_post') {
                        $isPost      = true;
                        $post        = get_post($postID);
                        $messageIcon = '';
                        if ($image === 'true' && get_the_post_thumbnail_url($post, [
                                '512',
                                '256',
                            ])) {
                            $messageIcon = get_the_post_thumbnail_url($post, [
                                '512',
                                '256',
                            ]);
                        }
                        $message = (object)[
                            'messageTitle' => isset($title) && $title ? stripcslashes($title) : $post->post_title,
                            'messageBody'  => isset($body) && $body ? stripcslashes($body) : substr(wp_strip_all_tags($post->post_content, true), 0, 120),
                            'messageUrl'   => get_the_permalink($post),
                            'messageIcon'  => $messageIcon,
                            'messageId'    => $post->ID,
                        ];
                    }
                    
                    // If provider is fcm get the topic, if not set get the first available topic
                    if ((($this->wspnFields->getOptionsReturn('notifier_provider', 'settings') ?: 'aws') === 'fcm')
                        && empty($topic)) {
                        $topics = $this->wspnFields->getOptionsReturn('notifier_fcm_available_topics', 'settings');
                        if (empty($topics) || !is_array($topics)) {
                            $errorMessage = 'No topics available';
                        } else {
                            $topic = $topics[0];
                        }
                    }
                    
                    // If there is an issue with the user's message input, display notification
                    if ($errorMessage) {
                        wp_send_json_error($errorMessage);
                    } else { // Else try to send the message to SNS
                        switch ($this->wspnFields->getOptionsReturn('notifier_provider', 'settings') ?: 'aws') {
                            case 'aws':
                                $sentSuccessfully = $this->sendMessageToAwsSns($message, $isPost);
                                if ($sentSuccessfully === true) {
                                    update_post_meta($postID, 'sentToSns', date('Y-m-d H:i:s'));
                                    wp_send_json_success();
                                } else {
                                    wp_send_json_error($sentSuccessfully);
                                }
                                break;
                            case 'fcm':
                                $message->topic   = $topic;
                                $sentSuccessfully = $this->sendMessageToFCM($message, $isPost);
                                if ($sentSuccessfully === true) {
                                    update_post_meta($postID, 'sentToFCM', date('Y-m-d H:i:s'));
                                    wp_send_json_success();
                                } else {
                                    wp_send_json_error($sentSuccessfully);
                                }
                                break;
                            default:
                                wp_send_json_error('No provider available');
                                break;
                        }
                        
                    }
                    break;
                case 'clear':
                    $value = $_REQUEST['data']['content']['value'];
                    $type  = $_REQUEST['data']['content']['type'];
                    
                    $success = $this->clearLogs($value, $type, true);
                    
                    if ($success) {
                        wp_send_json_success();
                    } else {
                        wp_send_json_error();
                    }
                    break;
                default:
                    wp_send_json_error();
                    wp_die();
            }
            
            wp_die();
        }
        
        /**
         * Update the options.
         *
         * @param $inputOption array
         *
         * @return bool
         *
         * @since 1.0.0
         */
        private function wspnUpdatePluginOptions(array $inputOption = []): bool {
            return update_option($this->pluginName, $inputOption);
        }
        
        /**
         * Return categories.
         *
         * @param $themeOptions
         * @param string $status
         *
         * @return mixed|string
         *
         * @since 1.0.0
         */
        public function wspnSetCategories($themeOptions, string $status = 'active') {
            /**
             * Setup category selection and selected.
             */
            $activeCategories = get_categories([
                'orderby' => 'name',
                'order'   => 'ASC',
                'parent'  => 0,
            ]);
            $allCategories    = [];
            foreach ($activeCategories as $category) {
                $allCategories[] = $category->term_id;
                $childCategories = get_categories([
                    'orderby' => 'name',
                    'order'   => 'ASC',
                    'parent'  => $category->term_id,
                ]);
                if (!empty($childCategories)) {
                    foreach ($childCategories as $child) {
                        $allCategories['child_' . $category->term_id] = $child->term_id;
                    }
                }
            }
            
            if (isset($themeOptions['categories']) && !empty($themeOptions['categories']['inactive'])) {
                $categoriesActive   = $themeOptions['categories']['active'];
                $categoriesInactive = $themeOptions['categories']['inactive'];
            } else {
                $categoriesActive   = implode(',', $allCategories);
                $categoriesInactive = '';
            }
            if ($status !== 'active') {
                return $categoriesInactive;
            }
            
            return $categoriesActive;
        }
        
        /**
         * Return pages.
         *
         * @param $themeOptions
         * @param string $status
         *
         * @return mixed|string
         *
         * @since 1.0.0
         */
        public function wspnSetPages($themeOptions, string $status = 'active') {
            /**
             * Setup page selection and selected.
             */
            $activePages = get_pages(['parent' => 0]);
            $allPages    = [];
            foreach ($activePages as $page) {
                $allPages[] = $page->ID;
                foreach (get_pages(['parent' => $page->ID]) as $childPage) {
                    $allPages['child_' . $page->ID] = $childPage->ID;
                }
            }
            
            if (isset($themeOptions['pages']) && !empty($themeOptions['pages']['active'])) {
                $pagesActive   = $themeOptions['pages']['active'];
                $pagesInactive = !empty($themeOptions['pages']['inactive']) ? $themeOptions['pages']['inactive'] : '';
            } elseif (empty($themeOptions['pages']['active']) && !empty($themeOptions['pages']['inactive'])) {
                $pagesActive   = !empty($themeOptions['pages']['active']) ? $themeOptions['pages']['active'] : '';
                $pagesInactive = $themeOptions['pages']['inactive'];
            } else {
                $pagesActive   = implode(',', $allPages);
                $pagesInactive = '';
            }
            
            if ($status !== 'active') {
                return $pagesInactive;
            }
            
            return $pagesActive;
        }
        
        /**
         * Create logs database table for the plugin.
         */
        public function createLogsDatabaseTable(): void {
            global $wpdb;
            
            // Check if the table exists already
            if ($wpdb->get_var("SHOW TABLES LIKE '$this->pluginLogTable'") !== $this->pluginLogTable) {
                $charsetCollate = $wpdb->get_charset_collate();
                $createTableSql = "CREATE TABLE $this->pluginLogTable (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    message varchar(500) NOT NULL,
                    post_id mediumint(9),
                    status varchar(10) NOT NULL,
                    error_message varchar(100),
                    time_submitted datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                    PRIMARY KEY  (id)
                ) $charsetCollate;";
                
                require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
                dbDelta($createTableSql);
                add_option("websuite_push_notifier_db_version", "1.0");
            }
        }
        
        /**
         * Get logs from the database table by their status type.
         *
         * @param string $type The type of logs to return.
         * @param string $return The information to return - column names or data.
         *
         * @return array|object|null
         */
        public function getLogsFromTable(string $type, string $return = 'data') {
            global $wpdb;
            
            // Return the column names as an associative array
            if ($return === 'columns') {
                $columns = [];
                foreach (($wpdb->get_col("DESC $this->pluginLogTable")) as $column) {
                    $columns[$column] = strtoupper(str_replace('_', ' ', $column));
                }
                
                return $columns;
            }
            
            // Return the data
            return $wpdb->get_results("SELECT * FROM $this->pluginLogTable WHERE status = '$type' ORDER BY ID DESC", ARRAY_A);
        }
        
        /**
         * Create the AWS SNS client using the credentials from the options page
         *
         * @return SnsClient
         */
        public function getSnsClient(): SnsClient {
            if (is_readable(dirname(__FILE__, 2) . '/libraries/aws-sdk-php/aws-autoloader.php')) {
                require dirname(__FILE__, 2) . '/libraries/aws-sdk-php/aws-autoloader.php';
            }
            
            // Check if WordPress proxy is enabled and include it in the SnsClient args
            $proxy = new WP_HTTP_Proxy();
            if ($proxy->is_enabled()) {
                $httpConfig = [
                    'proxy' => $proxy->host() . ':' . $proxy->port(),
                ];
            }
            
            return new SnsClient([
                'version'     => '2010-03-31',
                'region'      => $this->wspnFields->getOptionsReturn('notifier_aws_region', 'settings'),
                'credentials' => [
                    'key'    => $this->wspnFields->getOptionsReturn('notifier_aws_key', 'settings'),
                    'secret' => $this->wspnFields->getOptionsReturn('notifier_aws_secret', 'settings'),
                ],
                'http'        => $httpConfig ?? [],
            ]);
        }
        
        /**
         * Generate the message object and send it to the SNS service.
         *
         * Version is required as it ensures that your code will not be affected by a breaking change made to the service.
         * A list of available API versions can be found on each client's API documentation page:
         * http://docs.aws.amazon.com/aws-sdk-php/v3/api/index.html
         *
         * @param object $message The message to be sent
         * @param bool $isPost Whether the message is a post or not
         *
         * @return bool|string
         * @throws JsonException
         */
        public function sendMessageToAwsSns(object $message, bool $isPost) {
            global $wpdb;
            
            $snsClient = $this->getSnsClient();
            
            // If the user has requested to send a message.
            $messageObject = $this->createMessage($message, $isPost);
            
            try {
                $snsClient->publish([
                    'Message'          => $messageObject,
                    'MessageStructure' => 'json',
                    'TopicArn'         => $this->wspnFields->getOptionsReturn('notifier_sns_arn', 'settings'),
                ]);
                
                // Only save the log if logs are enabled
                if ((bool)$this->wspnFields->getOptionsReturn('logs_enabled', 'settings')) {
                    $wpdb->insert($this->pluginLogTable, [
                        'message'        => json_encode($message),
                        'post_id'        => $message->messageId ?? null,
                        'status'         => 'success',
                        'error_message'  => '-',
                        'time_submitted' => current_time('mysql'),
                    ]);
                    
                    // Persist the specified number of logs
                    $this->clearLogs($this->wspnFields->getOptionsReturn('max_logs', 'settings'), 'number');
                }
                
                return true;
            } catch (AwsException $e) {
                // Only save the log if logs are enabled
                if ((bool)$this->wspnFields->getOptionsReturn('logs_enabled', 'settings')) {
                    $wpdb->insert($this->pluginLogTable, [
                        'message'        => json_encode($message),
                        'post_id'        => $message->messageId ?? null,
                        'status'         => 'failed',
                        'error_message'  => $e->getAwsErrorMessage(),
                        'time_submitted' => current_time('mysql'),
                    ]);
                    
                    // Persist the specified number of logs
                    $this->clearLogs($this->wspnFields->getOptionsReturn('max_logs', 'settings'), 'number');
                }
                
                // If the message cannot be sent, display the error message to the frontend
                return $e->getAwsErrorMessage();
            }
        }
        
        /**
         * Generate the message object and send it to the SNS service.
         *
         * @param object $message The message to be sent
         * @param bool $isPost Whether the message is a post or not
         * @param array $data Additional data to be sent with the message
         * @param bool $notification Whether to send a notification or not
         *
         * @return bool|string
         * @throws JsonException
         */
        public function sendMessageToFCM(object $message, bool $isPost, array $data = [], bool $notification = true) {
            global $wpdb;
            
            require_once dirname(__FILE__, 2) . '/vendor/autoload.php';
            
            $messaging = (new Factory())->createMessaging();
            
            // If the user has requested to send a message.
            $messageObject = $this->createCloudMessage($message, $isPost, $data, $notification);
            
            try {
                $messaging->send($messageObject);
                
                // Only save the log if logs are enabled
                if ((bool)$this->wspnFields->getOptionsReturn('logs_enabled', 'settings')) {
                    $wpdb->insert($this->pluginLogTable, [
                        'message'        => json_encode($message),
                        'post_id'        => $message->messageId ?? null,
                        'status'         => 'success',
                        'error_message'  => '-',
                        'time_submitted' => current_time('mysql'),
                    ]);
                    
                    // Persist the specified number of logs
                    $this->clearLogs($this->wspnFields->getOptionsReturn('max_logs', 'settings'), 'number');
                }
                
                return true;
            } catch (MessagingException|FirebaseException $e) {
                // Only save the log if logs are enabled
                if ((bool)$this->wspnFields->getOptionsReturn('logs_enabled', 'settings')) {
                    $wpdb->insert($this->pluginLogTable, [
                        'message'        => json_encode($message, JSON_THROW_ON_ERROR),
                        'post_id'        => $message->messageId ?? null,
                        'status'         => 'failed',
                        'error_message'  => $e->getMessage(),
                        'time_submitted' => current_time('mysql'),
                    ]);
                    
                    // Persist the specified number of logs
                    $this->clearLogs($this->wspnFields->getOptionsReturn('max_logs', 'settings'), 'number');
                }
                
                return $e->getMessage();
            }
        }
        
        /**
         * Build the message return per Platform.
         *
         * @param object $message The message that needs to be built.
         * @param bool $isPost Whether the message is a post or not.
         *
         * @return string
         */
        public function createMessage(object $message, bool $isPost): string {
            if ($isPost) {
                $title = wp_strip_all_tags($message->messageTitle);
                $body  = substr(wp_strip_all_tags($message->messageBody), 0, 120);
                $url   = $message->messageUrl;
                $icon  = $message->messageIcon;
                $id    = $message->messageId;
                
                // Return the payload with messageId if the
                return '{"default": "{\"messageTitle\": \"' . $title . '\",\"messageBody\": \"' . $body . '\",\"messageUrl\": \"' . $url . '\",\"messageIcon\": \"' . $icon . '\",\"messageId\": \"' . $id . '\"}","APNS": "{\"aps\":{\"alert\": \"' . $title . '\"},\"custom\":{\"messageTitle\": \"' . $title . '\",\"messageBody\": \"' . $body . '\",\"messageUrl\": \"' . $url . '\",\"messageIcon\": \"' . $icon . '\",\"messageId\": \"' . $id . '\"}}","GCM": "{\"data\":{\"message\":\"' . $title . '\",\"custom\":{\"messageTitle\": \"' . $title . '\",\"messageBody\": \"' . $body . '\",\"messageUrl\": \"' . $url . '\",\"messageIcon\": \"' . $icon . '\",\"messageId\": \"' . $id . '\"}}}"}';
            } else {
                $title = wp_strip_all_tags($message->title);
                $body  = substr(wp_strip_all_tags($message->body), 0, 120);
                $url   = get_home_url();
                $icon  = wp_get_attachment_image_url($message->image, ['512', '256']);
                
                // Return the payload without messageId for any non-posts
                return '{"default": "{\"messageTitle\": \"' . $title . '\",\"messageBody\": \"' . $body . '\",\"messageUrl\": \"' . $url . '\",\"messageIcon\": \"' . $icon . '\"}","APNS": "{\"aps\":{\"alert\": \"' . $title . '\"},\"custom\":{\"messageTitle\": \"' . $title . '\",\"messageBody\": \"' . $body . '\",\"messageUrl\": \"' . $url . '\",\"messageIcon\": \"' . $icon . '\"}}","GCM": "{\"data\":{\"message\":\"' . $title . '\",\"custom\":{\"messageTitle\": \"' . $title . '\",\"messageBody\": \"' . $body . '\",\"messageUrl\": \"' . $url . '\",\"messageIcon\": \"' . $icon . '\"}}}"}';
            }
        }
        
        /**
         * Build the message return per Platform.
         *
         * @param object $message The message that needs to be built.
         * @param bool $isPost Whether the message is a post or not.
         * @param array $data Additional data to be sent with the message
         * @param bool $notification Whether to send a notification or not
         *
         * @return CloudMessage
         */
        public function createCloudMessage(object $message, bool $isPost, array $data = [], bool $notification = true): CloudMessage {
            // Allow the message to be filtered
            $message = apply_filters('websuite_push_notifier_fcm_message', $message, $isPost, $data, $notification);
            
            if ($isPost) {
                $payload = [
                    'topic' => $message->topic,
                    'data'  => array_merge([
                        'url' => $message->messageUrl,
                        'id'  => $message->messageId,
                    ], $data),
                ];
                
                if ($notification) {
                    $payload['notification'] = [
                        'title' => wp_strip_all_tags($message->messageTitle),
                        'body'  => substr(wp_strip_all_tags($message->messageBody), 0, 120),
                        'image' => $message->messageIcon ?: null,
                    ];
                    
                    if ($message->messageIcon) {
                        $payload['android'] = [
                            'notification' => [
                                'image' => $message->messageIcon,
                            ],
                        ];
                        $payload['apns']    = [
                            'payload'     => [
                                'aps' => [
                                    'mutable-content' => 1,
                                ],
                            ],
                            'fcm_options' => [
                                'image' => $message->messageIcon,
                            ],
                        ];
                        $payload['webpush'] = [
                            'headers' => [
                                'image' => $message->messageIcon,
                            ],
                        ];
                    }
                }
                
                // Return the payload with messageId if the
                return CloudMessage::fromArray($payload);
            }
            
            $payload = [
                'topic' => $message->topic,
                'data'  => array_merge([
                    'url' => get_home_url(),
                ], $data),
            ];
            
            if ($notification) {
                if (filter_var($message->image ?? '', FILTER_VALIDATE_URL)) {
                    $image = $message->image;
                } else {
                    $image = wp_get_attachment_image_url($message->image, ['512', '256']) ?: null;
                }
                
                $payload['notification'] = [
                    'title' => wp_strip_all_tags($message->title),
                    'body'  => substr(wp_strip_all_tags($message->body), 0, 120),
                    'image' => $image,
                ];
                if ($image) {
                    $payload['android'] = [
                        'notification' => [
                            'image' => $image,
                        ],
                    ];
                    $payload['apns']    = [
                        'payload'     => [
                            'aps' => [
                                'mutable-content' => 1,
                            ],
                        ],
                        'fcm_options' => [
                            'image' => $image,
                        ],
                    ];
                    $payload['webpush'] = [
                        'headers' => [
                            'image' => $image,
                        ],
                    ];
                }
            }
            
            // Return the payload without messageId for any non-posts
            return CloudMessage::fromArray($payload);
        }
        
        /**
         * Create the SNS button column.
         *
         * @param array $columns The list of columns on the posts list
         *
         * @return array
         */
        public function createPostSnsColumn(array $columns): array {
            return array_merge($columns, ['sendSns' => 'Send as Push Notification']);
        }
        
        /**
         * Add the content to the SNS column.
         *
         * @param string $columnKey The unique column ID
         * @param int $postID The post ID
         */
        public function createPostSnsColumnContent(string $columnKey, int $postID) {
            $sentAlready = get_post_meta($postID, 'sentToSns', true);
            
            if ($columnKey === 'sendSns') {
                if (!$sentAlready) {
                    echo "<a class='wspn_send_notification button' data-location='posts' data-post-id='$postID'>Send</a>";
                } else {
                    echo 'Already sent. To resend, use the post sidebar widget.';
                }
            }
        }
        
        /**
         * Setup Post meta box.
         *
         * @since 2.1.0
         */
        public function addCustomWidgetBoxPost() {
            add_meta_box('wspn_push_notifier_widget', __('Websuite Push Notifier', $this->pluginName), [
                $this,
                'customWidgetBoxForm',
            ], 'post', 'side');
        }
        
        /**
         * Display the box content.
         *
         * @since 2.1.0
         */
        public function customWidgetBoxForm() {
            global $post;
            $postAlreadySent = get_post_meta($post->ID, 'sentToSns', true);
            
            // Only published posts can send push notifications
            if (get_post_status($post->ID) === 'publish') { ?>
                <section class="">
                    <div class="description">
                        <p>To send a customised push notification for this post, enter the fields below.</p>
                        <p>
                            <strong>NOTE: Only insert data into the below fields if you would like to customise the message. If left empty, the title will be the post name and the message will be the trimmed excerpt.</strong>
                        </p>
                    </div>
                    <div class="">
                        <div class="widget-field">
                            <?php echo $this->wspnFields->textField('Custom Message Title', 'notifier_message_title', '', '', [
                                'placeholder' => 'Check out our latest post!',
                            ]); ?></div>
                        <div class="widget-field">
                            <?php echo $this->wspnFields->textBoxField('Custom Message', 'notifier_message_body', '', ''); ?></div>
                        <div class="widget-field">
                            <?php echo $this->wspnFields->checkboxField('Send Featured Image?', 'notifier_message_image', '', 1, [
                                'checked' => 1,
                            ]); ?></div>
                    </div>
                    <?php
                        if (($this->wspnFields->getOptionsReturn('notifier_provider', 'settings') ?: 'aws') === 'fcm') {
                            $topics = $this->wspnFields->getOptionsReturn('notifier_fcm_available_topics', 'settings');
                            
                            if (empty($topics) || !is_array($topics)) {
                                $topics = [];
                            }
                            ?> <p style="margin-top: 15px;"> <?php
                                    echo $this->wspnFields->selectField(
                                        'Topic',
                                        'notifier_fcm_topic',
                                        '',
                                        $topics,
                                        false,
                                        [
                                            'description' => '',
                                            'validate'    => true,
                                            'default'     => $topics[0],
                                            'sub'         => '',
                                            'status'      => false,
                                        ]);
                                ?> </p> <?php
                        }
                    ?>
                    <p>
                        <a class="btn components-button is-primary wspn_send_notification" data-location="single_post" data-post-id="<?php echo esc_html($post->ID); ?>"><?php echo isset($postAlreadySent) && !empty($postAlreadySent) ? 'Resend' : 'Send'; ?></a>
                    </p>
                </section>
                <?php
            }
        }
        
        /**
         * Clear the logs according to the user's specified method.
         *
         * @param int $input The number of logs to keep/the days to keep.
         * @param string $type This is either number of logs or days.
         * @param bool $return (optional) Whether to return something or not.
         *
         * @return bool|void
         *
         * @since 1.0.3
         */
        public
        function clearLogs(
            int $input, string $type, bool $return = false
        ) {
            global $wpdb;
            
            // Delete logs older than the days specified
            if ($type === 'days') {
                $response = $wpdb->query(
                    $wpdb->prepare("DELETE FROM " . $this->pluginLogTable . " WHERE time_submitted < DATE_SUB(CURDATE(),INTERVAL %d DAY)", [
                        $input,
                    ])
                );
            } else { // Else remove excess logs
                $response = $wpdb->query(
                    $wpdb->prepare("DELETE wspn FROM " . $this->pluginLogTable . " AS wspn JOIN (SELECT id FROM " . $this->pluginLogTable . " ORDER BY id DESC LIMIT 1 OFFSET %d) AS wspnl ON wspn.id < wspnl.id", [
                        ($input - 1),
                    ])
                );
            }
            
            // If the row count affected is a number, then the query was successful
            if ($return) {
                return is_numeric($response);
            }
        }
        
        /**
         * CRON job to clear the logs automatically based on the specified days.
         *
         * @since 1.0.3
         */
        public
        function wspnClearLogsCron() {
            global $wpdb;
            
            $logsEnabled     = $this->wspnFields->getOptionsReturn('logs_enabled', 'settings');
            $clearingEnabled = $this->wspnFields->getOptionsReturn('log_clearing_enabled', 'settings');
            $daysToKeep      = $this->wspnFields->getOptionsReturn('clear_days', 'settings');
            
            // Only run if logs and clearing are enabled
            if ($logsEnabled && $clearingEnabled) {
                // Delete logs older than the specified days, if days are specified
                if (!!$daysToKeep) {
                    $wpdb->query(
                        $wpdb->prepare("DELETE FROM " . $this->pluginLogTable . " WHERE time_submitted < DATE_SUB(CURDATE(),INTERVAL %d DAY)", [
                            $daysToKeep,
                        ])
                    );
                }
            }
        }
    }

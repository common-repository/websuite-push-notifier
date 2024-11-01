<?php
    
    namespace WSPN\wspn;
    
    use WSPN\admin;
    
    if (!defined('ABSPATH')) {
        exit();
    }
    
    /**
     * Fired during plugin activation.
     *
     * This class defines all code necessary to run during the plugin's activation.
     *
     * @since      1.0.0
     * @package    WebsuitePushNotifier
     * @subpackage WebsuitePushNotifier/wspn
     * @author     Johan Pretorius <johan.pretorius@afrozaar.com>
     */
    class websuitePushNotifierActivate {
        
        /**
         * Short Description. (use period)
         *
         * Long Description.
         *
         * @since 1.0.0
         */
        public static function activate($networkWide): void {
            global $wpdb;
            
            // Create the logs table
            if (is_multisite() && $networkWide) {
                // Get all blogs in the network and activate plugin on each one
                $blogIds = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogIds as $blogId) {
                    switch_to_blog($blogId);
                    (new admin\websuitePushNotifierAdmin(WSPN_PLUGIN_NAME, WSPN_PLUGIN_VERSION))->createLogsDatabaseTable();
                    restore_current_blog();
                }
            } else {
                (new admin\websuitePushNotifierAdmin(WSPN_PLUGIN_NAME, WSPN_PLUGIN_VERSION))->createLogsDatabaseTable();
            }
            
            // Add the CRON to run weekly
            if (!wp_next_scheduled('wspnCustomCronHook')) {
                wp_schedule_event(time(), 'weekly', 'wspnCustomCronHook');
            }
        }
    }

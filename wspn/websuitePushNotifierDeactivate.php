<?php
    
    namespace WSPN\wspn;
    
    if (!defined('ABSPATH')) {
        exit();
    }
    
    /**
     * Fired during plugin deactivation.
     *
     * This class defines all code necessary to run during the plugin's deactivation.
     *
     * @since      1.0.0
     * @package    WebsuitePushNotifier
     * @subpackage WebsuitePushNotifier/wspn
     * @author     Johan Pretorius <johan.pretorius@afrozaar.com>
     */
    class websuitePushNotifierDeactivate {
        
        /**
         * Short Description. (use period)
         *
         * Long Description.
         *
         * @since 1.0.0
         */
        public static function deactivate() {
            // Cancel the CRON job
            $timestamp = wp_next_scheduled('wspnCustomCronHook');
            wp_unschedule_event($timestamp, 'wspnCustomCronHook');
        }
        
    }

<?php
    
    /**
     * Fired when the plugin is uninstalled.
     *
     * When populating this file, consider the following flow of control:
     *
     * - This method should be static
     * - Check if the $_REQUEST content actually is the plugin name
     * - Run an admin referrer check to make sure it goes through authentication
     * - Verify the output of $_GET makes sense
     * - Repeat with other user roles. Best directly by using the links/query string parameters.
     * - Repeat things for multisite. Once for a single site in the network, once site wide.
     *
     *
     * @since      1.0.0
     * @package    WebsuitePushNotifier
     * @subpackage WebsuitePushNotifier/wspn
     * @author     Johan Pretorius <johan.pretorius@afrozaar.com>
     */
    
    // If uninstall not called from WordPress, then exit.
    if (!defined('WP_UNINSTALL_PLUGIN')) {
        exit;
    }
    
    /**
     * Delete the leftover options from WordPress.
     */
    if (is_multisite()) {
        foreach (get_sites() as $sites) {
            /**
             * Delete all options for multisite.
             */
            delete_blog_option($sites->blog_id, WSPN_PLUGIN_NAME);
        }
    } else {
        /**
         * Delete all options.
         */
        delete_option(WSPN_PLUGIN_NAME);
    }

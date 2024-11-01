<?php
    
    namespace WSPN\wspn;
    
    if (!defined('ABSPATH')) {
        exit();
    }
    
    /**
     * Global usage class.
     *
     * This class is used to load functions that are global (Admin and Frontend) used.
     *
     * @since      1.0.0RF
     * @package    WebsuitePushNotifier
     * @subpackage WebsuitePushNotifier/wspn
     * @author     Johan Pretorius <johan.pretorius@afrozaar.com>
     */
    class websuitePushNotifierGlobal {
        
        /**
         * The ID of this plugin.
         *
         * @since 1.0.0
         * @access private
         * @var string $pluginName The ID of this plugin.
         */
        private $pluginName;
        
        /**
         * The version of this plugin.
         *
         * @since 1.0.0
         * @access private
         * @var string $pluginVersion The current version of this plugin.
         */
        private $pluginVersion;
        
        /**
         * Initialize the collections used to maintain backend and frontend functions.
         *
         * @param string $pluginName The name of this plugin.
         * @param string $pluginVersion The version of this plugin.
         *
         * @since 1.0.0
         */
        public function __construct($pluginName, $pluginVersion) {
            $this->pluginName    = $pluginName;
            $this->pluginVersion = $pluginVersion;
        }
        
        /**
         * Return the plugin options.
         *
         * @since     1.0.0
         */
        public function getPluginOptions() {
            return get_option($this->pluginName, []);
        }
        
        /**
         * The settings page link for reuse.
         *
         * @return string
         *
         * @since 1.0.0
         */
        public function getSettingsLink() {
            return '<a href="' . admin_url('admin.php?page=' . $this->pluginName) . '">' . __('Settings', $this->pluginName) . '</a>';
        }
        
        /**
         * The name of the plugin used to uniquely identify it within the context of
         * WordPress and to define internationalization functionality.
         *
         * @return    string    The name of the plugin.
         * @since     1.0.0
         */
        public function getPluginName(): string {
            return $this->pluginName;
        }
        
        /**
         * Retrieve the version number of the plugin.
         *
         * @return    string    The version number of the plugin.
         * @since     1.0.0
         */
        public function getPluginVersion(): string {
            return $this->pluginVersion;
        }
    }

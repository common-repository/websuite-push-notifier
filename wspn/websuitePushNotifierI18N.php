<?php
    
    namespace WSPN\wspn;
    
    if (!defined('ABSPATH')) {
        exit();
    }
    
    /**
     * Define the internationalization functionality.
     *
     * Loads and defines the internationalization files for this plugin
     * so that it is ready for translation.
     *
     * @since      1.0.0
     * @package    WebsuitePushNotifier
     * @subpackage WebsuitePushNotifier/wspn
     * @author     Johan Pretorius <johan.pretorius@afrozaar.com>
     */
    class websuitePushNotifierI18N {
        
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
         * Initialize the options.
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
         * Load the plugin text domain for translation.
         *
         * @since 1.0.0
         */
        public function loadPluginTextDomain(): void {
            load_plugin_textdomain($this->pluginName, false, dirname(plugin_basename(__FILE__), 2) . '/languages/');
        }
    }

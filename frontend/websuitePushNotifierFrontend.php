<?php
    
    namespace WSPN\frontend;
    
    if (!defined('ABSPATH')) {
        exit();
    }
    
    /**
     * The public-facing functionality of the plugin.
     *
     * Defines the plugin name, version, and two examples hooks for how to
     * enqueue the public-facing stylesheet and JavaScript.
     *
     *
     * @package    WebsuitePushNotifier
     * @subpackage WebsuitePushNotifier/frontend
     * @author     Johan Pretorius <johan.pretorius@afrozaar.com>
     */
    class websuitePushNotifierFrontend {
        
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
         * Initialize the class and set its properties.
         *
         * @param string $pluginName The name of the plugin.
         * @param string $pluginVersion The version of this plugin.
         *
         * @since 1.0.0
         */
        public function __construct($pluginName, $pluginVersion) {
            $this->pluginName    = $pluginName;
            $this->pluginVersion = $pluginVersion;
            
            $this->registerCallbacks();
        }
        
        /**
         * Register the stylesheets for the public-facing side of the site.
         *
         * @since 1.0.0
         */
        public function enqueueScripts(): void {
            wp_enqueue_style($this->pluginName, plugin_dir_url(__FILE__) . 'css/websuite-push-notifier-frontend-style.min.css', [], $this->pluginVersion, 'all');
            wp_enqueue_script($this->pluginName, plugin_dir_url(__FILE__) . 'js/websuite-push-notifier-frontend-script.min.js', ['jquery'], $this->pluginVersion, false);
        }
        
        /**
         * Register the custom hooks and actions for the public-facing side of the site.
         *
         * @since 1.0.0
         */
        public function registerCallbacks(): void {
        }
    }

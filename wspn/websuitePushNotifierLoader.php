<?php
    
    namespace WSPN\wspn;
    
    if (!defined('ABSPATH')) {
        exit();
    }
    
    /**
     * Register all actions and filters for the plugin.
     *
     * Maintain a list of all hooks that are registered throughout
     * the plugin, and register them with the WordPress API. Call the
     * run function to execute the list of actions and filters.
     *
     * @since      1.0.0
     * @package    WebsuitePushNotifier
     * @subpackage WebsuitePushNotifier/wspn
     * @author     Johan Pretorius <johan.pretorius@afrozaar.com>
     */
    class websuitePushNotifierLoader {
        
        /**
         * The array of actions registered with WordPress.
         *
         * @since 1.0.0
         * @access protected
         * @var array $actions The actions registered with WordPress to fire when the plugin loads.
         */
        protected $actions;
        
        /**
         * The array of filters registered with WordPress.
         *
         * @since 1.0.0
         * @access protected
         * @var array $filters The filters registered with WordPress to fire when the plugin loads.
         */
        protected $filters;
        
        /**
         * The array of shortcodes registered with WordPress.
         *
         * @since 1.0.0
         * @access protected
         * @var array $shortcodes The shortcodes registered with WordPress to fire when the plugin loads.
         */
        protected $shortcodes;
        
        /**
         * The array of Actions to remove from WordPress.
         *
         * @since 1.0.0
         * @access protected
         * @var array $removeActions The Actions to remove from WordPress when loaded.
         */
        protected $removeActions;
        
        /**
         * The array of Filters to remove from WordPress.
         *
         * @since 1.0.0
         * @access protected
         * @var array $removeFilters he Filters to remove from WordPress when loaded.
         */
        protected $removeFilters;
        
        /**
         * Initialize the collections used to maintain the actions and filters.
         *
         * @since 1.0.0
         */
        public function __construct() {
            $this->actions       = [];
            $this->filters       = [];
            $this->shortcodes    = [];
            $this->removeActions = [];
            $this->removeFilters = [];
        }
        
        /**
         * Add a new action to the collection to be registered with WordPress.
         *
         * @param string $hook The name of the WordPress action that is being registered.
         * @param object $component A reference to the instance of the object on which the action is defined.
         * @param string $callback The name of the function definition on the $component.
         * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
         * @param int $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
         *
         * @since 1.0.0
         */
        public function addAction($hook, $component, $callback, $priority = 10, $accepted_args = 1): void {
            $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
        }
        
        /**
         * Add a new filter to the collection to be registered with WordPress.
         *
         * @param string $hook The name of the WordPress filter that is being registered.
         * @param object $component A reference to the instance of the object on which the filter is defined.
         * @param string $callback The name of the function definition on the $component.
         * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
         * @param int $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1
         *
         * @since 1.0.0
         */
        public function addFilter($hook, $component, $callback, $priority = 10, $accepted_args = 1): void {
            $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
        }
        
        /**
         * Add a new shortcode to the collection to be registered with WordPress.
         *
         * @param string $hook The name of the WordPress filter that is being registered.
         * @param object $component A reference to the instance of the object on which the filter is defined.
         * @param string $callback The name of the function definition on the $component.
         *
         * @since 1.0.0
         */
        public function addShortcode($hook, $component, $callback): void {
            $this->shortcodes = $this->add($this->shortcodes, $hook, $component, $callback, $priority = 10, $accepted_args = 1);
        }
        
        /**
         * Remove any Action registered within WordPress.
         *
         * @param string $hook The name of the WordPress filter that is being registered.
         * @param object $component A reference to the instance of the object on which the filter is defined.
         * @param string $callback The name of the function definition on the $component.
         *
         * @since 1.0.0
         */
        public function removeAction($hook, $component, $callback): void {
            $this->shortcodes = $this->add($this->removeActions, $hook, $component, $callback, $priority = 10, $accepted_args = 1);
        }
        
        /**
         * Remove any Filter registered within WordPress.
         *
         * @param string $hook The name of the WordPress filter that is being registered.
         * @param object $component A reference to the instance of the object on which the filter is defined.
         * @param string $callback The name of the function definition on the $component.
         *
         * @since 1.0.0
         */
        public function removeFilter($hook, $component, $callback): void {
            $this->shortcodes = $this->add($this->removeFilters, $hook, $component, $callback, $priority = 10, $accepted_args = 1);
        }
        
        /**
         * A utility function that is used to register the actions and hooks into a single
         * collection.
         *
         * @param array $hooks The collection of hooks that is being registered (that is, actions or filters).
         * @param string $hook The name of the WordPress filter that is being registered.
         * @param object $component A reference to the instance of the object on which the filter is defined.
         * @param string $callback The name of the function definition on the $component.
         * @param int $priority The priority at which the function should be fired.
         * @param int $accepted_args The number of arguments that should be passed to the $callback.
         *
         * @return   array The collection of actions and filters registered with WordPress.
         * @since 1.0.0
         * @access private
         */
        private function add($hooks, $hook, $component, $callback, $priority, $accepted_args) {
            $hooks[] = [
                'hook'          => $hook,
                'component'     => $component,
                'callback'      => $callback,
                'priority'      => $priority,
                'accepted_args' => $accepted_args,
            ];
            
            return $hooks;
        }
        
        /**
         * Register the filters and actions with WordPress.
         *
         * @since 1.0.0
         */
        public function run() {
            
            foreach ($this->filters as $hook) {
                add_filter($hook['hook'], [
                    $hook['component'],
                    $hook['callback'],
                ], $hook['priority'], $hook['accepted_args']);
            }
            
            foreach ($this->actions as $hook) {
                add_action($hook['hook'], [
                    $hook['component'],
                    $hook['callback'],
                ], $hook['priority'], $hook['accepted_args']);
            }
            
            foreach ($this->shortcodes as $hook) {
                add_shortcode($hook['hook'], [$hook['component'], $hook['callback']]);
            }
            
            foreach ($this->removeActions as $hook) {
                remove_action($hook['hook'], $hook['callback'], $hook['priority']);
            }
            
            foreach ($this->removeFilters as $hook) {
                remove_filter($hook['hook'], $hook['callback'], $hook['priority']);
            }
        }
    }

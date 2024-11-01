<?php
    
    namespace WSPN\wspn;
    
    use WSPN\admin\websuitePushNotifierAdmin;
    use WSPN\frontend\websuitePushNotifierFrontend;
    
    if (!defined('ABSPATH')) {
        exit();
    }
    
    /**
     * The core plugin class.
     *
     * This is used to define internationalization, admin-specific hooks, and
     * public-facing site hooks.
     *
     * Also maintains the unique identifier of this plugin as well as the current
     * version of the plugin.
     *
     * @since      1.0.0
     * @package    WebSuitePushNotifier
     * @subpackage WebSuitePushNotifier/wspn
     * @author     Johan Pretorius <johan.pretorius@afrozaar.com>
     */
    class websuitePushNotifierCore {
        
        /**
         * The loader that's responsible for maintaining and registering all hooks that power
         * the plugin.
         *
         * @since 1.0.0
         * @access protected
         * @var websuitePushNotifierLoader $loader Maintains and registers all hooks for the plugin.
         */
        protected $loader;
        
        /**
         * The unique identifier of this plugin.
         *
         * @since 1.0.0
         * @access protected
         * @var string $pluginName The string used to uniquely identify this plugin.
         */
        protected $pluginName;
        
        /**
         * The current version of the plugin.
         *
         * @since 1.0.0
         * @access protected
         * @var string $pluginVersion The current version of the plugin.
         */
        protected $pluginVersion;
        
        /**
         * Define the core functionality of the plugin.
         *
         * Set the plugin name and the plugin version that can be used throughout the plugin.
         * Load the dependencies, define the locale, and set the hooks for the admin area and
         * the public-facing side of the site.
         *
         * @since 1.0.0
         */
        public function __construct() {
            $this->pluginVersion = WSPN_PLUGIN_VERSION;
            $this->pluginName    = WSPN_PLUGIN_NAME;
            
            $this->loadDependencies();
            $this->defineGlobalHooks();
            $this->setLocale();
            if (is_admin()) {
                $this->defineAdminHooks();
            }
            //$this->defineFrontendHooks();
        }
        
        /**
         * Load the required dependencies for this plugin.
         *
         * Include the following files that make up the plugin:
         *
         * - websuitePushNotifierLoader. Orchestrates the hooks of the plugin.
         * - websuitePushNotifierI18N. Defines internationalization functionality.
         * - websuitePushNotifierAdmin. Defines all hooks for the admin area.
         * - websuitePushNotifierFrontend. Defines all hooks for the public side of the site.
         *
         * Create an instance of the loader which will be used to register the hooks
         * with WordPress.
         *
         * @since 1.0.0
         * @access private
         */
        private function loadDependencies(): void {
            $this->loader = new websuitePushNotifierLoader();
        }
        
        /**
         * Define the locale for this plugin for internationalization.
         *
         * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
         * with WordPress.
         *
         * @since 1.0.0
         * @access private
         */
        private function setLocale(): void {
            /**
             * Translate Methods @websuitePushNotifierI18N
             */
            $pluginI18n = new websuitePushNotifierI18N($this->pluginName, $this->pluginVersion);
            $this->loader->addAction('plugins_loaded', $pluginI18n, 'loadPluginTextDomain');
        }
        
        /**
         * Define the locale for this plugin for internationalization.
         *
         * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
         * with WordPress.
         *
         * @since 1.0.0
         * @access private
         */
        private function defineGlobalHooks(): void {
            /**
             * Global Methods @websuitePushNotifierGlobal
             */
            $pluginGlobal = new websuitePushNotifierGlobal($this->pluginName, $this->pluginVersion);
        }
        
        /**
         * Register all the hooks related to the admin area functionality
         * of the plugin.
         *
         * @since 1.0.0
         * @access private
         */
        private function defineAdminHooks(): void {
            /**
             * Admin Methods @websuitePushNotifierAdmin
             */
            $pluginAdmin = new websuitePushNotifierAdmin($this->pluginName, $this->pluginVersion);
            
            /**
             * Add a custom CRON job for clearing out the logs.
             */
            $this->loader->addAction('wspnCustomCronHook', $pluginAdmin, 'wspnClearLogsCron');
            
            /**
             * Scripts to load on plugin init.
             */
            $this->loader->addAction('admin_enqueue_scripts', $pluginAdmin, 'enqueueScripts');
            
            /**
             * Add admin menu.
             */
            $this->loader->addAction('admin_menu', $pluginAdmin, 'wspnAddPluginAdminMenu');
            
            /**
             * Add action link.
             */
            $pluginBasename = plugin_basename(plugin_dir_path(__DIR__) . $this->pluginName . '.php');
            $this->loader->addFilter('plugin_action_links_' . $pluginBasename, $pluginAdmin, 'wspnAddActionLinks');
            
            /**
             * Global WSPN plugin ajax call.
             */
            $this->loader->addAction('wp_ajax_wspn_admin', $pluginAdmin, 'wspnAjaxAdmin');
            
            // Check that config is valid. If not show an admin notice.
            if (!$this->validateProviders()) {
                return;
            }
            
            
            /**
             * Add SNS column to posts.
             */
            $this->loader->addFilter('manage_post_posts_columns', $pluginAdmin, 'createPostSnsColumn');
            $this->loader->addAction('manage_post_posts_custom_column', $pluginAdmin, 'createPostSnsColumnContent', 10, 2);
            
            /**
             * Customised post widget.
             */
            $this->loader->addAction('admin_init', $pluginAdmin, 'addCustomWidgetBoxPost');
        }
        
        /**
         * Validates configuration of providers.
         * @return bool
         * @access private
         */
        private function validateProviders(): bool {
            $options = (new websuitePushNotifierFields($this->pluginName, $this->pluginVersion))->optionsReturn;
            
            switch ($options['settings']['notifier_provider'] ?? 'aws') {
                case 'aws':
                    if (empty($options['settings']['notifier_aws_key']) ||
                        empty($options['settings']['notifier_aws_secret']) ||
                        empty($options['settings']['notifier_aws_region']) ||
                        empty($options['settings']['notifier_sns_arn'])) {
                        add_action('admin_notices', function() use ($options) { ?>
                            <div class="notice notice-error is-dismissible">
                                <p>
                                    <strong>WebSuite Push Notifier</strong>: AWS SNS is not configured correctly.
                                    Please check the
                                    <a href="<?php echo admin_url('options-general.php?page=websuite-push-notifier'); ?>">settings page</a>.
                                </p>
                                <ul>
                                    <?php if (empty($options['settings']['notifier_aws_key'])) { ?>
                                        <li>AWS Key is required.</li>
                                    <?php } ?>
                                    <?php if (empty($options['settings']['notifier_aws_secret'])) { ?>
                                        <li>AWS Secret is required.</li>
                                    <?php } ?>
                                    <?php if (empty($options['settings']['notifier_aws_region'])) { ?>
                                        <li>AWS Region is required.</li>
                                    <?php } ?>
                                    <?php if (empty($options['settings']['notifier_sns_arn'])) { ?>
                                        <li>SNS ARN is required.</li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php });
                        
                        return false;
                    }
                    break;
                case 'fcm':
                    if (!function_exists('curl_version')) {
                        add_action('admin_notices', function() { ?>
                            <div class="notice notice-error is-dismissible">
                                <p>
                                    <strong>WebSuite Push Notifier</strong>: Firebase Cloud Messaging requires
                                    the curl extension to be installed and enabled in order to function.
                                </p>
                            </div>
                        <?php });
                        
                        return false;
                    }
                    if (empty($options['settings']['notifier_fcm_credentials_path']) ||
                        empty($options['settings']['notifier_fcm_available_topics'])) {
                        add_action('admin_notices', function() use ($options) { ?>
                            <div class="notice notice-error is-dismissible">
                                <p>
                                    <strong>WebSuite Push Notifier</strong>:
                                    Firebase Cloud Messaging is not configured correctly. Please check the
                                    <a href="<?php echo admin_url('options-general.php?page=websuite-push-notifier'); ?>">settings page</a>..
                                </p>
                                <ul>
                                    <?php if (empty($options['settings']['notifier_fcm_credentials_path'])) { ?>
                                        <li>Path to Credentials File is required.</li>
                                    <?php } elseif (!file_exists($options['settings']['notifier_fcm_credentials_path'])) { ?>
                                        <li>Credentials File does not exist.</li>
                                    <?php } ?>
                                    <?php if (empty($options['settings']['notifier_fcm_available_topics'])) { ?>
                                        <li>At least one topic is required.</li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php });
                        
                        return false;
                    }
                    
                    // Set the credentials file path.
                    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $options['settings']['notifier_fcm_credentials_path']);
                    break;
                default:
                    return false;
            }
            
            return true;
        }
        
        /**
         * Register all the hooks related to the public-facing
         * functionality of the plugin.
         *
         * @since 1.0.0
         * @access private
         */
        private function defineFrontendHooks(): void {
            /**
             * Frontend Methods @websuitePushNotifierFrontend
             */
            $pluginPublic = new websuitePushNotifierFrontend($this->pluginName, $this->pluginVersion);
            
            /**
             * load scripts.
             */
            $this->loader->addAction('wp_enqueue_scripts', $pluginPublic, 'enqueueScripts');
            
            /**
             * Add ajax for frontend here, since the loader is used for functions.
             */
            $this->loader->addAction('wp_ajax_wspn_frontend', $pluginPublic, 'wspnAjaxFrontend');
            $this->loader->addAction('wp_ajax_nopriv_wspn_frontend', $pluginPublic, 'wspnAjaxFrontend');
        }
        
        /**
         * Run the loader to execute all the hooks with WordPress.
         *
         * @since 1.0.0
         */
        public function run(): void {
            $this->loader->run();
        }
        
        /**
         * The reference to the class that orchestrates the hooks with the plugin.
         *
         * @return    websuitePushNotifierLoader    Orchestrates the hooks of the plugin.
         * @since     1.0.0
         */
        public function getLoader(): websuitePushNotifierLoader {
            return $this->loader;
        }
        
    }

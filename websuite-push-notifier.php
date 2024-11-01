<?php
    
    /**
     * WebSuite Push Notifier
     *
     * @link              https://www.publisherstoolbox.com/websuite/
     * @since             1.0.0
     * @package           WebSuitePushNotifier
     *
     * @wordpress-plugin
     * Plugin Name:       WebSuite Push Notifier
     * Description:       Send push notifications with custom messaging when a post is published.
     * Version:           1.1.7
     * Author:            Publisher's Toolbox
     * Author URI:        https://www.publisherstoolbox.com/websuite/
     * License:           GPL-2.0+
     * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
     * Text Domain:       websuite-push-notifier
     * Domain Path:       /languages
     */
    
    use WSPN\wspn\websuitePushNotifierActivate;
    use WSPN\wspn\websuitePushNotifierCore;
    use WSPN\wspn\websuitePushNotifierDeactivate;
    
    /**
     * If this file is called directly, abort.
     */
    if (!defined('WPINC')) {
        die;
    }
    
    /**
     * Plugin version.
     * Start at version 1.0.0 and use SemVer - https://semver.org
     * Rename this for your plugin and update it as you release new versions.
     */
    define('WSPN_PLUGIN_VERSION', '1.1.7');
    
    /**
     * Plugin name.
     * Rename this for your plugin.
     */
    define('WSPN_PLUGIN_NAME', 'websuite-push-notifier');
    
    /**
     * SPL autoloader for Publishers Toolbox
     *
     * This function loads all classes for
     * the plugin automatically.
     *
     * @param string $className
     */
    function WspnAutoLoader(string $className) {
        /**
         * If the class being requested does not start with our prefix,
         * we know it's not one in our project
         */
        if (0 !== strpos($className, 'WSPN')) {
            return;
        }
        
        $className = ltrim($className, '\\');
        $fileName  = '';
        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = plugin_dir_path(__FILE__) . '/' . substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            $fileName  = str_replace('//WSPN', '', $fileName);
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        
        if (file_exists($fileName)) {
            require $fileName;
        }
    }
    
    try {
        spl_autoload_register('WspnAutoLoader');
    } catch (Exception $e) {
        throw new InvalidArgumentException('Could not register WspnAutoLoader.');
    }
    
    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-plugin-name-activator.php
     */
    function wspnActivatePlugin($networkWide) {
        websuitePushNotifierActivate::activate($networkWide);
    }
    
    register_activation_hook(__FILE__, 'wspnActivatePlugin');
    
    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-plugin-name-deactivator.php
     */
    function wspnDeactivatePlugin() {
        websuitePushNotifierDeactivate::deactivate();
    }
    
    register_deactivation_hook(__FILE__, 'wspnDeactivatePlugin');
    
    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since 1.0.0
     */
    function wspnRunPlugin() {
        (new websuitePushNotifierCore())->run();
    }
    
    wspnRunPlugin();

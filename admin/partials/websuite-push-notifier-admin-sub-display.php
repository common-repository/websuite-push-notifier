<?php
    /**
     * Provide a admin area view for the plugin
     *
     * This file is used to mark up the admin-facing aspects of the plugin.
     *
     * @since      1.0.0
     *
     * @package    WebsuitePushNotifier/
     * @subpackage WebsuitePushNotifier/admin/partials
     */
?>
<hr class="wp-header-end">
<section class="<?php echo esc_html($this->pluginName); ?>">
    <div class="header">
        <div class="grid">
            <div class="col-1-2">
                <img src="<?php echo plugin_dir_url(__DIR__) . 'assets/img/banner.png'; ?>" height="100" alt="<?php echo esc_html(get_admin_page_title()); ?>" class="logo">
            </div>
            <div class="col-1-2">
                <div class="is-right">
                    <p class="is-text-right">
                        Version: <?php echo esc_html($this->pluginVersion); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="wrap">
        <section class="start-page">
            <div class="grid">
                <div class="col-2-12">
                    <div class="col-1-1 menu-block">
                        <ul>
                            <?php require_once 'settings-sub/websuite-push-notifier-inc-menu-settings.php' ?>
                        </ul>
                    </div>
                </div>
                <div class="col-10-12">
                    <div class="grid">
                        <div class="col-1-1 content-block">
                            <?php require_once 'settings-sub/websuite-push-notifier-inc-tabs-settings.php' ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</section>

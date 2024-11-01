<?php
    /**
     * Provide an admin area view for the plugin
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
                            <?php require_once 'settings/websuite-push-notifier-inc-menu-settings.php' ?>
                        </ul>
                    </div>
                </div>
                <div class="col-10-12">
                    <div class="grid">
                        <div class="col-1-1 content-block">
                            <form class="plugin-options-form" id="plugin-options-form">
                                <input type="hidden" name="version" value="<?php echo esc_html($this->pluginVersion); ?>">
                                <button class="btn is-secondary is-right options-admin-save" id="options-admin-save" form="plugin-options-form" type="submit"><?php _e('Save All', $this->pluginName); ?></button>
                                <?php require_once 'settings/websuite-push-notifier-inc-tabs-settings.php' ?>
                            </form>
                            <div class="grid">
                                <div class="col-1-1">
                                    <button class="btn is-secondary is-right options-admin-save" id="options-admin-save" form="plugin-options-form" type="submit"><?php _e('Save All', $this->pluginName); ?></button>
                                </div>
                            </div>
                            <!-- Send Message Tab (Exclude from admin form) -->
                            <div id="settings-tab-2" class="tab-content">
                                <h3>Send Push Notification</h3>
                                <p>
                                    <strong>IMPORTANT: Please ensure only one of the below is entered. If you want to send a post, ensure the 'Send General Notification' fields are empty. If you want to send a general notification, ensure no post is selected.</strong>
                                </p>
                                <div class="card">
                                    <div class="card-header">
                                        Send General Notification
                                    </div>
                                    <div class="card-body">
                                        <div class="grid">
                                            <!-- Message Title -->
                                            <div class="form-item col-1-1 single">
                                                <?php echo $wspnFields->textField('Message Title:', 'notifier_message_title', 'settings', '', [
                                                    'description' => 'The title for your push notification.',
                                                    'placeholder' => '',
                                                    'validate'    => false,
                                                    'type'        => 'text',
                                                ]); ?>
                                            </div>
                                            <!-- Message Body -->
                                            <div class="form-item col-1-1 single">
                                                <?php echo $wspnFields->textBoxField('Message Body', 'notifier_message_body', 'settings', '', [
                                                    'description' => 'The message body for your push notification.',
                                                    'placeholder' => 'Message goes here',
                                                    'validate'    => false,
                                                    'maxlength'   => 120,
                                                ]); ?>
                                            </div>
                                            <div class="form-item col-1-1 single">
                                                <?php echo $wspnFields->imageUploadField('Message Thumbnail', 'notifier_message_image', 'settings', [
                                                    'description' => 'The image to be attached to the message.',
                                                ]); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        Send Article Notification
                                    </div>
                                    <div class="card-body">
                                        <div class="grid">
                                            <div class="form-item col-1-2 single">
                                                <?php $articles = get_posts([
                                                    'post_type'      => 'post',
                                                    'posts_per_page' => 20,
                                                    'post_status'    => 'publish',
                                                    'order'          => 'publish_date',
                                                    'orderby'        => 'DESC',
                                                ]); ?>
                                                <?php echo $wspnFields->selectField(
                                                    'Post to Send', 'notifier_post', 'settings', wp_list_pluck($articles, 'post_title', 'ID'), true,
                                                    [
                                                        'description' => 'Select the post you wish to send a notification for.',
                                                        'placeholder' => '- No Post -',
                                                        'validate'    => false,
                                                        'default'     => '',
                                                        'sub'         => '',
                                                        'status'      => false,
                                                    ]); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        Firebase Cloud Messaging Options
                                    </div>
                                    <div class="card-body">
                                        <div class="grid">
                                            <div class="form-item col-1-2 single">
                                                <?php
                                                    $topics = $wspnFields->getOptionsReturn(
                                                        'notifier_fcm_available_topics',
                                                        'settings'
                                                    );
                                                    
                                                    if (empty($topics) || !is_array($topics)) {
                                                        $topics = [];
                                                    }
                                                    
                                                    echo $wspnFields->selectField(
                                                        'Topic',
                                                        'notifier_fcm_topic',
                                                        '',
                                                        $topics,
                                                        false,
                                                        [
                                                            'description' => 'Select the topic to send to.',
                                                            'validate'    => true,
                                                            'default'     => $topics[0],
                                                            'sub'         => '',
                                                            'status'      => false,
                                                        ]);
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="has-margin-top">
                                    <a class="wspn_send_notification btn is-secondary is-right" data-location="main" href="#">Send</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</section>

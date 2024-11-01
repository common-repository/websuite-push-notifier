<div id="settings-tab-1" class="tab-content active">
    <h3>General</h3>
    <div class="card">
        <div class="card-header">
            Activate Plugin
        </div>
        <div class="card-body">
            <div class="grid">
                <div class="form-item col-12"><?php echo $wspnFields->checkboxField('Switch on Plugin', 'active', '', 1, [
                        'description' => 'Switch the Plugin on or off temporarily.',
                        'eventClass'  => 'display-next',
                    ]); ?>
                </div>
            </div>
            <div class="grid">
                <div class="form-item col-12 single">
                    <label>Notification Provider</label>
                    <p>
                        <?php echo $wspnFields->radioBoxField(
                            'AWS SNS (Default)',
                            'notifier_provider',
                            'settings',
                            'aws'
                        ); ?>
                    </p>
                    <?php echo $wspnFields->radioBoxField(
                        'Firebase Cloud Messaging',
                        'notifier_provider',
                        'settings',
                        'fcm'
                    ); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            AWS SNS Configuration
        </div>
        <div class="card-body">
            <div class="grid">
                <div class="form-item col-1-2">
                    <?php echo $wspnFields->textField('AWS Key', 'notifier_aws_key', 'settings', '', [
                        'description' => 'The AWS access key ID.',
                        'type'        => 'text',
                        'validate'    => false,
                    ]); ?>
                </div>
                <div class="form-item col-1-2">
                    <?php echo $wspnFields->textField('AWS Secret', 'notifier_aws_secret', 'settings', '', [
                        'description' => 'The AWS secret access key.',
                        'type'        => 'password',
                        'validate'    => false,
                    ]); ?>
                </div>
                <div class="form-item col-1-2 single">
                    <?php echo $wspnFields->textField('AWS Region', 'notifier_aws_region', 'settings', '', [
                        'description' => 'The AWS region.',
                        'placeholder' => 'eu-west-1',
                        'type'        => 'text',
                        'validate'    => false,
                    ]); ?>
                </div>
                <div class="form-item col-1-2">
                    <?php echo $wspnFields->textField('SNS ARN', 'notifier_sns_arn', 'settings', '', [
                        'description' => 'The Amazon Resource Name (ARN) and Topic being published to.',
                        'placeholder' => 'arn:aws:sns:eu-west-1:123:example-category-123456',
                        'type'        => 'text',
                        'validate'    => false,
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            Firebase Cloud Messaging Configuration
        </div>
        <div class="card-body">
            <div class="grid">
                <div class="form-item col-12">
                    <?php echo $wspnFields->textField(
                        'Path to Credentials File',
                        'notifier_fcm_credentials_path',
                        'settings',
                        '',
                        [
                            'description' => 'You need to upload your credentials file to the server and enter the path to it here. To get a credentials file, follow the instructions <a href="https://firebase.google.com/docs/cloud-messaging/auth-server#provide-credentials-manually" target="_blank">here</a>.',
                            'type'        => 'text',
                            'validate'    => false,
                        ]
                    ); ?>
                </div>
                <div class="form-item col-12">
                    <?php echo $wspnFields->listField('Available Topics', 'notifier_fcm_available_topics', 'settings', [
                        'description' => 'The available topics to send notifications to. The first topic will be the default topic.',
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="settings-tab-3" class="tab-content active">
    <h3>Log Settings</h3>
    <div class="card">
        <div class="card-header">
            Enable Logs
        </div>
        <div class="card-body">
            <div class="grid">
                <div class="form-item col-1-2">
                    <?php echo $wspnFields->checkboxField('Enable logging', 'logs_enabled', 'settings', 1, [
                        'description' => 'Turn the logs for the plugin on or off. These are useful if you are having issues with the plugin.',
                        'default'     => 1,
                        'eventClass'  => 'display-next',
                    ]); ?>
                </div>
                <div class="hidden-wspn">
                    <div class="form-item col-1-2">
                        <?php echo $wspnFields->checkboxField('Enable automatic log clearing', 'log_clearing_enabled', 'settings', 1, [
                            'description' => 'Turn log clearing for the plugin on or off.',
                            'default'     => 1,
                            'eventClass'  => 'display-next',
                        ]); ?>
                    </div>
                    <div class="hidden-wspn">
                        <p>
                            <em>NOTE: If both the below are selected, the number of logs cleared will consider both selections.</em>
                        </p>
                        <div class="form-item col-1-1">
                            <?php echo $wspnFields->selectField('Clear logs older than...', 'clear_days', 'settings', [
                                7  => '7 days',
                                14 => '14 days',
                                21 => '21 days',
                            ], true, [
                                'description' => 'Select how often the logs should be cleared (in days).',
                            ]); ?>
                        </div>
                        <div class="form-item col-1-1">
                            <?php echo $wspnFields->textField('Only keep this number of logs', 'max_logs', 'settings', 120, [
                                'description' => 'Select how many logs should be kept.',
                                'type'        => 'number',
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


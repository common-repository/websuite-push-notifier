<?php
    
    use WSPN\admin;
    use WSPN\wspn\websuitePushNotifierFields;
    
    $wspnFields = new websuitePushNotifierFields($this->pluginName, $this->pluginVersion);

?>
<div id="settings-tab-1" class="tab-content active">
    <div class="card">
        <div class="card-header">
            Messages Sent
        </div>
        <div class="card-body">
            <div class="grid">
                <div>
                    <button class="btn is-secondary is-right wspn_clear_number">Clear x Logs</button>
                    <button style="margin-right:10px;" class="btn is-secondary is-right wspn_clear_days">Clear Older Logs</button>
                </div>
                <div class="col-1-1">
                    <?php
                        if (!!$wspnFields->getOptionsReturn('logs_enabled', 'settings')) {
                            $sentTable = new admin\websuitePushNotifierLogsTable(WSPN_PLUGIN_NAME, WSPN_PLUGIN_VERSION, 'success');
                            $sentTable->prepare_items();
                            $sentTable->display();
                        } else {
                            ?>
                            <p>Logs are disabled in your plugin settings. Please enable to be able to view and log successful messages.</p><?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="settings-tab-2" class="tab-content">
    <div class="card">
        <div class="card-header">
            Failed Messages
        </div>
        <div class="card-body">
            <div class="grid">
                <div>
                    <button class="btn is-secondary is-right wspn_clear_number">Clear x Logs</button>
                    <button style="margin-right:10px;" class="btn is-secondary is-right wspn_clear_days">Clear Older Logs</button>
                </div>
                <div class="col-1-1">
                    <?php
                        if (!!$wspnFields->getOptionsReturn('logs_enabled', 'settings')) {
                            $errorTable = new admin\websuitePushNotifierLogsTable(WSPN_PLUGIN_NAME, WSPN_PLUGIN_VERSION, 'failed');
                            $errorTable->prepare_items();
                            $errorTable->display();
                        } else {
                            ?>
                            <p>Logs are disabled in your plugin settings. Please enable to be able to view and log errors.</p><?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

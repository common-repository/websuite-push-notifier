<div class="<?php echo esc_html($this->singularName); ?>-header">
    <h1><?php echo esc_html($this->singularName) ?></h1>
</div>
<div id="poststuff" class="wrap admin-security cols-2">
    <form action="#" method="post">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div id="titlediv">
                    <div id="titlewrap">
                        <label for="title">Archive Title</label>
                        <input type="text" name="content[title_<?php echo esc_html($this->postTypeID) ?>]" size="30" value="<?php echo esc_html(get_option('title_' . $this->postTypeID)); ?>" id="title" spellcheck="true" autocomplete="off">
                    </div>
                </div>
                <div class="fields">
                    <?php wp_editor(get_option('intro_' . $this->postTypeID), 'content[intro_' . $this->postTypeID . ']'); ?>
                </div>
                <div id="postbox-container-1" class="postbox-container">
                    <input type="submit" name="publish" id="publish" class="button button-primary button-large" value="Publish">
                </div>
                <div id="postbox-container-2" class="postbox-container">
                </div>
            </div>
        </div>
    </form>
</div>

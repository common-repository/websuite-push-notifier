<?php
    
    namespace WSPN\wspn;
    
    if (!defined('ABSPATH')) {
        exit();
    }
    
    /**
     * The field's builder class.
     *
     * This class is used to build input fields quickly and easily.
     *
     * @since      1.0.0
     * @package    WebsuitePushNotifier
     * @subpackage WebsuitePushNotifier/wspn
     * @author     Johan Pretorius <johan.pretorius@afrozaar.com>
     */
    class websuitePushNotifierFields {
        
        /**
         * The ID of this plugin.
         *
         * @access private
         * @since 1.0.0
         * @var string $pluginName The ID of this plugin.
         */
        public string $pluginName;
        
        /**
         * The version of this plugin.
         *
         * @access private
         * @since 1.0.0
         * @var string $pluginVersion The current version of this plugin.
         */
        private string $pluginVersion;
        
        /**
         * The options array.
         *
         * @access public
         * @since 1.0.0
         * @var string $optionsReturn Returns the option's data to use in field.
         *
         */
        public $optionsReturn;
        
        /**
         * The field message for premium version.
         *
         * @access public
         * @since 1.0.0
         * @var string $optionsReturn Returns the message.
         *
         */
        public string $message;
        
        /**
         * Initialize the options to build the fields.
         *
         * @param $pluginName
         * @param $pluginVersion
         *
         * @since 1.0.0
         */
        public function __construct($pluginName, $pluginVersion) {
            $this->pluginName    = $pluginName;
            $this->pluginVersion = $pluginVersion;
            $this->optionsReturn = (new websuitePushNotifierGlobal($this->pluginName, $this->pluginVersion))->getPluginOptions();
            $this->message       = 'This is a Premium feature.';
        }
        
        /**
         * Standard text field, use this as basis for other fields.
         *
         * @param string $title The input title and label.
         * @param string $name The input name.
         * @param string $group Group fields together.
         * @param string $default The default value if any.
         * @param array $options
         *
         * @return string
         *
         * @since 1.0.0
         */
        public function textField(
            string $title, string $name, string $group = '', string $default = '', array $options = [
            'description' => '',
            'placeholder' => '',
            'validate'    => false,
            'sub'         => '',
            'type'        => 'text',
            'status'      => false,
        ]
        ): string {
            $dataValue     = $this->getOptionsReturn($name, $group, $options);
            $fieldName     = $this->getOptionsReturn($name, $group, $options, 'field');
            $premiumCheck  = isset($options['status']) && $options['status'];
            $validateCheck = isset($options['validate']) && $options['validate'];
            
            /**
             * Field label.
             */
            if (isset($options['type']) && $options['type'] === 'hidden') {
                $htmlBuild = '';
            } else {
                $htmlBuild = '<label
                title="' . esc_attr($title) . '"
                for="' . esc_attr($name) . '"
                ' . ($premiumCheck ? 'class="tip is-disabled" data-tt="<b>' . esc_html($this->message) . '</b>"' : '') . '>
                ' . esc_html($title) . '
                ' . ($validateCheck ? '<span class="is-req">*</span>' : '') . ' </label>';
            }
            
            /**
             * Build field html.
             */
            $htmlBuild .= '<input
            type="' . esc_attr(($options['type'] ?? 'text')) . '"
            id="' . esc_attr($name) . '"
            name="' . esc_attr($fieldName) . '"
            value="' . esc_attr((!empty($dataValue) ? $dataValue : $default)) . '"
            ' . (isset($options['placeholder']) ? 'placeholder="' . esc_attr($options['placeholder']) . '" ' : '') . ($premiumCheck ? ' readonly' : '') . (!$validateCheck ? 'class="no-validate"' : 'class="validate" required') . '>';
            
            /**
             * Description.
             */
            $premiumDesc = ($premiumCheck ? ' (Premium Only)' : '');
            $htmlBuild   .= (isset($options['description']) ? '<div class="is-desc">' . esc_html($options['description']) . ' ' . esc_html($premiumDesc) . '</div>' : '<div class="is-desc">' . esc_html($premiumDesc) . '</div>');
            
            return $htmlBuild;
        }
        
        /**
         * Standard wp editor field, use this as basis for other fields.
         *
         * @param string $title The input title and label.
         * @param string $name The input name.
         * @param string $group Group fields together.
         * @param string $default The default value if any.
         * @param array $options
         *
         * @return string
         *
         * @since 1.0.0
         */
        public function wpEditor(
            string $title, string $name, string $group = '', string $default = '', array $options = [
            'description' => '',
            'placeholder' => '',
            'validate'    => false,
            'sub'         => '',
            'type'        => 'text',
            'status'      => false,
        ]
        ): string {
            $dataValue     = $this->getOptionsReturn($name, $group, $options);
            $fieldName     = $this->getOptionsReturn($name, $group, $options, 'field');
            $premiumCheck  = isset($options['status']) && $options['status'];
            $validateCheck = isset($options['validate']) && $options['validate'];
            
            /**
             * Field label.
             */
            if (isset($options['type']) && $options['type'] === 'hidden') {
                $htmlBuild = '';
            } else {
                $htmlBuild = '<label
                title="' . esc_attr($title) . '"
                for="' . esc_attr($name) . '"
                ' . ($premiumCheck ? 'class="tip is-disabled" data-tt="<b>' . esc_html($this->message) . '</b>"' : '') . '>
                ' . esc_html($title) . '
                ' . ($validateCheck ? '<span class="is-req">*</span>' : '') . ' </label>';
            }
            
            ob_start();
            wp_editor($dataValue, $name, [
                'tinymce'       => true,
                'textarea_name' => $fieldName,
                'quicktags'     => false,
                'media_buttons' => false,
                'textarea_rows' => 5,
            ]);
            $htmlBuild .= ob_get_contents();
            ob_get_clean();
            
            /**
             * Description.
             */
            $premiumDesc = ($premiumCheck ? ' (Premium Only)' : '');
            $htmlBuild   .= (isset($options['description']) ? '<div class="is-desc">' . esc_html($options['description']) . ' ' . esc_html($premiumDesc) . '</div>' : '<div class="is-desc">' . esc_html($premiumDesc) . '</div>');
            
            return $htmlBuild;
        }
        
        /**
         * Textarea field.
         *
         * @param string $title The input title and label.
         * @param string $name The input name.
         * @param string $group Group fields together.
         * @param string $default The default value if any.
         * @param array $options
         *
         * @return string
         *
         * @since 1.0.0
         */
        public function textBoxField(
            string $title, string $name, string $group = '', string $default = '', array $options = [
            'description' => '',
            'placeholder' => '',
            'validate'    => false,
            'sub'         => '',
            'status'      => false,
            'maxlength'   => '',
        ]
        ): string {
            $dataValue     = $this->getOptionsReturn($name, $group, $options);
            $fieldName     = $this->getOptionsReturn($name, $group, $options, 'field');
            $premiumCheck  = isset($options['status']) && $options['status'];
            $validateCheck = isset($options['validate']) && $options['validate'];
            
            /**
             * Field label.
             */
            $htmlBuild = '<label
            title="' . esc_attr($title) . '"
            for="' . esc_attr($name) . '"
            ' . ($premiumCheck ? 'class="tip is-disabled" data-tt="<b>' . esc_html($this->message) . '</b>"' : '') . '>
            ' . esc_html($title) . '
            ' . ($validateCheck ? '<span class="is-req">*</span>' : '') . '</label>';
            
            /**
             * Build field html.
             */
            $htmlBuild .= '<textarea
            id="' . esc_attr($name) . '"
            name="' . esc_attr($fieldName) . '"
            maxlength="' . esc_attr(($options['maxlength'] ?? 999)) . '"
            ' . ($premiumCheck ? ' readonly' : '') . '>' . (!empty($dataValue) ? $dataValue : $default) . '</textarea>';
            
            /**
             * Description.
             */
            $premiumDesc = ($premiumCheck ? ' (Premium Only)' : '');
            $htmlBuild   .= (isset($options['description']) ? '<div class="is-desc">' . esc_html($options['description']) . ' ' . esc_html($premiumDesc) . '</div>' : '<div class="is-desc">' . esc_html($premiumDesc) . '</div>');
            
            return $htmlBuild;
        }
        
        /**
         * Select field.
         *
         * @param string $title The input title and label.
         * @param string $name The input name.
         * @param string $group Group fields together.
         * @param array $values The default values in an array.
         * @param bool $useArrayKeysAsValue Whether the array has specific keys that need to be used on the option.
         * @param array $options
         *
         * @return string
         *
         * @since 1.0.0
         */
        public function selectField(
            string $title, string $name, string $group = '', array $values = ['select'], bool $useArrayKeysAsValue = false, array $options = [
            'description' => '',
            'placeholder' => '',
            'validate'    => false,
            'default'     => '',
            'sub'         => '',
            'status'      => false,
        ]
        ): string {
            $dataValue     = $this->getOptionsReturn($name, $group, $options);
            $fieldName     = $this->getOptionsReturn($name, $group, $options, 'field');
            $premiumCheck  = isset($options['status']) && $options['status'];
            $validateCheck = isset($options['validate']) && $options['validate'];
            
            /**
             * Field label.
             */
            $htmlBuild = '<label
            title="' . esc_attr($title) . '"
            for="' . esc_attr($name) . '"
            ' . ($premiumCheck ? 'class="tip is-disabled" data-tt="<b>' . esc_html($this->message) . '</b>"' : '') . '>
            ' . esc_html($title) . '
            ' . ($validateCheck ? '<span class="is-req">*</span>' : '') . '</label>';
            
            /**
             * Build field html.
             */
            $htmlBuild .= '<select id="' . esc_attr($name) . '" name="' . esc_attr($fieldName) . '" ' . ($premiumCheck ? ' class="is-disabled" readonly' : '') . '>';
            
            $htmlBuild .= '<option value="">' . esc_html(($options['placeholder'] ?? 'Select')) . '</option>';
            
            // If the select uses specific keys, do not sort
            if (!$useArrayKeysAsValue) {
                sort($values);
            }
            
            foreach ($values as $key => $value) {
                if (!empty($dataValue) && $dataValue === $value) {
                    $checkOption = 'selected';
                } elseif (empty($dataValue) && isset($options['default']) && $options['default'] === $value) {
                    $checkOption = 'selected';
                } else {
                    $checkOption = '';
                }
                
                $htmlBuild .= '<option value="' . esc_attr(($useArrayKeysAsValue ? $key : $value)) . '" ' . $checkOption . '>' . ucwords(esc_html($value)) . '</option>';
            }
            $htmlBuild .= '</select>';
            
            /**
             * Description.
             */
            $premiumDesc = ($premiumCheck ? ' (Premium Only)' : '');
            $htmlBuild   .= (isset($options['description']) ? '<div class="is-desc">' . esc_html($options['description']) . ' ' . esc_html($premiumDesc) . '</div>' : '<div class="is-desc">' . esc_html($premiumDesc) . '</div>');
            
            return $htmlBuild;
        }
        
        /**
         * Checkbox field.
         *
         * Option: eventClass - Used to attach jQuery operators to field.
         *
         * @param string $title The input title and label.
         * @param string $name The input name.
         * @param string $group Group fields together.
         * @param int $default The default value if any.
         * @param array $options
         *
         * @return string
         *
         * @since 1.0.0
         */
        public function checkboxField(
            string $title, string $name, string $group = '', int $default = 0, array $options = [
            'description' => '',
            'eventClass'  => '',
            'checked'     => '',
            'sub'         => '',
            'status'      => false,
            'message'     => '',
        ]
        ): string {
            $dataValue    = $this->getOptionsReturn($name, $group, $options);
            $fieldName    = $this->getOptionsReturn($name, $group, $options, 'field');
            $defaultCheck = isset($options['checked']) || !empty($dataValue) ? 'checked="checked"' : '';
            $premiumCheck = isset($options['status']) && $options['status'];
            $eventClass   = (isset($options['eventClass']) && $options['eventClass'] ? ' ' . $options['eventClass'] : '');
            
            /**
             * Field label.
             */
            $message   = isset($options['message']) && !empty($options['message']) ? $options['message'] : $this->message;
            $htmlBuild = '<label title="' . esc_attr($title) . '" for="' . esc_attr($name) . '" ' . ($premiumCheck ? 'class="tip is-disabled" data-tt="<b>' . esc_html($message) . '</b>"' : '') . '>';
            
            /**
             * Build field html.
             */
            $htmlBuild .= '<input
            type="checkbox"
            name="' . esc_attr($fieldName) . '"
            value="' . esc_attr($default) . '"
            id="' . esc_attr($fieldName) . '"
            class="switch' . $eventClass . '"
                ' . (!empty($dataValue) ? checked($dataValue, $default, false) : $defaultCheck) . '
                ' . ($premiumCheck ? ' readonly' : '') . (!empty($message && $premiumCheck) ? ' disabled' : '') . '>';
            
            $htmlBuild .= ' ' . esc_html($title) . '</label>';
            
            /**
             * Description.
             */
            $premiumDesc = ($premiumCheck && $message ? ' (Premium Only)' : '');
            $htmlBuild   .= (isset($options['description']) ? '<div class="is-desc">' . esc_html($options['description']) . ' ' . esc_html($premiumDesc) . '</div>' : '<div class="is-desc">' . esc_html($premiumDesc) . '</div>');
            
            return $htmlBuild;
        }
        
        /**
         * Radiobox Field.
         *
         * @param string $title The input title and label.
         * @param string $name The input name.
         * @param string $group Group fields together.
         * @param mixed $default The default value if any.
         * @param array $options
         *
         * @return string
         *
         * @since 1.0.0
         */
        public function radioBoxField(
            string $title, string $name, string $group = '', $default = 0, array $options = [
            'description' => '',
            'eventClass'  => '',
            'checked'     => '',
            'sub'         => '',
            'status'      => false,
        ]
        ): string {
            $dataValue    = $this->getOptionsReturn($name, $group, $options);
            $fieldName    = $this->getOptionsReturn($name, $group, $options, 'field');
            $defaultCheck = isset($options['checked']) && !empty($dataValue) ? 'checked="checked"' : '';
            $premiumCheck = isset($options['status']) && $options['status'];
            
            /**
             * Build field html.
             */
            $htmlBuild = '<input
            type="radio"
            id="' . esc_attr(($name . '-' . $default)) . '"
            name="' . esc_attr($fieldName) . '"
            value="' . esc_attr($default) . '"
             ' . (!empty($dataValue) ? checked($dataValue, $default, false) : $defaultCheck) . '
             ' . ($premiumCheck ? ' readonly disabled class="is-disabled"' : '') . '>';
            
            /**
             * Field label.
             */
            $htmlBuild .= '<label
            for="' . esc_attr(($name . '-' . $default)) . '"
            title="' . esc_attr($title) . '"
            ' . ($premiumCheck ? 'class="tip is-disabled" data-tt="<b>' . esc_html($this->message) . '</b>"' : '') . '>
            ' . esc_html($title) . '</label>';
            
            /**
             * Description.
             */
            $premiumDesc = ($premiumCheck ? ' (Premium Only)' : '');
            $htmlBuild   .= (isset($options['description']) ? '<div class="is-desc">' . esc_html($options['description']) . ' ' . esc_html($premiumDesc) . '</div>' : '<div class="is-desc">' . esc_html($premiumDesc) . '</div>');
            
            return $htmlBuild;
        }
        
        /**
         * Color Picker Field.
         *
         * @param string $title The input title and label.
         * @param string $name The input name.
         * @param string $group Group fields together.
         * @param string $default The default value if any.
         * @param array $options
         *
         * @return string
         *
         * @since 1.0.0
         */
        public function colorPickerField(
            string $title, string $name, string $group = '', string $default = '', array $options = [
            'description' => '',
            'sub'         => '',
            'status'      => false,
        ]
        ): string {
            $dataValue    = $this->getOptionsReturn($name, $group, $options);
            $fieldName    = $this->getOptionsReturn($name, $group, $options, 'field');
            $premiumCheck = isset($options['status']) && $options['status'];
            
            /**
             * Field label.
             */
            $htmlBuild = '<label
            title="' . esc_attr($title) . '"
            for="' . esc_attr($name) . '"
            ' . ($premiumCheck ? 'class="tip is-disabled" data-tt="<b>' . esc_html($this->message) . '</b>"' : '') . '>
            ' . esc_html($title) . '</label>';
            
            /**
             * Build field html.
             */
            $htmlBuild .= '<input
            type="text"
            id="' . esc_attr($name) . '"
            name="' . esc_attr($fieldName) . '"
            value="' . esc_attr((!empty($dataValue) ? $dataValue : $default)) . '"
            class="minicolors' . ($premiumCheck ? ' is-disabled' : '') . '"
            data-defaultValue="' . esc_attr($default) . '"
             ' . ($premiumCheck ? ' readonly' : '') . '>';
            
            /**
             * Description.
             */
            $premiumDesc = ($premiumCheck ? ' (Premium Only)' : '');
            $htmlBuild   .= (isset($options['description']) ? '<div class="is-desc">' . esc_html($options['description']) . ' ' . esc_html($premiumDesc) . '</div>' : '<div class="is-desc">' . esc_html($premiumDesc) . '</div>');
            
            return $htmlBuild;
        }
        
        /**
         * Slider field.
         *
         * @param string $title The input title and label.
         * @param string $name The input name.
         * @param string $group Group fields together.
         * @param int $default The default value if any.
         * @param array $options
         *
         * @return string
         *
         * @since 1.0.0
         */
        public function sliderField(
            string $title, string $name, string $group = '', int $default = 90, array $options = [
            'description' => '',
            'min'         => 0,
            'max'         => 10,
            'append'      => '',
            'sub'         => '',
            'status'      => false,
        ]
        ): string {
            $dataValue    = $this->getOptionsReturn($name, $group, $options);
            $fieldName    = $this->getOptionsReturn($name, $group, $options, 'field');
            $premiumCheck = isset($options['status']) && $options['status'];
            
            /**
             * Field label.
             */
            $htmlBuild = '<label
            title="' . esc_attr($title) . '"
            for="' . esc_attr($name) . '"
            ' . ($premiumCheck ? 'class="tip is-disabled" data-tt="<b>' . esc_html($this->message) . '</b>"' : 'class="sliderLabel"') . '>
            ' . esc_html($title) . ':</label>';
            
            /**
             * Build field html.
             */
            $htmlBuild .= '<input
            type="text"
            name="' . esc_attr($fieldName) . '"
            value="' . esc_attr((!empty($dataValue) ? $dataValue : $default)) . '"
            class="sliderField' . ($premiumCheck ? ' is-disabled' : '') . '"
            id="' . esc_attr($name) . '"
            data-min="' . esc_attr($options['min']) . '"
            data-max="' . esc_attr($options['max']) . '"
            data-default="' . esc_attr((!empty($dataValue) ? $dataValue : $default)) . '"
            data-step="' . esc_attr($options['step']) . '"
            ' . (isset($options['append']) ? 'data-append="' . esc_attr($options['append']) . '"' : '') . '
            readonly="readonly"
             ' . ($premiumCheck ? ' readonly' : '') . '>' . $options['append'];
            
            $htmlBuild .= '<div class="slider-pt-' . esc_html($name) . '-ui toolbox-slider"></div>';
            
            /**
             * Description.
             */
            $premiumDesc = ($premiumCheck ? ' (Premium Only)' : '');
            $htmlBuild   .= (isset($options['description']) ? '<div class="is-desc">' . esc_html($options['description']) . ' ' . esc_html($premiumDesc) . '</div>' : '<div class="is-desc">' . esc_html($premiumDesc) . '</div>');
            
            return $htmlBuild;
        }
        
        /**
         * Media Select Field.
         *
         * Uses: wp_enqueue_media()
         *
         * @param string $title The label title.
         * @param string $name The field name.
         * @param string $group Group fields together.
         * @param array $options
         *
         * @return string
         *
         * @since 1.0.0
         */
        public function imageUploadField(
            string $title, string $name, string $group = '', array $options = [
            'description' => '',
            'placeholder' => '',
            'validate'    => false,
            'sub'         => '',
        ]
        ): string {
            $dataValue = $this->getOptionsReturn($name, $group, $options);
            $fieldName = $this->getOptionsReturn($name, $group, $options, 'field');
            $src       = '';
            $alt       = '';
            
            if (!empty($dataValue) && is_numeric($dataValue)) {
                $src   = wp_get_attachment_url($dataValue);
                $value = $dataValue;
            } else if (!empty($dataValue)) {
                $src   = $dataValue;
                $value = $dataValue;
            } else {
                $value = '';
            }
            
            if ($src) {
                $alt = $title;
            }
            
            $htmlBuild = '<label title="' . esc_attr($title) . '">' . esc_html($title) . ' ' . (isset($options['validate']) && $options['validate'] ? '<span class="is-req">*</span></label>' : '') . '</label>';
            $htmlBuild .= '<div class="uploader-selected-image"><img data-src="' . esc_attr($src) . '" src="' . esc_attr($src) . '" alt="' . esc_attr($alt) . '"></div>';
            $htmlBuild .= '<div>';
            $htmlBuild .= '<input type="hidden" name="' . esc_attr($fieldName) . '" id="' . esc_attr($name) . '" value="' . esc_attr($value) . '" />';
            $htmlBuild .= '<button type="button" class="upload_image_button btn">Upload</button>';
            $htmlBuild .= ' <button type="button" class="remove_image_button btn is-small is-icon is-destructive">';
            $htmlBuild .= '<span class="dashicons dashicons-post-trash"></span></button>';
            $htmlBuild .= '</div>';
            
            /**
             * Description.
             */
            $htmlBuild .= (isset($options['description']) ? '<div class="is-desc">' . esc_html($options['description']) . '</div>' : '');
            
            return $htmlBuild;
        }
        
        /**
         * Returns a list input with a text field to add new items.
         *
         * @param string $title The label title.
         * @param string $name The field name.
         * @param string $group Group fields together.
         * @param array $options
         *
         * @return string
         */
        public function listField(string $title, string $name, string $group = '', array $options = []): string {
            $value = $this->getOptionsReturn($name, $group, $options);
            ob_start();
            ?>
            <label title="<?php echo esc_attr($title); ?>" id="<?php echo esc_attr($group ? $group . '[' . $name . ']' : $name); ?>">
                <?php echo esc_html($title); ?>
            </label>
            <?php if (!empty($options['description'])) { ?>
                <p class="is-desc" style="margin-top: 0;"><?php echo $options['description']; ?></p>
            <?php } ?>
            <div class="listFieldContainer">
                <?php
                    if (!empty($value)) {
                        foreach ($value as $item) {
                            echo '<div class="listFieldItem"><input type="text" name="'
                                 . esc_attr($group ? $group . '[' . $name . ']' : $name)
                                 . '[]" value="'
                                 . esc_attr($item) . '" class="listField"><button type="button" class="removeListField btn is-small is-icon is-destructive">'
                                 . '<span class="dashicons dashicons-post-trash"></span></button></div>';
                        }
                    } else {
                        echo '<div class="listFieldItem"><input type="text" name="'
                             . esc_attr($group ? $group . '[' . $name . ']' : $name)
                             . '[]" value="" class="listField"><button type="button" class="removeListField btn is-small is-icon is-destructive">'
                             . '<span class="dashicons dashicons-post-trash"></span></button></div>';
                    }
                ?>
            </div>
            <button type="button" class="addListField btn is-small is-icon">
                <span class="dashicons dashicons-plus-alt"></span></button>
            <?php
            return ob_get_clean();
        }
        
        /**
         * Build field names and return data
         *
         * @param string $name The field name.
         * @param string $group Group fields together.
         * @param array $options
         * @param string $type
         *
         * @return string
         *
         * @since 1.0.0
         */
        public function getOptionsReturn(string $name = '', string $group = '', array $options = [], string $type = 'data') {
            $dataValue = '';
            $fieldName = '';
            
            /**
             * Field naming setup.
             */
            if (empty($group) && isset($name)) {
                $dataValue = $this->optionsReturn[$name] ?? '';
                $fieldName = $name;
            } elseif (!empty($group) && empty($options['sub'])) {
                $dataValue = $this->optionsReturn[$group][$name] ?? '';
                $fieldName = $group . '[' . $name . ']';
            } elseif (!empty($options['sub'])) {
                $dataValue = $this->optionsReturn[$group][$options['sub']][$name] ?? '';
                $fieldName = $group . '[' . $options['sub'] . ']' . '[' . $name . ']';
            }
            
            if ($type === 'data') {
                /**
                 * Data value return on Sidebar Meta fields.
                 */
                if (isset($options['meta'])) {
                    global $post;
                    $postId        = $post->ID;
                    $metaDataValue = get_post_meta($postId, $this->themeName, true);
                    
                    return ($metaDataValue[$name] ?? '');
                }
                
                return $dataValue;
            }
            
            return $fieldName;
        }
    }

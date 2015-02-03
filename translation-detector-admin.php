<?php
/* * *************************************************************
 * Back-End Scripts & Styles enqueueing
 * ************************************************************* */

function tdfp_register_scripts() {
    if (is_admin()) {
        wp_enqueue_style('translation-detector-admin', plugins_url('css/translation-detector-admin.css', __FILE__));
        wp_enqueue_style('wp-color-picker'); // Add the color picker css file     
        wp_enqueue_script('translation-detector', plugins_url('js/translation-detector.js', __FILE__), array('wp-color-picker'), false, true);
    }
}
add_action('admin_enqueue_scripts', 'tdfp_register_scripts');

/* * *************************************************************
 * Override strings page in polylang admin
 * ************************************************************* */

function tdfp_custom_strings($strings) {
    $options = get_option('tdfp_settings');
    if (isset($options['text'])) {
        $strings[] = array(
            'context' => 'Translation detector',
            'multiline' => 0,
            'name' => 'Text to display',
            'string' => $options['text'],
        );
    }
    return $strings;
}
add_filter('pll_get_strings', 'tdfp_custom_strings');


/* * *************************************************************
 * Create admin page menu
 * ************************************************************* */

function tdfp_add_admin_menu() {
    add_options_page('Translation detector', 'Translation detector', 'manage_options', 'translation_detector', 'translation_detector_options_page');
}
add_action('admin_menu', 'tdfp_add_admin_menu');

/* * *************************************************************
 * Register plugin settings 
 * ************************************************************* */

function tdfp_settings_init() {
    register_setting('tdfp_settings', 'tdfp_settings');
    add_settings_section('tdfp_options', '', '', 'tdfp_settings');
    add_settings_field('tdfp_text_display', __('Text to display', 'tdfp-translate'), 'tdfp_text_display_render', 'tdfp_settings', 'tdfp_options');
    add_settings_field('tdfp_color_text', __('Text color', 'tdfp-translate'), 'tdfp_color_text_render', 'tdfp_settings', 'tdfp_options');
    add_settings_field('tdfp_color_background', __('Background color', 'tdfp-translate'), 'tdfp_color_background_render', 'tdfp_settings', 'tdfp_options');
    add_settings_field('tdfp_color_hover', __('Hover color', 'tdfp-translate'), 'tdfp_color_hover_render', 'tdfp_settings', 'tdfp_options');
    add_settings_field('tdfp_select_display', __('Display', 'tdfp-translate'), 'tdfp_select_display_render', 'tdfp_settings', 'tdfp_options');
    add_settings_field('tdfp_checkbox_post_type', __('Post types', 'tdfp-translate'), 'tdfp_checkbox_post_type_render', 'tdfp_settings', 'tdfp_options');
}
add_action('admin_init', 'tdfp_settings_init');

function tdfp_text_display_render() {
    $options = get_option('tdfp_settings');
    $val = (isset($options['text'])) ? $options['text'] : '';
    ?>
    <input type="text" name="tdfp_settings[text]" value="<?php echo $val; ?>" >
    <?php
    if ($val != '') {
        ?>
        <span class="description"><?php echo sprintf(__('You can translate this text in Polylang settings <a href="%s">here.</a>', 'tdfp-translate'), admin_url('options-general.php?page=mlang&tab=strings')); ?></span>
        <?php
    }
}

function tdfp_color_text_render() {
    $options = get_option('tdfp_settings');
    $val = (isset($options['color'])) ? $options['color'] : '';
    ?>
    <input type="text" name="tdfp_settings[color]" value="<?php echo $val; ?>" class="color-field" />
    <?php
}

function tdfp_color_background_render() {
    $options = get_option('tdfp_settings');
    $val = (isset($options['background'])) ? $options['background'] : '';
    ?>
    <input type="text" name="tdfp_settings[background]" value="<?php echo $val; ?>" class="color-field" />
    <?php
}

function tdfp_color_hover_render() {
    $options = get_option('tdfp_settings');
    $val = (isset($options['hover'])) ? $options['hover'] : '';
    ?>
    <input type="text" name="tdfp_settings[hover]" value="<?php echo $val; ?>" class="color-field" />
    <?php
}

function tdfp_select_display_render() {
    $options = get_option('tdfp_settings');
    $val = (isset($options['display'])) ? $options['display'] : '';
    $select_values = array(
        'none' => __('Don\'t displays', 'tdfp-translate'),
        'top' => __('Top of the content', 'tdfp-translate'),
        'bot' => __('Bottom of the content', 'tdfp-translate'),
        'both' => __('Top and bottom', 'tdfp-translate'),
    );
    ?>
    <select name="tdfp_settings[display]">
        <?php foreach ($select_values as $select_value => $select_name) { ?>
            <option value="<?php echo $select_value; ?>" <?php if ($select_value == $val) { ?>selected="selected"<?php } ?>><?php echo $select_name; ?></option>
        <?php } ?>
    </select>
    <?php
}

function tdfp_checkbox_post_type_render() {
    $options = get_option('tdfp_settings');
    ?>
    <label><input type="checkbox" name="tdfp_settings[post_type][homepage]" value="homepage" <?php if(isset($options['post_type']['homepage'])) echo 'checked'; ?> />
    <?php _e('Homepage', 'tdfp-translate'); ?></label>
    <?php
    $post_types = get_post_types(array('public' => true), 'objects');
    foreach ($post_types as $type_name => $post_type) {
        if ($type_name != 'attachment') {
            ?>
            <label><input type="checkbox" name="tdfp_settings[post_type][<?php echo $type_name; ?>]" value="<?php echo $type_name; ?>" <?php if(isset($options['post_type'][$type_name])) echo 'checked'; ?> />
            <?php _e($post_type->label); ?></label>
            <?php
        }
    }
}

function translation_detector_options_page() {
    ?>
    <form action='options.php' method='post'>
        <div class="wrap">
            <h2>Translation detector</h2>
            <div id="post-body-content">
                <div id="tdfp-admin-page" class="meta-box-sortabless">
                    <div id="tdfp-form" class="postbox">
                        <h3 class="hndle"><span><?php _e('Settings', 'tdfp-translate'); ?></span></h3>
                        <div class="inside">
                            <?php
                            settings_fields('tdfp_settings');
                            do_settings_sections('tdfp_settings');
                            submit_button();
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php
}

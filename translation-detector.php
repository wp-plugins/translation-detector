<?php

/**
 * Plugin Name: Translation detector for Polylang
 * Plugin URI: https://wordpress.org/plugins/translation-detector/
 * Description: This plugin allow you to display links to your translated content. The style and the positions of the links is configurable.
 * Version: 1.1
 * Author: Guillaume DIARD
 * Author URI: http://guillaume-diard.fr
 * Domain Path: /langs
 * Text Domain: tdfp-translate
 */
/* * *************************************************************
 * SECURITY : Exit if accessed directly
 * ************************************************************* */
if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!defined('ABSPATH')) {
    exit;
}

/* * *************************************************************
 * Define constants
 * ************************************************************* */
if (!defined('TDFP_PATH')) {
    define('TDFP_PATH', plugin_dir_path(__FILE__));
}
if (!defined('TDFP_BASE')) {
    define('TDFP_BASE', plugin_basename(__FILE__));
}
if (!defined('TDFP_URL')) {
    define('TDFP_URL', plugin_dir_url(__FILE__));
}
if (!defined('TDFP_ID')) {
    define('TDFP_ID', 'translation_detector');
}

/* * *************************************************************
 * Load plugin files
 * ************************************************************* */
$tdfpFiles = array('admin', 'functions');
foreach ($tdfpFiles as $tdfpFile) {
    require_once( plugin_dir_path(__FILE__) . 'translation-detector-' . $tdfpFile . '.php' );
}

/* * *************************************************************
 * Load plugin textdomain
 * ************************************************************* */
if (!function_exists('tdfp_load_textdomain')) {

    function tdfp_load_textdomain() {
        $path = dirname(plugin_basename(__FILE__)) . '/langs/';
        load_plugin_textdomain('tdfp-translate', false, $path);
    }
    add_action('init', 'tdfp_load_textdomain');
}

/* * *************************************************************
 * Add settings link on extentions page
 * ************************************************************* */

function tdfp_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=' . TDFP_ID . '">' . __('Settings', 'tdfp-translate') . '</a>';
    $links[] = $settings_link;
    return $links;
}
add_filter('plugin_action_links_' . TDFP_BASE, 'tdfp_settings_link');

/* * *************************************************************
 * Add custom meta link on plugin list page
 * ************************************************************* */
if (!function_exists('tdfp_meta_links')) {

    function tdfp_meta_links($links, $file) {
        if ($file == TDFP_BASE) {
            $links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RVGEPQCLB78GC" target="_blank" title="' . __('Donate', 'tdfp-translate') . '"><strong>' . __('Donate', 'tdfp-translate') . '</strong></a>';
        }
        return $links;
    }
    add_filter('plugin_row_meta', 'tdfp_meta_links', 10, 2);
}

/* * *************************************************************
 * Remove Plugin settings from DB on uninstallation
 * ************************************************************* */

function tdfp_uninstall() {
    delete_option('tdfp_settings');
}
//Hooks for install
if (function_exists('register_uninstall_hook')) {
    register_uninstall_hook(__FILE__, 'tdfp_uninstall');
}
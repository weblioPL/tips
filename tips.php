<?php
/**
 * Plugin Name: Tips Summary
 * Description: Receives tips data via REST webhook, stores in database, and provides shortcodes for aggregated data.
 * Version: 1.0.0
 * Author: Tips Summary
 * Text Domain: tips-summary
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'TIPS_SUMMARY_VERSION', '1.0.0' );
define( 'TIPS_SUMMARY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TIPS_SUMMARY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once TIPS_SUMMARY_PLUGIN_DIR . 'includes/class-tips-database.php';
require_once TIPS_SUMMARY_PLUGIN_DIR . 'includes/class-tips-rest-api.php';
require_once TIPS_SUMMARY_PLUGIN_DIR . 'includes/class-tips-shortcodes.php';
require_once TIPS_SUMMARY_PLUGIN_DIR . 'includes/class-tips-admin.php';

register_activation_hook( __FILE__, array( 'Tips_Database', 'activate' ) );

add_action( 'plugins_loaded', 'tips_summary_init' );

function tips_summary_init() {
    Tips_Database::init();
    Tips_REST_API::init();
    Tips_Shortcodes::init();
    Tips_Admin::init();
}

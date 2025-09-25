<?php
/**
* Plugin Name: FilePress
* Plugin URI: https://mahamuduldev.com/filepress
* Description: FilePress is a simple yet modern WordPress file manager. Upload, organize, and manage files from the WordPress dashboard.
* Version: 1.1.0
* Author: Mahamudul Hasan
* Author URI: https://mahamuduldev.com
* Text Domain: filepress
* Domain Path: /languages
* License: GPLv2 or later
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants.
define( 'FILEPRESS_VERSION', '1.0.0' );
define( 'FILEPRESS_PATH', plugin_dir_path( __FILE__ ) );
define( 'FILEPRESS_URL', plugin_dir_url( __FILE__ ) );

// Include the main class.
require_once FILEPRESS_PATH . 'includes/class-filepress.php';

// Initialize the plugin.
function run_filepress() {
    $plugin = new FilePress();
    $plugin->run();
}
add_action( 'plugins_loaded', 'run_filepress' );

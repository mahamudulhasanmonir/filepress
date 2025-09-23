<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FilePress {

    /**
     * Run the plugin.
     */
    public function run() {
        // Hooks
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    /**
     * Register admin menu page.
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'FilePress', 'filepress' ),
            __( 'FilePress', 'filepress' ),
            'manage_options',
            'filepress',
            array( $this, 'render_admin_page' ),
            'dashicons-media-document'
        );
    }

    /**
     * Enqueue CSS/JS assets.
     */
    public function enqueue_assets( $hook ) {
        if ( $hook !== 'toplevel_page_filepress' ) {
            return;
        }

        wp_enqueue_style(
            'filepress-style',
            FILEPRESS_URL . 'assets/css/style.css',
            array(),
            FILEPRESS_VERSION
        );

        wp_enqueue_script(
            'filepress-script',
            FILEPRESS_URL . 'assets/js/script.js',
            array( 'jquery' ),
            FILEPRESS_VERSION,
            true
        );
    }

    /**
     * Render the admin page content.
     */
    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'FilePress – File Manager', 'filepress' ); ?></h1>
            <p><?php esc_html_e( 'Welcome to FilePress! Upload, organize, and manage your files right from the WordPress dashboard.', 'filepress' ); ?></p>
            
            <div id="filepress-app">
                <!-- File Manager UI will be rendered here -->
                <p><?php esc_html_e( 'Your file manager interface will appear here.', 'filepress' ); ?></p>
            </div>
        </div>
        <?php
    }
}

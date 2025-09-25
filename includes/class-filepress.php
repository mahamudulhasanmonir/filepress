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

    public function __construct() {
    add_action( 'admin_post_filepress_upload', array( $this, 'handle_file_upload' ) );
}

    public function handle_file_upload() {
        if ( ! isset( $_POST['filepress_nonce'] ) || ! wp_verify_nonce( $_POST['filepress_nonce'], 'filepress_upload' ) ) {
            wp_die( 'Security check failed.' );
        }

        if ( ! current_user_can( 'upload_files' ) ) {
            wp_die( 'You do not have permission to upload files.' );
        }

        if ( ! empty( $_FILES['filepress_file']['name'] ) ) {
            $uploaded_file = $_FILES['filepress_file'];

            $upload_dir = wp_upload_dir();
            $filepress_dir = $upload_dir['basedir'] . '/filepress/';

            if ( ! file_exists( $filepress_dir ) ) {
                wp_mkdir_p( $filepress_dir );
            }

            $target_file = $filepress_dir . basename( $uploaded_file['name'] );

            if ( move_uploaded_file( $uploaded_file['tmp_name'], $target_file ) ) {
                wp_redirect( admin_url( 'admin.php?page=filepress&uploaded=1' ) );
                exit;
            } else {
                wp_die( 'File upload failed.' );
            }
        }
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
            <p><?php esc_html_e( 'Upload, organize, and manage your files right from the WordPress dashboard.', 'filepress' ); ?></p>

            <!-- File Upload Form -->
    <form id="filepress-upload-form" method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
        <input type="hidden" name="action" value="filepress_upload">
        <?php wp_nonce_field( 'filepress_upload', 'filepress_nonce' ); ?>
        <input type="file" name="filepress_file" required>
        <button type="submit" class="filepress-btn"><?php esc_html_e( 'Upload File', 'filepress' ); ?></button>
    </form>


            <hr>

            <!-- File List -->
            <h2><?php esc_html_e( 'Uploaded Files', 'filepress' ); ?></h2>
            <ul>
                <?php
                $upload_dir = wp_upload_dir();
                $filepress_dir = $upload_dir['basedir'] . '/filepress/';

                if ( file_exists( $filepress_dir ) ) {
                    $files = scandir( $filepress_dir );
                    foreach ( $files as $file ) {
                        if ( $file !== '.' && $file !== '..' ) {
                            $file_url = $upload_dir['baseurl'] . '/filepress/' . $file;
                            echo '<li><a href="' . esc_url( $file_url ) . '" target="_blank">' . esc_html( $file ) . '</a></li>';
                        }
                    }
                } else {
                    echo '<li>' . esc_html__( 'No files uploaded yet.', 'filepress' ) . '</li>';
                }
                ?>
            </ul>
        </div>
        <?php
    }
    }
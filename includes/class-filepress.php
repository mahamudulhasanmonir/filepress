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
        add_action( 'admin_post_filepress_delete', array( $this, 'handle_file_delete' ) );
        add_action( 'admin_post_filepress_rename', array( $this, 'handle_file_rename' ) );
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

            // Move the uploaded file
            if ( move_uploaded_file( $uploaded_file['tmp_name'], $target_file ) ) {
                wp_redirect( admin_url( 'admin.php?page=filepress&uploaded=1' ) );
                exit;
            } else {
                wp_die( 'File upload failed.' );
            }
        } else {
            wp_die( 'No file selected.' );
        }
    }


    public function handle_file_delete() {
        if ( ! isset( $_POST['filepress_nonce'] ) || ! wp_verify_nonce( $_POST['filepress_nonce'], 'filepress_action' ) ) {
            wp_die( 'Security check failed.' );
        }

        $upload_dir = wp_upload_dir();
        $filepress_dir = $upload_dir['basedir'] . '/filepress/';
        $file_name = sanitize_file_name( $_POST['file_name'] );
        $file_path = $filepress_dir . $file_name;

        if ( file_exists( $file_path ) ) {
            unlink( $file_path );
        }

        wp_redirect( admin_url( 'admin.php?page=filepress&deleted=1' ) );
        exit;
    }

    public function handle_file_rename() {
        if ( ! isset( $_POST['filepress_nonce'] ) || ! wp_verify_nonce( $_POST['filepress_nonce'], 'filepress_action' ) ) {
            wp_die( 'Security check failed.' );
        }

        $upload_dir = wp_upload_dir();
        $filepress_dir = $upload_dir['basedir'] . '/filepress/';

        $old_name = sanitize_file_name( $_POST['old_name'] );
        $new_name = sanitize_file_name( $_POST['new_name'] );

        $old_path = $filepress_dir . $old_name;
        $new_path = $filepress_dir . $new_name;

        if ( file_exists( $old_path ) && ! file_exists( $new_path ) ) {
            rename( $old_path, $new_path );
        }

        wp_redirect( admin_url( 'admin.php?page=filepress&renamed=1' ) );
        exit;
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
        $upload_dir = wp_upload_dir();
        $filepress_dir = $upload_dir['basedir'] . '/filepress/';
        $filepress_url = $upload_dir['baseurl'] . '/filepress/';

        if ( ! file_exists( $filepress_dir ) ) {
            wp_mkdir_p( $filepress_dir );
        }
        ?>
        <div class="wrap filepress-container">
            <h1>📂 FilePress – File Manager</h1>
            <p>Upload, manage, and organize your files directly from WordPress.</p>

            <!-- Upload Form -->
            <form id="filepress-upload-form" method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                <input type="hidden" name="action" value="filepress_upload">
                <?php wp_nonce_field( 'filepress_upload', 'filepress_nonce' ); ?>
                <input type="file" name="filepress_file" required>
                <button type="submit" class="filepress-btn">Upload File</button>
            </form>


            <hr>

            <!-- File List -->
            <h2>📁 Uploaded Files</h2>
            <table class="filepress-table">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Preview</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $files = scandir( $filepress_dir );
                    $has_files = false;

                    foreach ( $files as $file ) {
                        if ( $file !== '.' && $file !== '..' ) {
                            $has_files = true;
                            $file_url = $filepress_url . $file;
                            ?>
                            <tr>
                                <td><?php echo esc_html( $file ); ?></td>
                                <td>
                                    <?php if ( preg_match( '/\.(jpg|jpeg|png|gif|webp)$/i', $file ) ) : ?>
                                        <img src="<?php echo esc_url( $file_url ); ?>" alt="" class="filepress-thumb">
                                    <?php else : ?>
                                        <a href="<?php echo esc_url( $file_url ); ?>" target="_blank">Open</a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- Rename Form -->
                                    <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" class="inline-form">
                                        <input type="hidden" name="action" value="filepress_rename">
                                        <input type="hidden" name="old_name" value="<?php echo esc_attr( $file ); ?>">
                                        <?php wp_nonce_field( 'filepress_action', 'filepress_nonce' ); ?>
                                        <input type="text" name="new_name" placeholder="Rename to..." required>
                                        <button type="submit" class="filepress-btn small">Rename</button>
                                    </form>

                                    <!-- Delete Form -->
                                    <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" class="inline-form">
                                        <input type="hidden" name="action" value="filepress_delete">
                                        <input type="hidden" name="file_name" value="<?php echo esc_attr( $file ); ?>">
                                        <?php wp_nonce_field( 'filepress_action', 'filepress_nonce' ); ?>
                                        <button type="submit" class="filepress-btn danger small">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }
                    }

                    if ( ! $has_files ) {
                        echo '<tr><td colspan="3">No files uploaded yet.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    }
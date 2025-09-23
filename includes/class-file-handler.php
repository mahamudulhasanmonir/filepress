<?php
'modified' => filemtime( $file_path ),
);
}
}
}


wp_send_json_success( $files );
}


// Delete file
public static function ajax_delete() {
check_ajax_referer( 'filepress_nonce', 'nonce' );
if ( ! current_user_can( 'delete_posts' ) ) {
wp_send_json_error( 'Permission denied' );
}


$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
if ( empty( $name ) ) {
wp_send_json_error( 'Invalid file name' );
}


$uploads = wp_upload_dir();
$file = trailingslashit( $uploads['basedir'] ) . 'filepress/' . $name;


if ( file_exists( $file ) ) {
$deleted = unlink( $file );
if ( $deleted ) {
wp_send_json_success( 'Deleted' );
}
}


wp_send_json_error( 'Could not delete' );
}


// Rename file
public static function ajax_rename() {
check_ajax_referer( 'filepress_nonce', 'nonce' );
if ( ! current_user_can( 'edit_posts' ) ) {
wp_send_json_error( 'Permission denied' );
}


$old = isset( $_POST['old'] ) ? sanitize_text_field( wp_unslash( $_POST['old'] ) ) : '';
$new = isset( $_POST['new'] ) ? sanitize_file_name( wp_unslash( $_POST['new'] ) ) : '';
if ( empty( $old ) || empty( $new ) ) {
wp_send_json_error( 'Invalid parameters' );
}


$uploads = wp_upload_dir();
$dir = trailingslashit( $uploads['basedir'] ) . 'filepress';
$old_path = $dir . '/' . $old;
$new_path = $dir . '/' . $new;


if ( file_exists( $old_path ) && ! file_exists( $new_path ) ) {
$moved = rename( $old_path, $new_path );
if ( $moved ) {
wp_send_json_success( 'Renamed' );
}
}


wp_send_json_error( 'Could not rename' );
}
}
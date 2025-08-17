<?php

defined( 'ABSPATH' ) || exit;

// Delete file via AJAX
add_action( 'wp_ajax_issues_exporter_for_gitlab_delete_csv_file', 'issues_exporter_for_gitlab_delete_csv_file' );
function issues_exporter_for_gitlab_delete_csv_file() {
    // Permission check
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => __( 'Permission denied.', 'issues-exporter-for-gitlab' ) ] );
    }

    // Verify nonce
    check_ajax_referer( 'issues_exporter_for_gitlab_nonce', 'security' );

    // Get and sanitize file name from request
    $file_name = isset( $_POST['file_name'] ) ? sanitize_text_field( wp_unslash( $_POST['file_name'] ) ) : '';
    $file_name = sanitize_file_name( $file_name );

    if ( empty( $file_name ) ) {
        wp_send_json_error( [ 'message' => __( 'Invalid file name.', 'issues-exporter-for-gitlab' ) ] );
    }

    // Get upload directory
    $upload_dir = wp_upload_dir();
    $base_dir   = trailingslashit( $upload_dir['basedir'] ) . 'issues-exporter-for-gitlab';
    $file_path  = trailingslashit( $base_dir ) . $file_name;

    // Validate file path securely
    $real_file = realpath( $file_path );
    $real_base = realpath( $base_dir );

    if ( ! $real_file || ! $real_base || strpos( $real_file, $real_base ) !== 0 ) {
        wp_send_json_error( [ 'message' => __( 'Invalid file path.', 'issues-exporter-for-gitlab' ) ] );
    }

    if ( ! file_exists( $real_file ) ) {
        wp_send_json_error( [ 'message' => __( 'File not found.', 'issues-exporter-for-gitlab' ) ] );
    }

    // Delete file
    if ( wp_delete_file( $real_file ) ) {
        wp_send_json_success( [ 'message' => __( 'File deleted successfully.', 'issues-exporter-for-gitlab' ) ] );
    } else {
        wp_send_json_error( [ 'message' => __( 'Failed to delete file.', 'issues-exporter-for-gitlab' ) ] );
    }
}

// Export GitLab issues via AJAX
add_action( 'wp_ajax_issues_exporter_for_gitlab_export', 'issues_exporter_for_gitlab_export' );
function issues_exporter_for_gitlab_export() {

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => __( 'Permission denied.', 'issues-exporter-for-gitlab' ) ] );
    }

    check_ajax_referer( 'issues_exporter_for_gitlab_nonce', 'security' );

    $page     = isset( $_GET['page'] ) ? absint( $_GET['page'] ) : 1;
    $per_page = isset( $_GET['per_page'] ) ? absint( $_GET['per_page'] ) : 50;
    $csv_file = isset( $_GET['csv_path_file'] ) ? sanitize_text_field( wp_unslash( $_GET['csv_path_file'] ) ) : '';

    $project_id   = get_option( 'issues_exporter_for_gitlab_project_id' );
    $access_token = get_option( 'issues_exporter_for_gitlab_access_token' );

    if ( empty( $project_id ) || empty( $access_token ) ) {
        wp_send_json_error( [ 'message' => __( 'Missing GitLab project ID or access token.', 'issues-exporter-for-gitlab' ) ] );
    }

    //$url = "https://gitlab.com/api/v4/projects/$project_id/issues?per_page=$per_page&page=$page&order_by=created_at";
    $base_url   = 'https://gitlab.com/api/v4/projects/';
    $endpoint   = trailingslashit( $project_id ) . 'issues';

    $args = [
        'per_page' => absint( $per_page ),
        'page'     => absint( $page ),
        'order_by' => 'created_at',
    ];

    // Build query safely
    $query_string = http_build_query( $args, '', '&', PHP_QUERY_RFC3986 );

    // Final API URL
    $url = esc_url_raw( $base_url . $endpoint . '?' . $query_string );

    $response = wp_remote_get( $url, [
        'headers' => [ 'PRIVATE-TOKEN' => $access_token ]
    ] );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( [ 'message' => __( 'Failed to fetch from GitLab.', 'issues-exporter-for-gitlab' ) ] );
    }

    $body       = wp_remote_retrieve_body( $response );
    $records    = json_decode( $body, true );
    $total      = (int) wp_remote_retrieve_header( $response, 'x-total' );
    $totalPages = (int) wp_remote_retrieve_header( $response, 'x-total-pages' );

    $exporter_file = ISSUES_EXPORTER_FOR_GITLAB_PATH . 'includes/exporter.php';
    if ( file_exists( $exporter_file ) ) {
        require_once $exporter_file;
    } else {
        wp_send_json_error( [ 'message' => __( 'Missing exporter.php file.', 'issues-exporter-for-gitlab' ) ] );
    }

    $written = issues_exporter_for_gitlab_write_csv( $csv_file, $records, $page );

    wp_send_json_success([
        'recordsFetched' => $written,
        'totalRecords'   => $total,
        'totalPages'     => $totalPages,
        'hasMore'        => ( $page * $per_page < min( $total, $total ) )
    ]);
}

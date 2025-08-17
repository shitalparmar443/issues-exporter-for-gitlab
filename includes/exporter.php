<?php

defined( 'ABSPATH' ) || exit;

/**
 * Write GitLab issues to CSV/TSV file.
 *
 * @param string $filename File name for export.
 * @param array  $records  Issues data from GitLab.
 * @param int    $page     Current page (used for appending).
 * @return int|WP_Error    Number of records written or WP_Error on failure.
 */
function issues_exporter_for_gitlab_write_csv( $filename, $records, $page ) {
    global $wp_filesystem;

    if ( ! is_array( $records ) ) {
        return new WP_Error( 'invalid_records', __( 'Records must be an array.', 'issues-exporter-for-gitlab' ) );
    }

    // Load WP filesystem functions if needed
    if ( ! function_exists( 'request_filesystem_credentials' ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    // Get filesystem credentials
    $creds = request_filesystem_credentials( '', '', false, false, null );
    if ( false === $creds ) {
        return new WP_Error( 'fs_no_creds', __( 'Unable to get filesystem credentials.', 'issues-exporter-for-gitlab' ) );
    }

    // Initialize filesystem
    if ( ! WP_Filesystem( $creds ) ) {
        return new WP_Error( 'fs_init_failed', __( 'Filesystem initialization failed.', 'issues-exporter-for-gitlab' ) );
    }

    // Prepare export directory
    $upload_dir = wp_upload_dir();
    $export_dir = trailingslashit( $upload_dir['basedir'] ) . 'issues-exporter-for-gitlab';

    if ( ! wp_mkdir_p( $export_dir ) ) {
        return new WP_Error( 'dir_failed', __( 'Failed to create export directory.', 'issues-exporter-for-gitlab' ) );
    }

    // Sanitize file name
    $filename  = sanitize_file_name( $filename );
    $filepath  = trailingslashit( $export_dir ) . $filename;

    $is_first_page = ( absint( $page ) === 1 );

    // Define CSV headers
    $headers = [
        'board_title', 'task_title', 'slug', 'type', 'status', 'stage',
        'source', 'priority', 'description', 'position',
        'started_at', 'due_at', 'archived_at', 'subtasks',
    ];

    $csv_data = '';

    // Add headers on first page
    if ( $is_first_page ) {
        $csv_data .= implode( "\t", array_map( 'sanitize_text_field', $headers ) ) . "\n";
    }

    $count = 0;

    foreach ( $records as $record ) {
        $title       = sanitize_text_field( $record['title'] ?? '' );
        $slug        = sanitize_title( $title ) . '-' . absint( $record['id'] ?? 0 );
        $description = wp_strip_all_tags( $record['description'] ?? '' );
        $status      = ( $record['state'] ?? 'opened' ) === 'opened' ? 'open' : 'closed';
        $labels      = $status === 'closed' ? 'Closed' : sanitize_text_field( $record['labels'][0] ?? '' );

        $row = [
            'MGMT.',
            $title,
            $slug,
            'task',
            $status,
            $labels,
            'GitLab',
            'low',
            $description,
            '',
            ! empty( $record['created_at'] ) ? gmdate( 'Y-m-d H:i:s', strtotime( $record['created_at'] ) ) : '',
            ! empty( $record['due_date'] )   ? gmdate( 'Y-m-d H:i:s', strtotime( $record['due_date'] ) )   : '',
            '',
            '',
        ];

        $csv_data .= implode( "\t", array_map( 'sanitize_text_field', $row ) ) . "\n";
        $count++;
    }

    // Write or append file content
    if ( $is_first_page ) {
        $wp_filesystem->put_contents( $filepath, $csv_data, FS_CHMOD_FILE );
    } else {
        $existing = $wp_filesystem->get_contents( $filepath );
        $wp_filesystem->put_contents( $filepath, $existing . $csv_data, FS_CHMOD_FILE );
    }

    return $count;
}

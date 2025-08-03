<?php

defined( 'ABSPATH' ) || exit;

function issues_exporter_for_gitlab_write_csv( $filename, $records, $page ) {
    global $wp_filesystem;

    if ( ! function_exists( 'request_filesystem_credentials' ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    if ( false === ( $creds = request_filesystem_credentials( '', '', false, false, null ) ) ) {
        return; // Unable to get filesystem credentials
    }

    if ( ! WP_Filesystem( $creds ) ) {
        return; // Filesystem init failed
    }

    $directory = WP_CONTENT_DIR . '/issues-exporter-for-gitlab';
    $filepath  = trailingslashit( $directory ) . $filename;

    if ( ! file_exists( $directory ) ) {
        wp_mkdir_p( $directory );
    }

    $is_first_page = ( $page === 1 );

    // CSV headers
    $headers = [
        'board_title', 'task_title', 'slug', 'type', 'status', 'stage',
        'source', 'priority', 'description', 'position',
        'started_at', 'due_at', 'archived_at', 'subtasks'
    ];

    $csv_data = '';

    // Add headers on first page
    if ( $is_first_page ) {
        $csv_data .= implode( "\t", array_map( 'sanitize_text_field', $headers ) ) . "\n";
    }

    $count = 0;

    foreach ( $records as $record ) {
        $title       = sanitize_text_field( $record['title'] ?? '' );
        $slug        = sanitize_title( $title ) . '-' . ( $record['id'] ?? '' );
        $description = wp_strip_all_tags( $record['description'] ?? '' );
        $status      = ( $record['state'] ?? 'opened' ) === 'opened' ? 'open' : 'closed';
        $labels      = $status === 'closed' ? 'Closed' : ( $record['labels'][0] ?? '' );

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
            ''
        ];

        $csv_data .= implode( "\t", array_map( 'sanitize_text_field', $row ) ) . "\n";
        $count++;
    }

    // Write file content
    if ( $is_first_page ) {
        $wp_filesystem->put_contents( $filepath, $csv_data, FS_CHMOD_FILE );
    } else {
        $existing = $wp_filesystem->get_contents( $filepath );
        $wp_filesystem->put_contents( $filepath, $existing . $csv_data, FS_CHMOD_FILE );
    }

    return $count;
}

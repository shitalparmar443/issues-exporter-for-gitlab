<?php

defined( 'ABSPATH' ) || exit;

function issues_exporter_for_gitlab_admin_page() {
    if ( isset( $_POST['issues_exporter_for_gitlab_submit'] ) && check_admin_referer( 'issues_exporter_for_gitlab_settings' ) ) {

        $iefg_project_id   = isset( $_POST['iefg_project_id'] ) ? sanitize_text_field( wp_unslash( $_POST['iefg_project_id'] ) ) : '';
        $iefg_access_token = isset( $_POST['iefg_access_token'] ) ? sanitize_text_field( wp_unslash( $_POST['iefg_access_token'] ) ) : '';

        update_option( 'issues_exporter_for_gitlab_project_id', $iefg_project_id );
        update_option( 'issues_exporter_for_gitlab_access_token', $iefg_access_token );
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved.', 'issues-exporter-for-gitlab' ) . '</p></div>';
    }

    $iefg_project_id   = esc_attr( get_option( 'issues_exporter_for_gitlab_project_id', '' ) );
    $iefg_access_token = esc_attr( get_option( 'issues_exporter_for_gitlab_access_token', '' ) );

    $export_dir = WP_CONTENT_DIR . '/issues-exporter-for-gitlab';
    $export_url = content_url( '/issues-exporter-for-gitlab' );
    $files      = [];

    if ( file_exists( $export_dir ) ) {
        foreach ( glob( $export_dir . '/*.csv' ) as $file ) {
            $files[] = [
                'name'     => basename( $file ),
                'url'      => $export_url . '/' . basename( $file ),
                'size'     => filesize( $file ),
                'modified' => filemtime( $file ),
            ];
        }

        usort( $files, function( $a, $b ) {
            return $b['modified'] - $a['modified'];
        } );
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Issues Exporter for GitLab Settings', 'issues-exporter-for-gitlab' ); ?></h1>
        <form method="post">
            <?php wp_nonce_field( 'issues_exporter_for_gitlab_settings' ); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'GitLab Project ID', 'issues-exporter-for-gitlab' ); ?></th>
                    <td><input type="text" name="iefg_project_id" value="<?php echo esc_attr( $iefg_project_id ); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'GitLab Access Token', 'issues-exporter-for-gitlab' ); ?></th>
                    <td><input type="text" name="iefg_access_token" value="<?php echo esc_attr( $iefg_access_token ); ?>" class="regular-text" required></td>
                </tr>
            </table>
            <p><input type="submit" name="issues_exporter_for_gitlab_submit" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'issues-exporter-for-gitlab' ); ?>"></p>
        </form>

        <hr>

        <h2><?php esc_html_e( 'Issues Exporter for GitLab', 'issues-exporter-for-gitlab' ); ?></h2>
        <button id="startExport" class="button button-primary"><?php esc_html_e( 'Start Export', 'issues-exporter-for-gitlab' ); ?></button>
        <p><strong><?php esc_html_e( 'Status:', 'issues-exporter-for-gitlab' ); ?></strong> <span id="status"></span></p>
        <p><strong><?php esc_html_e( 'Pages Done:', 'issues-exporter-for-gitlab' ); ?></strong> <span id="pagesDone">0</span></p>
        <p><strong><?php esc_html_e( 'Records Fetched:', 'issues-exporter-for-gitlab' ); ?></strong> <span id="recordsFetched">0</span></p>
        <p><strong><?php esc_html_e( 'Total Records:', 'issues-exporter-for-gitlab' ); ?></strong> <span id="totalRecords">0</span></p>
        <p><strong><?php esc_html_e( 'Total Pages:', 'issues-exporter-for-gitlab' ); ?></strong> <span id="totalPages">0</span></p>

        <?php if ( ! empty( $files ) ) : ?>
            <h2><?php esc_html_e( 'Recently Generated CSV Files', 'issues-exporter-for-gitlab' ); ?></h2>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'File Name', 'issues-exporter-for-gitlab' ); ?></th>
                        <th><?php esc_html_e( 'Size', 'issues-exporter-for-gitlab' ); ?></th>
                        <th><?php esc_html_e( 'Last Modified', 'issues-exporter-for-gitlab' ); ?></th>
                        <th><?php esc_html_e( 'Actions', 'issues-exporter-for-gitlab' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $files as $f ) : ?>
                        <tr>
                            <td><?php echo esc_html( $f['name'] ); ?></td>
                            <td><?php echo esc_html( size_format( filesize( $file ) ) ); ?></td>
                            <td><?php echo esc_html( date_i18n( 'Y-m-d H:i:s', filemtime( $file ) ) ); ?></td>
                            <td>
                                <a class="button button-small" href="<?php echo esc_url( $f['url'] ); ?>" download><?php esc_html_e( 'Download', 'issues-exporter-for-gitlab' ); ?></a>
                                <button class="button button-small issues-exporter-for-gitlab-delete-csv" data-filename="<?php echo esc_attr( $f['name'] ); ?>"><?php esc_html_e( 'Delete', 'issues-exporter-for-gitlab' ); ?></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><em><?php esc_html_e( 'No exported CSV files found.', 'issues-exporter-for-gitlab' ); ?></em></p>
        <?php endif; ?>
    </div>
    <?php
}

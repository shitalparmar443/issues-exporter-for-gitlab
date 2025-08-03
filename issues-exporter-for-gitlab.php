<?php
/**
 * Plugin Name:       Issues Exporter for GitLab
 * Plugin URI:        https://wordpress.org/plugins/issues-exporter-for-gitlab/
 * Description:       Export GitLab issues to CSV with live AJAX progress, file listing, and cleanup.
 * Version:           1.0.0
 * Author:            Shitalben Parmar
 * Author URI:        https://profiles.wordpress.org/shitalparmar443/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       issues-exporter-for-gitlab
 * Domain Path:       /languages
 *
 * @package IssuesExporterForGitLab
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Plugin Constants.
if ( ! defined( 'ISSUES_EXPORTER_FOR_GITLAB_PATH' ) ) {
    define( 'ISSUES_EXPORTER_FOR_GITLAB_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'ISSUES_EXPORTER_FOR_GITLAB_URL' ) ) {
    define( 'ISSUES_EXPORTER_FOR_GITLAB_URL', plugin_dir_url( __FILE__ ) );
}

// Include Required Files.
require_once ISSUES_EXPORTER_FOR_GITLAB_PATH . 'includes/admin-page.php';
require_once ISSUES_EXPORTER_FOR_GITLAB_PATH . 'includes/ajax-handlers.php';
require_once ISSUES_EXPORTER_FOR_GITLAB_PATH . 'includes/exporter.php';

/**
 * Register the GitLab Exporter menu page.
 */
function issues_exporter_for_gitlab_add_menu() {
    add_menu_page(
        __( 'Issues Exporter for GitLab', 'issues-exporter-for-gitlab' ),
        __( 'Issues Exporter for GitLab', 'issues-exporter-for-gitlab' ),
        'manage_options',
        'issues-exporter-for-gitlab',
        'issues_exporter_for_gitlab_admin_page',
        'dashicons-download',
        80
    );
}
add_action( 'admin_menu', 'issues_exporter_for_gitlab_add_menu' );

/**
 * Enqueue admin scripts and styles.
 *
 * @param string $hook The current admin page.
 */
function issues_exporter_for_gitlab_enqueue_admin_scripts( $hook ) {
    if ( 'toplevel_page_issues-exporter-for-gitlab' !== $hook ) {
        return;
    }

    wp_enqueue_script(
        'issues-exporter-for-gitlab-js',
        ISSUES_EXPORTER_FOR_GITLAB_URL . 'assets/js/admin-issues-exporter-for-gitlab.js',
        array( 'jquery' ),
        '1.1.0',
        true
    );

    wp_localize_script(
        'issues-exporter-for-gitlab-js',
        'ISSUES_EXPORTER_FOR_GITLAB_JS',
        array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'issues_exporter_for_gitlab_nonce' ),
        )
    );
}
add_action( 'admin_enqueue_scripts', 'issues_exporter_for_gitlab_enqueue_admin_scripts' );

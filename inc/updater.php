<?php
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'DAYANARC_GITHUB_RAW', 'https://raw.githubusercontent.com/mazaheri/dayanarc-theme/master/style.css' );
define( 'DAYANARC_GITHUB_ZIP', 'https://api.github.com/repos/mazaheri/dayanarc-theme/zipball/master' );

// ── Check GitHub for a newer version ─────────────────────────────────────────
function dayanarc_check_for_update( $transient ) {
    if ( empty( $transient->checked ) ) return $transient;

    $theme_slug      = get_option( 'stylesheet' );
    $current_version = wp_get_theme()->get( 'Version' );
    $latest_version  = dayanarc_get_github_version();

    if ( $latest_version && version_compare( $latest_version, $current_version, '>' ) ) {
        $transient->response[ $theme_slug ] = [
            'theme'       => $theme_slug,
            'new_version' => $latest_version,
            'url'         => 'https://github.com/mazaheri/dayanarc-theme',
            'package'     => DAYANARC_GITHUB_ZIP,
        ];
    }

    return $transient;
}
add_filter( 'pre_set_site_transient_update_themes', 'dayanarc_check_for_update' );

// ── Fetch remote version from GitHub style.css (cached 12 h) ─────────────────
function dayanarc_get_github_version() {
    $cached = get_transient( 'dayanarc_github_version' );
    if ( $cached ) return $cached;

    $response = wp_remote_get( DAYANARC_GITHUB_RAW, [
        'timeout' => 10,
        'headers' => [ 'Accept' => 'text/plain' ],
    ] );

    if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
        return false;
    }

    $body = wp_remote_retrieve_body( $response );
    preg_match( '/^Version:\s*(.+)$/mi', $body, $matches );
    $version = isset( $matches[1] ) ? trim( $matches[1] ) : false;

    if ( $version ) {
        set_transient( 'dayanarc_github_version', $version, 12 * HOUR_IN_SECONDS );
    }

    return $version;
}

// ── Clear version cache whenever the theme is updated ────────────────────────
function dayanarc_clear_version_cache() {
    delete_transient( 'dayanarc_github_version' );
}
add_action( 'upgrader_process_complete', 'dayanarc_clear_version_cache' );

// ── Show update notice in Appearance > Themes ─────────────────────────────────
function dayanarc_update_message( $theme_data, $response ) {
    if ( ! empty( $response['new_version'] ) ) {
        echo '<br><strong>' .
            sprintf(
                esc_html__( 'Version %s is available on GitHub. Install the update above.', 'dayanarc' ),
                esc_html( $response['new_version'] )
            ) .
        '</strong>';
    }
}
add_action( 'in_theme_update_message-' . get_option( 'stylesheet' ), 'dayanarc_update_message', 10, 2 );

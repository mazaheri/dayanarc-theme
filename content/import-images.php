<?php
/**
 * Image Import Script — Dayan Arc Theme
 *
 * Detects changed files via MD5 hash — only re-imports when a file actually changed.
 * Safe to run repeatedly: unchanged files are skipped, changed files replace the old attachment.
 *
 * Run with:
 *   studio wp eval-file wp-content/themes/theme/content/import-images.php
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * FOLDER CONVENTION
 * ─────────────────────────────────────────────────────────────────────────────
 * content/images/
 *   hero/
 *     bg.jpg
 *   about/
 *     main.jpg
 *     detail.jpg
 *   our-service/
 *     image-1.jpg
 *     image-2.jpg
 *   services/
 *     architecture/thumbnail.jpg
 *     industrial-warehouse/thumbnail.jpg
 *     structural-engineering/thumbnail.jpg
 *     mep-smart-systems/thumbnail.jpg
 *     facade-landscape/thumbnail.jpg
 *     technical-coordination/thumbnail.jpg
 *   portfolio/
 *     georgia/          ← first image = cover, rest = gallery
 *     gcc/
 *     germany/
 * ─────────────────────────────────────────────────────────────────────────────
 */

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$content_dir = __DIR__ . '/';

// ══ SMART IMPORT HELPER ══════════════════════════════════════════════════════
//
// Returns attachment ID. Skips if hash unchanged, replaces if changed.

function dayanarc_smart_import( $full_path ) {
    if ( ! file_exists( $full_path ) ) {
        return new WP_Error( 'not_found', "File not found: $full_path" );
    }

    $hash      = md5_file( $full_path );
    $filename  = basename( $full_path );
    $norm_path = wp_normalize_path( $full_path );

    // Look for an existing attachment with this source path
    $existing = get_posts( [
        'post_type'      => 'attachment',
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'meta_query'     => [ [
            'key'   => '_source_file_path',
            'value' => $norm_path,
        ] ],
    ] );

    if ( $existing ) {
        $att_id       = $existing[0]->ID;
        $stored_hash  = get_post_meta( $att_id, '_source_file_hash', true );

        if ( $stored_hash === $hash ) {
            return [ 'id' => $att_id, 'status' => 'skipped' ];
        }

        // Hash changed — delete old attachment and re-import
        wp_delete_attachment( $att_id, true );
    }

    // Import fresh
    $upload = wp_upload_bits( $filename, null, file_get_contents( $full_path ) );
    if ( ! empty( $upload['error'] ) ) {
        return new WP_Error( 'upload_failed', $upload['error'] );
    }

    $mime   = wp_check_filetype( $upload['file'] );
    $att_id = wp_insert_attachment( [
        'guid'           => $upload['url'],
        'post_mime_type' => $mime['type'],
        'post_title'     => sanitize_file_name( $filename ),
        'post_status'    => 'inherit',
    ], $upload['file'] );

    if ( is_wp_error( $att_id ) ) {
        return $att_id;
    }

    wp_update_attachment_metadata( $att_id, wp_generate_attachment_metadata( $att_id, $upload['file'] ) );

    update_post_meta( $att_id, '_source_file_path', $norm_path );
    update_post_meta( $att_id, '_source_file_hash', $hash );

    return [ 'id' => $att_id, 'status' => 'imported' ];
}

// ══ IMAGE MAP ════════════════════════════════════════════════════════════════
//
// Types:
//   'theme_mod'   → set_theme_mod( key, attachment_url )
//   'logo_mod'    → set_theme_mod( key, attachment_id )   ← for header/footer logos
//   'thumbnail'   → set_post_thumbnail( post from option, attachment_id )

$image_map = [

    // Logos (stored as attachment IDs in theme mods)
    [
        'file' => 'images/logos/header.jpg',
        'type' => 'logo_mod',
        'key'  => 'header_logo_id',
    ],
    [
        'file' => 'images/logos/footer.jpg',
        'type' => 'logo_mod',
        'key'  => 'footer_logo_id',
    ],

    // About section
    [
        'file' => 'images/about/main.jpg',
        'type' => 'theme_mod',
        'key'  => 'about_image_main',
    ],
    [
        'file' => 'images/about/detail.jpg',
        'type' => 'theme_mod',
        'key'  => 'about_image_detail',
    ],

    // Our Service section (homepage section 3)
    [
        'file' => 'images/our-service/image-1.jpg',
        'type' => 'theme_mod',
        'key'  => 'our_service_image_1',
    ],
    [
        'file' => 'images/our-service/image-2.png',
        'type' => 'theme_mod',
        'key'  => 'our_service_image_2',
    ],

    // Service thumbnails — homepage cards + service detail pages
    [
        'file'   => 'images/services/architecture/thumbnail.png',
        'type'   => 'thumbnail',
        'option' => 'dayanarc_service_architecture_id',
    ],
    [
        'file'   => 'images/services/industrial-warehouse/thumbnail.png',
        'type'   => 'thumbnail',
        'option' => 'dayanarc_service_interior_design_id',
    ],
    [
        'file'   => 'images/services/structural-engineering/thumbnail.png',
        'type'   => 'thumbnail',
        'option' => 'dayanarc_service_3d_viz_id',
    ],
    [
        'file'   => 'images/services/mep-smart-systems/thumbnail.png',
        'type'   => 'thumbnail',
        'option' => 'dayanarc_service_project_mgmt_id',
    ],
    [
        'file'   => 'images/services/facade-landscape/thumbnail.png',
        'type'   => 'thumbnail',
        'option' => 'dayanarc_service_5_id',
    ],
    [
        'file'   => 'images/services/technical-coordination/thumbnail.png',
        'type'   => 'thumbnail',
        'option' => 'dayanarc_service_6_id',
    ],

];

// ══ PORTFOLIO MAP ════════════════════════════════════════════════════════════
// Each folder: images sorted alphabetically → first = cover, rest = gallery.

$portfolio_map = [
    [
        'folder'  => 'images/portfolio/georgia/',
        'post_id' => 63,
        'label'   => 'Georgia Residence',
    ],
    [
        'folder'  => 'images/portfolio/gcc/',
        'post_id' => 72,
        'label'   => 'GCC Pavilion',
    ],
    [
        'folder'  => 'images/portfolio/germany/',
        'post_id' => 80,
        'label'   => 'Germany Office HQ',
    ],
];

// ══ APPLY — THEME MODS + THUMBNAILS ══════════════════════════════════════════

echo "\n=== Importing images ===\n\n";

$counts = [ 'imported' => 0, 'skipped' => 0, 'missing' => 0, 'error' => 0 ];

foreach ( $image_map as $entry ) {
    $full_path = $content_dir . $entry['file'];

    if ( ! file_exists( $full_path ) ) {
        echo "  – Skip (missing): {$entry['file']}\n";
        $counts['missing']++;
        continue;
    }

    $result = dayanarc_smart_import( $full_path );

    if ( is_wp_error( $result ) ) {
        echo "  ✗ Error: {$entry['file']} — " . $result->get_error_message() . "\n";
        $counts['error']++;
        continue;
    }

    $att_id = $result['id'];
    $status = $result['status'];
    $counts[ $status ]++;

    if ( $status === 'skipped' ) {
        echo "  ○ Unchanged: {$entry['file']}\n";
        // Still re-assign in case the destination changed
    }

    // Assign to destination
    if ( $entry['type'] === 'logo_mod' ) {
        set_theme_mod( $entry['key'], $att_id );
        $verb = $status === 'imported' ? '✓' : '↺';
        echo "  $verb Logo ID [{$entry['key']} = $att_id]\n";

    } elseif ( $entry['type'] === 'theme_mod' ) {
        $url = wp_get_attachment_url( $att_id );
        set_theme_mod( $entry['key'], $url );
        $verb = $status === 'imported' ? '✓' : '↺';
        echo "  $verb Theme mod [{$entry['key']}]\n";

    } elseif ( $entry['type'] === 'thumbnail' ) {
        $post_id = (int) get_option( $entry['option'] );
        if ( $post_id && get_post( $post_id ) ) {
            set_post_thumbnail( $post_id, $att_id );
            $verb = $status === 'imported' ? '✓' : '↺';
            echo "  $verb Thumbnail [{$entry['option']} → post $post_id]\n";
        } else {
            echo "  ✗ Post not found for option: {$entry['option']}\n";
            $counts['error']++;
        }
    }
}

// ══ APPLY — PORTFOLIO GALLERIES ══════════════════════════════════════════════

echo "\n--- Portfolio ---\n\n";

$image_exts = [ 'jpg', 'jpeg', 'png', 'webp', 'gif' ];

foreach ( $portfolio_map as $project ) {
    $folder   = $content_dir . $project['folder'];
    $post_id  = $project['post_id'];
    $label    = $project['label'];

    if ( ! is_dir( $folder ) ) {
        echo "  – Skip (folder missing): {$project['folder']}\n";
        continue;
    }

    if ( ! get_post( $post_id ) ) {
        echo "  ✗ Portfolio post not found: $label (ID $post_id)\n";
        continue;
    }

    // Collect + sort image files
    $files = [];
    foreach ( scandir( $folder ) as $f ) {
        $ext = strtolower( pathinfo( $f, PATHINFO_EXTENSION ) );
        if ( in_array( $ext, $image_exts, true ) ) {
            $files[] = $folder . $f;
        }
    }
    sort( $files );

    if ( empty( $files ) ) {
        echo "  – Skip (no images): {$project['folder']}\n";
        continue;
    }

    $att_ids    = [];
    $imported_c = 0;
    $skipped_c  = 0;

    foreach ( $files as $fp ) {
        $result = dayanarc_smart_import( $fp );
        if ( is_wp_error( $result ) ) {
            echo "  ✗ Error: " . basename( $fp ) . " — " . $result->get_error_message() . "\n";
            continue;
        }
        $att_ids[] = $result['id'];
        if ( $result['status'] === 'imported' ) $imported_c++;
        else $skipped_c++;
    }

    if ( empty( $att_ids ) ) continue;

    // First image = featured (cover), rest = gallery
    set_post_thumbnail( $post_id, $att_ids[0] );
    $gallery = array_slice( $att_ids, 1 );
    update_post_meta( $post_id, '_portfolio_gallery', json_encode( $gallery ) );

    echo "  ✓ $label — {$imported_c} imported, {$skipped_c} unchanged (" . count($att_ids) . " total)\n";
    $counts['imported'] += $imported_c;
    $counts['skipped']  += $skipped_c;
}

// ══ SUMMARY ══════════════════════════════════════════════════════════════════

echo "\n=== Done — imported: {$counts['imported']}, unchanged: {$counts['skipped']}, missing: {$counts['missing']}, errors: {$counts['error']} ===\n";

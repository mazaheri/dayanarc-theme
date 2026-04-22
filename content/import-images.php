<?php
/**
 * Image Import Script — Dayan Arc Theme
 *
 * Place image files in content/images/ following the folder conventions below,
 * then run ONE command to import everything and assign to the right places:
 *
 *   studio wp eval-file wp-content/themes/theme/content/import-images.php
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * FOLDER CONVENTION
 * ─────────────────────────────────────────────────────────────────────────────
 * content/images/
 *   hero/
 *     bg.jpg                ← hero background (if used as theme mod)
 *   about/
 *     main.jpg              ← large about image (left column)
 *     detail.jpg            ← small about image (right column, desktop only)
 *   services/
 *     architecture/
 *       thumbnail.jpg       ← service card + page featured image
 *     interior-design/
 *       thumbnail.jpg
 *     3d-visualization/
 *       thumbnail.jpg
 *     project-management/
 *       thumbnail.jpg
 *   portfolio/
 *     (project images go here — uploaded via WP Admin → Portfolio)
 *   journal/
 *     (post featured images go here — uploaded per post in WP Admin)
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * HOW TO TELL CLAUDE TO UPDATE IMAGES
 * ─────────────────────────────────────────────────────────────────────────────
 * "I've placed a new about main image at content/images/about/main.jpg.
 *  Import and assign it."
 *
 * Claude will:
 *  1. Verify the file exists
 *  2. Run: studio wp eval-file wp-content/themes/theme/content/import-images.php
 *  3. Confirm the assignment
 *
 * Each run only imports files that EXIST in the folder.
 * Missing files are skipped with a notice — no errors.
 * Running twice imports duplicates — remove old files after confirming.
 * ─────────────────────────────────────────────────────────────────────────────
 */

// ══ IMAGE MAP ════════════════════════════════════════════════════════════════
// Each entry maps a local file path (relative to this script's directory)
// to a destination: either a Customizer theme mod or a post thumbnail.
//
// Types:
//   'theme_mod'  → set_theme_mod( key, attachment_url )
//   'thumbnail'  → set_post_thumbnail( post_id_from_option, attachment_id )

$image_map = [

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

    // Service page thumbnails (used on homepage cards AND service detail pages)
    [
        'file'   => 'images/services/architecture/thumbnail.jpg',
        'type'   => 'thumbnail',
        'option' => 'dayanarc_service_architecture_id',
    ],
    [
        'file'   => 'images/services/interior-design/thumbnail.jpg',
        'type'   => 'thumbnail',
        'option' => 'dayanarc_service_interior_design_id',
    ],
    [
        'file'   => 'images/services/3d-visualization/thumbnail.jpg',
        'type'   => 'thumbnail',
        'option' => 'dayanarc_service_3d_viz_id',
    ],
    [
        'file'   => 'images/services/project-management/thumbnail.jpg',
        'type'   => 'thumbnail',
        'option' => 'dayanarc_service_project_mgmt_id',
    ],

];

// ══ APPLY ════════════════════════════════════════════════════════════════════

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$content_dir = __DIR__ . '/';

echo "\n=== Importing images ===\n\n";

foreach ( $image_map as $entry ) {

    $full_path = $content_dir . $entry['file'];

    if ( ! file_exists( $full_path ) ) {
        echo "- Skipped (file not found): {$entry['file']}\n";
        continue;
    }

    // Copy into WordPress uploads directory
    $upload = wp_upload_bits( basename( $full_path ), null, file_get_contents( $full_path ) );

    if ( ! empty( $upload['error'] ) ) {
        echo "✗ Upload failed: {$entry['file']} — {$upload['error']}\n";
        continue;
    }

    $mime = wp_check_filetype( $upload['file'] );

    $att_id = wp_insert_attachment( [
        'guid'           => $upload['url'],
        'post_mime_type' => $mime['type'],
        'post_title'     => sanitize_file_name( basename( $full_path ) ),
        'post_status'    => 'inherit',
    ], $upload['file'] );

    if ( is_wp_error( $att_id ) ) {
        echo "✗ Attachment failed: {$entry['file']} — " . $att_id->get_error_message() . "\n";
        continue;
    }

    wp_update_attachment_metadata( $att_id, wp_generate_attachment_metadata( $att_id, $upload['file'] ) );

    // Assign to destination
    if ( $entry['type'] === 'theme_mod' ) {
        set_theme_mod( $entry['key'], $upload['url'] );
        echo "✓ Theme mod set: {$entry['key']}\n";

    } elseif ( $entry['type'] === 'thumbnail' ) {
        $post_id = (int) get_option( $entry['option'] );
        if ( $post_id && get_post( $post_id ) ) {
            set_post_thumbnail( $post_id, $att_id );
            echo "✓ Thumbnail set: {$entry['option']} (post $post_id)\n";
        } else {
            echo "✗ Post not found for option: {$entry['option']}\n";
        }
    }
}

echo "\n=== Done ===\n";

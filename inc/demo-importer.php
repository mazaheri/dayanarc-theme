<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// ── Admin menu page ───────────────────────────────────────────────────────────
function dayanarc_demo_menu() {
    add_theme_page(
        'Import Demo Content',
        'Import Demo',
        'manage_options',
        'dayanarc-demo-import',
        'dayanarc_demo_page'
    );
}
add_action( 'admin_menu', 'dayanarc_demo_menu' );

function dayanarc_demo_page() {
    $imported = false;
    $reset    = false;
    $errors   = [];

    if ( isset( $_POST['dayanarc_run_import'] ) && check_admin_referer( 'dayanarc_import_nonce' ) ) {
        $result = dayanarc_run_import();
        if ( is_wp_error( $result ) ) {
            $errors[] = $result->get_error_message();
        } else {
            $imported = true;
        }
    }

    if ( isset( $_POST['dayanarc_run_reset'] ) && check_admin_referer( 'dayanarc_import_nonce' ) ) {
        dayanarc_reset_content();
        $result = dayanarc_run_import();
        if ( is_wp_error( $result ) ) {
            $errors[] = $result->get_error_message();
        } else {
            $reset = true;
        }
    }
    ?>
    <div class="wrap">
        <h1 style="font-family:Georgia,serif;">🏛 Dayan Arc — Import &amp; Sync Content</h1>

        <?php if ( $imported ) : ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>Import complete!</strong>
                   <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">View your site →</a>
                </p>
            </div>
        <?php endif; ?>

        <?php if ( $reset ) : ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>Reset &amp; reimport complete!</strong> All content was cleared and reimported from scratch.
                   <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">View your site →</a>
                </p>
            </div>
        <?php endif; ?>

        <?php foreach ( $errors as $err ) : ?>
            <div class="notice notice-error"><p><?php echo esc_html( $err ); ?></p></div>
        <?php endforeach; ?>

        <div style="max-width:640px; margin-top:1.5rem;">
            <p>This will create and update the following content:</p>
            <ul style="list-style:disc; margin-left:2rem; line-height:2;">
                <li><strong>Logos</strong> — header (black bg) &amp; footer (green bg) from <code>content/images/logos/</code></li>
                <li><strong>About &amp; Our Service images</strong> from <code>content/images/</code></li>
                <li><strong>3 Portfolio projects</strong> with gallery images (Georgia, GCC, Germany)</li>
                <li><strong>3 Blog/Journal posts</strong> with featured images</li>
                <li><strong>Home page</strong> set as the static front page</li>
                <li><strong>Journal page</strong> set as the blog posts page</li>
                <li><strong>Portfolio page</strong> at /portfolio/</li>
                <li><strong>Contact Form 7</strong> form created</li>
                <li><strong>Contact page</strong> at /contact/</li>
                <li><strong>6 Service pages</strong>: Architectural &amp; Interior Design, Industrial Sheds &amp; Warehouses, Structural Engineering, MEP &amp; Smart Systems, Facade &amp; Landscape Architecture, Technical Coordination &amp; Project Consultancy</li>
                <li><strong>All content theme mods</strong> — headings, descriptions, social links, contact info</li>
                <li><strong>Primary navigation menu</strong> with all links</li>
            </ul>

            <!-- Normal import -->
            <p style="color:#1a5c2e; background:#d4edda; padding:.75rem 1rem; border-left:4px solid #28a745; margin-top:1.5rem;">
                <strong>Import / Sync</strong> — unchanged images are skipped (MD5 hash detection). Changed files are re-imported automatically. Safe to run anytime.
            </p>
            <form method="post" style="margin-top:.75rem;">
                <?php wp_nonce_field( 'dayanarc_import_nonce' ); ?>
                <?php submit_button( 'Import / Sync Content', 'primary large', 'dayanarc_run_import', false ); ?>
            </form>

            <!-- Reset + reimport -->
            <p style="color:#856404; background:#fff3cd; padding:.75rem 1rem; border-left:4px solid #ffc107; margin-top:2rem;">
                <strong>Reset &amp; Reimport Everything</strong> — clears all stored IDs and theme mod values, then runs a full fresh import. Use this if something looks wrong or out of sync.
            </p>
            <form method="post" style="margin-top:.75rem;" onsubmit="return confirm('This will clear all stored content IDs and theme settings, then reimport everything. Continue?');">
                <?php wp_nonce_field( 'dayanarc_import_nonce' ); ?>
                <?php submit_button( 'Reset & Reimport Everything', 'secondary large', 'dayanarc_run_reset', false, [ 'style' => 'background:#856404;color:#fff;border-color:#856404;' ] ); ?>
            </form>
        </div>
    </div>
    <?php
}

// ── Main import runner ────────────────────────────────────────────────────────
function dayanarc_run_import() {
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    // Journal post asset images (project*.png from /assets/)
    $image_ids = dayanarc_import_images();

    // Static pages
    dayanarc_import_home_page();
    dayanarc_import_journal_page();
    dayanarc_import_portfolio_page();
    dayanarc_import_contact_form();
    dayanarc_import_contact_page();

    // Service pages must exist before thumbnails are assigned
    dayanarc_import_service_pages();

    // Logos, about, our-service images, and service thumbnails
    dayanarc_import_content_images();

    // Portfolio projects with folder-based gallery import
    dayanarc_import_portfolio();

    // Journal posts
    dayanarc_import_journal_posts( $image_ids );

    // All text/URL theme mods from manifest
    dayanarc_apply_content_theme_mods();

    // Nav menu (runs last so all page URLs are available)
    dayanarc_import_nav_menu();

    flush_rewrite_rules( false );
    update_option( 'dayanarc_flush_rewrites_pending', '1' );

    return true;
}

// ── Reset: clear all stored IDs and theme mods ────────────────────────────────
// Called before a fresh reimport so nothing is treated as "already done".
function dayanarc_reset_content() {
    // Clear all option IDs stored by the importer
    $options = [
        'dayanarc_service_architecture_id',
        'dayanarc_service_interior_design_id',
        'dayanarc_service_3d_viz_id',
        'dayanarc_service_project_mgmt_id',
        'dayanarc_service_5_id',
        'dayanarc_service_6_id',
        'dayanarc_portfolio_georgia_id',
        'dayanarc_portfolio_gcc_id',
        'dayanarc_portfolio_germany_id',
        'dayanarc_contact_form_id',
        'dayanarc_contact_page_id',
        'dayanarc_portfolio_page_id',
    ];
    foreach ( $options as $opt ) {
        delete_option( $opt );
    }

    // Clear all theme mods set by the importer
    $mods = [
        'header_logo_id', 'footer_logo_id',
        'about_image_main', 'about_image_detail',
        'our_service_image_1', 'our_service_image_2',
        'hero_word_1', 'hero_word_2', 'hero_word_3', 'hero_cta_label', 'hero_tagline',
        'about_heading_line1', 'about_heading_line2', 'about_cta_label', 'about_body',
        'our_service_heading', 'our_service_description',
        'our_service_image_1_desc', 'our_service_image_2_desc',
        'portfolio_heading',
        'services_heading_line1', 'services_heading_line2', 'services_cta_label',
        'services_intro', 'services_tagline',
        'journal_heading',
        'fp_contact_heading_line1', 'fp_contact_heading_line2', 'fp_contact_description',
        'contact_page_heading', 'contact_page_description',
        'footer_tagline', 'contact_location', 'contact_email', 'contact_website',
        'social_instagram', 'social_linkedin', 'social_facebook',
        'social_phone', 'social_whatsapp',
    ];
    foreach ( $mods as $mod ) {
        remove_theme_mod( $mod );
    }
}

// ── Smart import: hash-based, skips unchanged files ──────────────────────────
// Returns [ 'id' => int, 'status' => 'imported'|'skipped' ] or WP_Error.
function dayanarc_smart_import_file( $full_path ) {
    if ( ! file_exists( $full_path ) ) {
        return new WP_Error( 'not_found', "File not found: $full_path" );
    }

    $hash      = md5_file( $full_path );
    $norm_path = wp_normalize_path( $full_path );

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
        $att_id = $existing[0]->ID;
        if ( get_post_meta( $att_id, '_source_file_hash', true ) === $hash ) {
            return [ 'id' => $att_id, 'status' => 'skipped' ];
        }
        // Hash changed — delete old and re-import
        wp_delete_attachment( $att_id, true );
    }

    $filename = basename( $full_path );
    $upload   = wp_upload_bits( $filename, null, file_get_contents( $full_path ) );
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

    if ( is_wp_error( $att_id ) ) return $att_id;

    wp_update_attachment_metadata( $att_id, wp_generate_attachment_metadata( $att_id, $upload['file'] ) );
    update_post_meta( $att_id, '_source_file_path', $norm_path );
    update_post_meta( $att_id, '_source_file_hash', $hash );

    return [ 'id' => $att_id, 'status' => 'imported' ];
}

// ── Legacy import helper (used for journal post thumbnails only) ──────────────
function dayanarc_import_image_file( $src_path, $key ) {
    if ( ! file_exists( $src_path ) ) return 0;

    $existing = new WP_Query( [
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'meta_key'       => '_dayanarc_source_file',
        'meta_value'     => $key,
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ] );
    if ( $existing->have_posts() ) return $existing->posts[0];

    $upload_dir = wp_upload_dir();
    $filename   = sanitize_file_name( basename( $src_path ) );
    $dest       = $upload_dir['path'] . '/' . $filename;

    $n = 1;
    while ( file_exists( $dest ) ) {
        $info = pathinfo( $filename );
        $dest = $upload_dir['path'] . '/' . $info['filename'] . '-' . $n . '.' . $info['extension'];
        $n++;
    }

    if ( ! @copy( $src_path, $dest ) ) return 0;

    $mime      = wp_check_filetype( basename( $dest ) );
    $attach_id = wp_insert_attachment( [
        'post_mime_type' => $mime['type'],
        'post_title'     => pathinfo( $key, PATHINFO_FILENAME ),
        'post_status'    => 'inherit',
    ], $dest );

    if ( is_wp_error( $attach_id ) ) return 0;

    wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $dest ) );
    update_post_meta( $attach_id, '_dayanarc_source_file', $key );

    return (int) $attach_id;
}

// ── 1. Upload theme asset images (journal post thumbnails) ────────────────────
function dayanarc_import_images() {
    $theme_dir = get_template_directory();
    $ids       = [];

    foreach ( [ 'project1.png', 'project2.png', 'project3.png', 'interior1.jpg', 'interior2.jpg' ] as $filename ) {
        $id = dayanarc_import_image_file( $theme_dir . '/assets/' . $filename, $filename );
        if ( $id ) $ids[ $filename ] = $id;
    }

    return $ids;
}

// ── Import content/images/ — logos, about, our-service, service thumbnails ────
function dayanarc_import_content_images() {
    $content_dir = get_template_directory() . '/content/images/';

    // Logos — stored as attachment IDs in theme mods
    foreach ( [
        [ 'file' => 'logos/header.jpg', 'key' => 'header_logo_id' ],
        [ 'file' => 'logos/footer.jpg', 'key' => 'footer_logo_id' ],
    ] as $entry ) {
        $result = dayanarc_smart_import_file( $content_dir . $entry['file'] );
        if ( ! is_wp_error( $result ) ) {
            set_theme_mod( $entry['key'], $result['id'] );
        }
    }

    // About + Our Service images — stored as URLs in theme mods
    foreach ( [
        [ 'file' => 'about/main.jpg',         'key' => 'about_image_main' ],
        [ 'file' => 'about/detail.jpg',        'key' => 'about_image_detail' ],
        [ 'file' => 'our-service/image-1.jpg', 'key' => 'our_service_image_1' ],
        [ 'file' => 'our-service/image-2.png', 'key' => 'our_service_image_2' ],
    ] as $entry ) {
        $result = dayanarc_smart_import_file( $content_dir . $entry['file'] );
        if ( ! is_wp_error( $result ) ) {
            set_theme_mod( $entry['key'], wp_get_attachment_url( $result['id'] ) );
        }
    }

    // Service thumbnails — set as post featured images
    foreach ( [
        [ 'folder' => 'services/architecture/',           'option' => 'dayanarc_service_architecture_id' ],
        [ 'folder' => 'services/industrial-warehouse/',   'option' => 'dayanarc_service_interior_design_id' ],
        [ 'folder' => 'services/structural-engineering/', 'option' => 'dayanarc_service_3d_viz_id' ],
        [ 'folder' => 'services/mep-smart-systems/',      'option' => 'dayanarc_service_project_mgmt_id' ],
        [ 'folder' => 'services/facade-landscape/',       'option' => 'dayanarc_service_5_id' ],
        [ 'folder' => 'services/technical-coordination/', 'option' => 'dayanarc_service_6_id' ],
    ] as $entry ) {
        $result = dayanarc_smart_import_file( $content_dir . $entry['folder'] . 'thumbnail.png' );
        if ( ! is_wp_error( $result ) ) {
            $post_id = (int) get_option( $entry['option'] );
            if ( $post_id && get_post( $post_id ) ) {
                set_post_thumbnail( $post_id, $result['id'] );
            }
        }
    }
}

// ── 2. Portfolio projects ─────────────────────────────────────────────────────
// Finds or creates each CPT post, then imports images from content/images/portfolio/
// First image in folder = cover, rest = gallery.
function dayanarc_import_portfolio() {
    $content_dir = get_template_directory() . '/content/images/';
    $image_exts  = [ 'jpg', 'jpeg', 'png', 'webp', 'gif' ];

    $items = [
        [
            'title'    => 'Georgia Residence',
            'content'  => 'A refined residential project in Tbilisi blending contemporary design with local architectural traditions. Dayan Arc delivered the full scope from concept through execution, creating warm and livable spaces that honour both the setting and the client\'s lifestyle.',
            'excerpt'  => 'A refined residential project blending contemporary design with local architectural traditions.',
            'location' => 'Tbilisi, Georgia',
            'concept'  => 'Full residential design and fit-out',
            'palette'  => 'Warm neutrals, natural stone, textured plaster',
            'folder'   => 'portfolio/georgia/',
            'option'   => 'dayanarc_portfolio_georgia_id',
        ],
        [
            'title'    => 'GCC Pavilion',
            'content'  => 'A landmark hospitality and commercial pavilion designed from concept to execution with precision and care. Every detail — from the structural skin to the interior finishes — was orchestrated to create an immersive and memorable guest experience.',
            'excerpt'  => 'A landmark hospitality space designed from concept to execution with precision and care.',
            'location' => 'GCC Region',
            'concept'  => 'Complete hospitality design and fit-out',
            'palette'  => 'Monochromatic accents, reflective surfaces, deep tones',
            'folder'   => 'portfolio/gcc/',
            'option'   => 'dayanarc_portfolio_gcc_id',
        ],
        [
            'title'    => 'Germany Office HQ',
            'content'  => 'A modern office headquarters in Berlin that balances open collaboration with focused workspaces. The design draws on Bauhaus proportions while integrating contemporary materials and biophilic elements for a productive and inspiring environment.',
            'excerpt'  => 'A modern office design that balances open collaboration with focused work environments.',
            'location' => 'Berlin, Germany',
            'concept'  => 'Office and workplace design',
            'palette'  => 'Concrete grey, warm oak, matte black accents',
            'folder'   => 'portfolio/germany/',
            'option'   => 'dayanarc_portfolio_germany_id',
        ],
    ];

    foreach ( $items as $item ) {
        // Find or create portfolio post
        $post_id = (int) get_option( $item['option'], 0 );

        if ( ! $post_id || ! get_post( $post_id ) ) {
            $q = new WP_Query( [
                'post_type'      => 'portfolio',
                'title'          => $item['title'],
                'post_status'    => 'any',
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'no_found_rows'  => true,
            ] );
            $post_id = $q->have_posts() ? $q->posts[0] : 0;

            if ( ! $post_id ) {
                $post_id = wp_insert_post( [
                    'post_title'   => $item['title'],
                    'post_content' => $item['content'],
                    'post_excerpt' => $item['excerpt'],
                    'post_status'  => 'publish',
                    'post_type'    => 'portfolio',
                ] );
            }

            if ( ! $post_id || is_wp_error( $post_id ) ) continue;
            update_option( $item['option'], $post_id );
        }

        // Always update meta
        update_post_meta( $post_id, '_portfolio_location', $item['location'] );
        update_post_meta( $post_id, '_portfolio_concept',  $item['concept'] );
        update_post_meta( $post_id, '_portfolio_palette',  $item['palette'] );

        // Import images from folder
        $folder = $content_dir . $item['folder'];
        if ( ! is_dir( $folder ) ) continue;

        $files = [];
        foreach ( scandir( $folder ) as $f ) {
            $ext = strtolower( pathinfo( $f, PATHINFO_EXTENSION ) );
            if ( in_array( $ext, $image_exts, true ) ) {
                $files[] = $folder . $f;
            }
        }
        sort( $files );

        if ( empty( $files ) ) continue;

        $att_ids = [];
        foreach ( $files as $fp ) {
            $result = dayanarc_smart_import_file( $fp );
            if ( ! is_wp_error( $result ) ) {
                $att_ids[] = $result['id'];
            }
        }

        if ( ! empty( $att_ids ) ) {
            set_post_thumbnail( $post_id, $att_ids[0] );
            update_post_meta( $post_id, '_portfolio_gallery', json_encode( array_slice( $att_ids, 1 ) ) );
        }
    }
}

// ── 3. Blog posts (journal — 3 posts) ────────────────────────────────────────
function dayanarc_import_journal_posts( $image_ids ) {
    $posts = [
        [
            'title'   => 'Design Excellence',
            'content' => 'Design is the art of creating solutions that blend form with function, bringing innovation and beauty to everyday spaces. At Dayan Arc, excellence is not an aspiration — it is our baseline. Every project begins with a deep dive into the client\'s world, their habits, their values, and the way they move through space. Only then do we begin to design.',
            'image'   => 'project1.png',
        ],
        [
            'title'   => 'Modern Living',
            'content' => 'Creating spaces that elevate everyday living through thoughtful design, where every element serves a purpose and adds to the overall harmony. The modern home is not defined by a style — it is defined by how well it serves the people who live in it. We design for the morning rush, the quiet evening, the weekend gathering, and everything in between.',
            'image'   => 'project2.png',
        ],
        [
            'title'   => 'Functional Luxury',
            'content' => 'Balancing high-end aesthetics with practical functionality — where beauty and purpose coexist seamlessly. True luxury is not about price. It is about the feeling of a space that anticipates your needs, materials that age beautifully, and details so considered that they become invisible. This is the standard we hold ourselves to on every project.',
            'image'   => 'project3.png',
        ],
    ];

    foreach ( $posts as $post ) {
        if ( dayanarc_post_exists( $post['title'], 'post' ) ) continue;

        $post_id = wp_insert_post( [
            'post_title'   => $post['title'],
            'post_content' => $post['content'],
            'post_excerpt' => wp_trim_words( $post['content'], 25, '...' ),
            'post_status'  => 'publish',
            'post_type'    => 'post',
        ] );
        if ( is_wp_error( $post_id ) ) continue;

        if ( isset( $image_ids[ $post['image'] ] ) ) {
            set_post_thumbnail( $post_id, $image_ids[ $post['image'] ] );
        }
    }
}

// ── 4. Home page + static front page ─────────────────────────────────────────
function dayanarc_import_home_page() {
    $existing_front = (int) get_option( 'page_on_front' );
    if ( $existing_front && get_post( $existing_front ) ) return;

    if ( dayanarc_post_exists( 'Home', 'page' ) ) {
        $q = new WP_Query( [
            'post_type'      => 'page',
            'title'          => 'Home',
            'post_status'    => 'any',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ] );
        if ( $q->have_posts() ) {
            update_option( 'show_on_front', 'page' );
            update_option( 'page_on_front', $q->posts[0] );
        }
        return;
    }

    $home_id = wp_insert_post( [
        'post_title'   => 'Home',
        'post_content' => '',
        'post_status'  => 'publish',
        'post_type'    => 'page',
    ] );

    if ( ! is_wp_error( $home_id ) ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $home_id );
    }
}

// ── 5. Journal page + set as blog posts page ──────────────────────────────────
function dayanarc_import_journal_page() {
    $existing_posts_page = (int) get_option( 'page_for_posts' );
    if ( $existing_posts_page && get_post( $existing_posts_page ) ) return;

    $q = new WP_Query( [
        'post_type'      => 'page',
        'title'          => 'Journal',
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ] );

    if ( $q->have_posts() ) {
        $journal_id = $q->posts[0];
    } else {
        $journal_id = wp_insert_post( [
            'post_title'   => 'Journal',
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ] );
        if ( is_wp_error( $journal_id ) ) return;
    }

    update_option( 'page_for_posts', $journal_id );
    update_option( 'show_on_front', 'page' );
}

// ── 5b. Portfolio page ────────────────────────────────────────────────────────
function dayanarc_import_portfolio_page() {
    $stored_id = (int) get_option( 'dayanarc_portfolio_page_id', 0 );
    if ( $stored_id ) {
        $p = get_post( $stored_id );
        if ( $p ) {
            if ( $p->post_status === 'trash' ) wp_untrash_post( $stored_id );
            update_post_meta( $stored_id, '_wp_page_template', 'page-portfolio.php' );
            return $stored_id;
        }
    }

    $q = new WP_Query( [
        'post_type'      => 'page',
        'title'          => 'Portfolio',
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ] );
    if ( $q->have_posts() ) {
        $id = $q->posts[0];
        $p  = get_post( $id );
        if ( $p && $p->post_status === 'trash' ) wp_untrash_post( $id );
        update_post_meta( $id, '_wp_page_template', 'page-portfolio.php' );
        update_option( 'dayanarc_portfolio_page_id', $id );
        return $id;
    }

    $page_id = wp_insert_post( [
        'post_title'   => 'Portfolio',
        'post_content' => '',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_name'    => 'portfolio',
    ] );

    if ( is_wp_error( $page_id ) || ! $page_id ) return 0;

    update_post_meta( $page_id, '_wp_page_template', 'page-portfolio.php' );
    update_option( 'dayanarc_portfolio_page_id', $page_id );

    return $page_id;
}

// ── 6. Contact Form 7 form ────────────────────────────────────────────────────
function dayanarc_import_contact_form() {
    $existing_id = (int) get_option( 'dayanarc_contact_form_id', 0 );
    if ( $existing_id && get_post( $existing_id ) && get_post_type( $existing_id ) === 'wpcf7_contact_form' ) {
        return $existing_id;
    }

    if ( ! post_type_exists( 'wpcf7_contact_form' ) ) return 0;

    $form_id = wp_insert_post( [
        'post_title'  => 'Dayan Arc Contact',
        'post_type'   => 'wpcf7_contact_form',
        'post_status' => 'publish',
        'post_name'   => 'dayan-arc-contact',
    ] );

    if ( is_wp_error( $form_id ) || ! $form_id ) return 0;

    $form_body = '<div class="grid grid-cols-1 md:grid-cols-2 gap-10">
<div>[text* your-name class:form-input placeholder "Name"]</div>
<div>[tel your-phone class:form-input placeholder "Phone"]</div>
</div>
<div>[email* your-email class:form-input placeholder "Email"]</div>
<div>[textarea* your-message rows:5 class:form-textarea placeholder "Message"]</div>
<div style="margin-top:0.25rem;">
<button type="submit" style="border:none;background:transparent;cursor:pointer;padding:0;margin:0;display:inline-flex;align-items:center;gap:0.75rem;">
<span style="font-size:11px;text-transform:uppercase;letter-spacing:0.15em;font-weight:600;color:#231f20;">SEND REQUEST</span>
<svg width="16" height="10" viewBox="0 0 16 10" fill="none" stroke="#231f20" stroke-width="1.2"><path d="M11 1L15 5M15 5L11 9M15 5H0" stroke-linecap="round" stroke-linejoin="round"/></svg>
</button>
</div>';

    $mail = [
        'active'             => true,
        'recipient'          => '[_site_admin_email]',
        'sender'             => get_bloginfo( 'name' ) . ' <[_site_admin_email]>',
        'subject'            => 'New contact request from [your-name]',
        'body'               => "Name: [your-name]\nPhone: [your-phone]\nEmail: [your-email]\n\nMessage:\n[your-message]\n\n---\nSent from: [_site_title] ([_site_url])",
        'additional_headers' => 'Reply-To: [your-email]',
        'attachments'        => '',
        'use_html'           => false,
        'exclude_blank'      => false,
    ];

    $mail_2 = [
        'active'             => false,
        'recipient'          => '',
        'sender'             => '',
        'subject'            => '',
        'body'               => '',
        'additional_headers' => '',
        'attachments'        => '',
        'use_html'           => false,
        'exclude_blank'      => false,
    ];

    update_post_meta( $form_id, '_form',                $form_body );
    update_post_meta( $form_id, '_mail',                $mail );
    update_post_meta( $form_id, '_mail_2',              $mail_2 );
    update_post_meta( $form_id, '_messages',            [] );
    update_post_meta( $form_id, '_additional_settings', '' );
    update_option( 'dayanarc_contact_form_id', $form_id );

    return $form_id;
}

// ── 7. Contact page ───────────────────────────────────────────────────────────
function dayanarc_import_contact_page() {
    $existing_id = (int) get_option( 'dayanarc_contact_page_id', 0 );
    if ( $existing_id && get_post( $existing_id ) ) return $existing_id;

    $q = new WP_Query( [
        'post_type'      => 'page',
        'title'          => 'Contact',
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ] );

    if ( $q->have_posts() ) {
        $id = $q->posts[0];
        update_post_meta( $id, '_wp_page_template', 'page-contact.php' );
        update_option( 'dayanarc_contact_page_id', $id );
        return $id;
    }

    $page_id = wp_insert_post( [
        'post_title'   => 'Contact',
        'post_content' => '',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_name'    => 'contact',
    ] );

    if ( is_wp_error( $page_id ) || ! $page_id ) return 0;

    update_post_meta( $page_id, '_wp_page_template', 'page-contact.php' );
    update_option( 'dayanarc_contact_page_id', $page_id );

    return $page_id;
}

// ── 8. Service pages (all 6) ──────────────────────────────────────────────────
function dayanarc_import_service_pages() {
    $services = [
        [
            'title'            => 'Architectural & Interior Design',
            'old_titles'       => [ 'Architecture', 'Residential Excellence' ],
            'slug'             => 'architecture',
            'option'           => 'dayanarc_service_architecture_id',
            'card_description' => 'From concept to detail, we design modern spaces that reflect lifestyle and precision—covering everything from form to interiors and custom elements.',
            'card_label'       => '',
            'features'         => "Concept Development\nSchematic Design\nDesign Development\nInterior Styling & Layouts\nCustom Furniture Design\nMaterial Selection and FF&E",
            'content'          => 'At Dayan Arc, our architectural and interior design services span the full design journey — from initial concept through schematic design, design development, and construction documentation. We work closely with each client to understand their vision, delivering spaces that are both beautiful and precisely functional.',
        ],
        [
            'title'            => 'Industrial Sheds & Warehouses',
            'old_titles'       => [ 'Interior Design', 'Commercial & Hospitality' ],
            'slug'             => 'industrial-warehouse',
            'option'           => 'dayanarc_service_interior_design_id',
            'card_description' => 'End-to-end design and execution of wide-span industrial structures, optimized for cost, efficiency, and smart space integration.',
            'card_label'       => '',
            'features'         => "Wide-Span Structural Engineering\nSteel Structure Execution\nMezzanine & Office Integration\nMaterial Optimization\nDesign & Planning\nTurnkey Industrial Solutions",
            'content'          => 'We deliver end-to-end design and execution of wide-span industrial structures. From warehouses to manufacturing facilities, our solutions are optimized for cost, operational efficiency, and smart space integration.',
        ],
        [
            'title'            => 'Structural Engineering',
            'old_titles'       => [ '3D Visualization', 'Public & Institutional' ],
            'slug'             => 'structural-engineering',
            'option'           => 'dayanarc_service_3d_viz_id',
            'card_description' => 'Robust steel and concrete structures designed for safety, efficiency, and full alignment with architectural vision.',
            'card_label'       => '',
            'features'         => "Concrete Structure Analysis & Design\nSteel Frame Engineering & Design\nSeismic & Lateral Load Analysis\nStructural Retrofitting\nSpecialized Foundation Design\nTechnical Calculation Reports",
            'content'          => 'Our structural engineering team delivers robust steel and concrete structures designed for safety, efficiency, and full alignment with architectural vision. Every project is backed by rigorous analysis and technical precision.',
        ],
        [
            'title'            => 'MEP & Smart Systems',
            'old_titles'       => [ 'Project Management', 'Infrastructure & Large-Scale' ],
            'slug'             => 'mep-smart-systems',
            'option'           => 'dayanarc_service_project_mgmt_id',
            'card_description' => 'Integrated mechanical, electrical, and plumbing systems with energy-efficient design and smart building technologies.',
            'card_label'       => '',
            'features'         => "HVAC System Design\nElectrical & Lighting Infrastructure\nPlumbing & Sanitary Engineering\nBuilding Management Systems (BMS)\nFire Protection Systems\nEnergy Efficiency Analysis",
            'content'          => 'We design and integrate mechanical, electrical, and plumbing systems with energy-efficient thinking and smart building technologies. Our MEP solutions are coordinated seamlessly with architecture and structure for efficient, comfortable buildings.',
        ],
        [
            'title'            => 'Facade & Landscape Architecture',
            'old_titles'       => [],
            'slug'             => 'facade-landscape',
            'option'           => 'dayanarc_service_5_id',
            'card_description' => 'Harmonized facade and landscape solutions combining aesthetics, materials, lighting, and environmental integration.',
            'card_label'       => '',
            'features'         => "Facade Engineering\nExterior Lighting Design\nLandscape & Hardscape Design\nWater Features & Pool Design\nClimate-Appropriate Planting\nFacade Structural Detailing",
            'content'          => 'Our facade and landscape practice creates harmonized exterior environments that combine aesthetics, durable materials, thoughtful lighting, and environmental sensitivity — designed as an extension of the building\'s architectural identity.',
        ],
        [
            'title'            => 'Technical Coordination & Project Consultancy',
            'old_titles'       => [],
            'slug'             => 'technical-coordination',
            'option'           => 'dayanarc_service_6_id',
            'card_description' => 'Seamless coordination across disciplines with expert consultancy to ensure accuracy, efficiency, and flawless project delivery.',
            'card_label'       => '',
            'features'         => "Interdisciplinary Coordination\nTechnical Engineering Consultancy\nCode & Standards Compliance\nConstruction Documentation Management\nDesign Supervision\nValue Engineering",
            'content'          => 'Great projects are built on seamless coordination. Our technical coordination and consultancy service bridges every discipline — architecture, structure, MEP, facade — ensuring accuracy, efficiency, and flawless delivery from design through handover.',
        ],
    ];

    foreach ( $services as $svc ) {
        $page_id = (int) get_option( $svc['option'], 0 );

        if ( $page_id && get_post( $page_id ) ) {
            wp_update_post( [ 'ID' => $page_id, 'post_title' => $svc['title'] ] );
        } else {
            $page_id      = 0;
            $search_titles = array_merge( [ $svc['title'] ], $svc['old_titles'] );

            foreach ( $search_titles as $search_title ) {
                $q = new WP_Query( [
                    'post_type'      => 'page',
                    'title'          => $search_title,
                    'post_status'    => 'any',
                    'posts_per_page' => 1,
                    'fields'         => 'ids',
                    'no_found_rows'  => true,
                ] );
                if ( $q->have_posts() ) {
                    $page_id = $q->posts[0];
                    wp_update_post( [ 'ID' => $page_id, 'post_title' => $svc['title'] ] );
                    break;
                }
            }

            if ( ! $page_id ) {
                $page_id = wp_insert_post( [
                    'post_title'   => $svc['title'],
                    'post_content' => $svc['content'],
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_name'    => $svc['slug'],
                ] );
            }
        }

        if ( ! $page_id || is_wp_error( $page_id ) ) continue;

        update_post_meta( $page_id, '_wp_page_template',         'page-service.php' );
        update_post_meta( $page_id, '_service_card_description', $svc['card_description'] );
        update_post_meta( $page_id, '_service_card_tagline',     $svc['card_label'] );
        update_post_meta( $page_id, '_service_what_we_offer',    'WHAT WE OFFER' );
        update_post_meta( $page_id, '_service_cta_heading',      'READY TO START YOUR PROJECT?' );
        update_post_meta( $page_id, '_service_cta_description',  "Let's discuss your vision and bring it to life with the expertise and care that defines Dayan Arc." );
        update_post_meta( $page_id, '_service_cta_label',        'CONTACT US' );
        update_post_meta( $page_id, '_service_features',         $svc['features'] );

        update_option( $svc['option'], $page_id );
    }
}

// ── 9. Apply all text/URL theme mods (mirrors content/manifest.php) ───────────
function dayanarc_apply_content_theme_mods() {
    $mods = [
        // Hero
        'hero_word_1'              => 'VISION.',
        'hero_word_2'              => 'DESIGN.',
        'hero_word_3'              => 'REALITY.',
        'hero_cta_label'           => 'Get in touch',
        'hero_tagline'             => 'At Dayan Arc, we blend creativity and expertise to craft exceptional architectural and interior design experiences. From concept to completion, we bring spaces to life with innovation, precision, and a passion for design excellence.',
        // About
        'about_heading_line1'      => 'A VISION BEYOND',
        'about_heading_line2'      => 'BORDERS',
        'about_cta_label'          => 'GET IN TOUCH',
        'about_body'               => 'At Dayan Arc, we believe that architecture is more than just designing structures; it is the art of crafting experiences and building legacies. With over 20 years of expertise and a track record of more than 400 global projects, my team and I have bridged the gap between German engineering precision and creative luxury. From our strategic hubs in Germany, Dubai, and Georgia, we personally ensure that every project — whether a bespoke villa or a complex international airport — meets the highest global standards of excellence.',
        // Our Service section
        'our_service_heading'      => 'WHAT WE DO',
        'our_service_description'  => 'From architectural vision to flawless execution — our integrated services cover every discipline, every scale, and every geography.',
        'our_service_image_1_desc' => 'We turn your vision into reality through innovative design and precise planning—creating residential and commercial spaces that balance beauty and functionality.',
        'our_service_image_2_desc' => 'We deliver advanced engineering solutions, from complex structures to large-scale facilities, ensuring efficiency, durability, and precision.',
        // Portfolio section
        'portfolio_heading'        => 'OUR PROJECTS',
        // Services section
        'services_heading_line1'   => 'CORE DESIGN',
        'services_heading_line2'   => 'CONCEPTS',
        'services_cta_label'       => 'GET IN TOUCH',
        'services_intro'           => 'Our integrated design services are applied across a diverse range of sectors, ensuring that every concept — from private luxury to public infrastructure — is executed with unrivaled precision and global standards.',
        'services_tagline'         => 'Transforming ideas into inspiring, functional spaces.',
        // Journal section
        'journal_heading'          => 'LATEST PROJECTS',
        // Homepage contact section
        'fp_contact_heading_line1' => "LET'S BEGIN A",
        'fp_contact_heading_line2' => 'CONVERSATION',
        'fp_contact_description'   => "Tell us more about your space, your ideas, and your aspirations. We'll guide you through the next steps with care and intention.",
        // Contact page
        'contact_page_heading'     => "LET'S BEGIN A CONVERSATION",
        'contact_page_description' => "Tell us more about your space, your ideas, and your aspirations. We'll guide you through the next steps with care and intention.",
        // Footer / Brand
        'footer_tagline'           => 'Bringing together creativity, expertise, and passion to deliver exceptional design solutions.',
        'contact_location'         => 'Business Bay, Dubai, UAE',
        'contact_email'            => 'support@dayanarc.com',
        'contact_website'          => 'http://dayanarc.com',
        // Social
        'social_instagram'         => 'https://www.instagram.com/dayan.arc.co',
        'social_linkedin'          => 'https://www.linkedin.com/company/dayanarc-de/?viewAsMember=true',
        'social_facebook'          => 'https://www.facebook.com/share/1B5ciXyKgT/?mibextid=wwXIfr',
        'social_phone'             => '+971564160061',
        'social_whatsapp'          => '+971564160061',
    ];

    foreach ( $mods as $key => $value ) {
        set_theme_mod( $key, $value );
    }
}

// ── Nav menu ──────────────────────────────────────────────────────────────────
function dayanarc_import_nav_menu() {
    $menu_name     = 'Primary Menu';
    $portfolio_url = dayanarc_portfolio_url();
    $journal_id    = (int) get_option( 'page_for_posts' );
    $journal_url   = $journal_id ? get_permalink( $journal_id ) : home_url( '/journal/' );
    $contact_url   = dayanarc_contact_page_url();

    $desired_urls = [
        'Portfolio' => $portfolio_url,
        'Journal'   => $journal_url,
        'Contact'   => $contact_url,
    ];

    $existing_menu = wp_get_nav_menu_object( $menu_name );

    if ( $existing_menu ) {
        $items = wp_get_nav_menu_items( $existing_menu->term_id );
        if ( $items ) {
            foreach ( $items as $item ) {
                if ( isset( $desired_urls[ $item->title ] ) && $item->url !== $desired_urls[ $item->title ] ) {
                    wp_update_nav_menu_item( $existing_menu->term_id, $item->ID, [
                        'menu-item-title'  => $item->title,
                        'menu-item-url'    => $desired_urls[ $item->title ],
                        'menu-item-status' => 'publish',
                        'menu-item-type'   => 'custom',
                    ] );
                }
            }
        }
        return;
    }

    $menu_id = wp_create_nav_menu( $menu_name );
    if ( is_wp_error( $menu_id ) ) return;

    foreach ( [
        [ 'label' => 'About Us',  'url' => home_url( '/' ) ],
        [ 'label' => 'Portfolio', 'url' => $portfolio_url ],
        [ 'label' => 'Services',  'url' => home_url( '/' ) ],
        [ 'label' => 'Journal',   'url' => $journal_url ],
        [ 'label' => 'Contact',   'url' => $contact_url ],
    ] as $item ) {
        wp_update_nav_menu_item( $menu_id, 0, [
            'menu-item-title'  => $item['label'],
            'menu-item-url'    => $item['url'],
            'menu-item-status' => 'publish',
            'menu-item-type'   => 'custom',
        ] );
    }

    $locations            = get_theme_mod( 'nav_menu_locations', [] );
    $locations['primary'] = $menu_id;
    set_theme_mod( 'nav_menu_locations', $locations );
}

// ── Helper: check if post exists by title ─────────────────────────────────────
function dayanarc_post_exists( $title, $post_type ) {
    $q = new WP_Query( [
        'post_type'              => $post_type,
        'title'                  => $title,
        'post_status'            => 'any',
        'posts_per_page'         => 1,
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ] );
    return $q->have_posts();
}

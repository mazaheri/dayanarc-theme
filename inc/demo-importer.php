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
    $errors   = [];

    if ( isset( $_POST['dayanarc_run_import'] ) && check_admin_referer( 'dayanarc_import_nonce' ) ) {
        $result = dayanarc_run_import();
        if ( is_wp_error( $result ) ) {
            $errors[] = $result->get_error_message();
        } else {
            $imported = true;
        }
    }
    ?>
    <div class="wrap">
        <h1 style="font-family:Georgia,serif;">🏛 Dayan Arc — Import Demo Content</h1>

        <?php if ( $imported ) : ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>Demo content imported successfully!</strong>
                   <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">View your site →</a>
                </p>
            </div>
        <?php endif; ?>

        <?php foreach ( $errors as $err ) : ?>
            <div class="notice notice-error"><p><?php echo esc_html( $err ); ?></p></div>
        <?php endforeach; ?>

        <div style="max-width:600px; margin-top:1.5rem;">
            <p>This will create the following demo content in the database:</p>
            <ul style="list-style:disc; margin-left:2rem; line-height:2;">
                <li><strong>4 Portfolio projects</strong> with gallery images (Georgia, GCC, Germany, Studio)</li>
                <li><strong>3 Blog/Journal posts</strong> with featured images</li>
                <li><strong>Home page</strong> set as the static front page</li>
                <li><strong>Journal page</strong> set as the blog posts page</li>
                <li><strong>Portfolio page</strong> at /portfolio/ with portfolio listing template</li>
                <li><strong>Contact Form 7</strong> form created (configure reCAPTCHA v3 keys in Contact → Integration)</li>
                <li><strong>Contact page</strong> at /contact/</li>
                <li><strong>4 Service pages</strong>: Architecture, Interior Design, 3D Visualization, Project Management</li>
                <li><strong>Primary navigation menu</strong> with all links</li>
            </ul>
            <p style="color:#856404; background:#fff3cd; padding:.75rem 1rem; border-left:4px solid #ffc107; margin-top:1rem;">
                Existing posts/pages with the same title will be skipped — safe to run more than once.
            </p>
            <form method="post" style="margin-top:1.5rem;">
                <?php wp_nonce_field( 'dayanarc_import_nonce' ); ?>
                <?php submit_button( 'Import Demo Content', 'primary large', 'dayanarc_run_import', false ); ?>
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

    // 1. Upload images
    $image_ids = dayanarc_import_images();

    // 2. Portfolio items
    dayanarc_import_portfolio( $image_ids );

    // 3. Journal posts
    dayanarc_import_journal_posts( $image_ids );

    // 4. Home page + static front page
    dayanarc_import_home_page();

    // 5. Journal page + blog posts page setting
    dayanarc_import_journal_page();

    // 5b. Portfolio page
    dayanarc_import_portfolio_page();

    // 6. Contact Form 7 form
    dayanarc_import_contact_form();

    // 7. Contact page
    dayanarc_import_contact_page();

    // 8. Service pages
    dayanarc_import_service_pages( $image_ids );

    // 9. Apply all text theme mods from content manifest
    dayanarc_apply_content_theme_mods();

    // 10. Import content/images/ (about, our-service thumbnails)
    dayanarc_import_content_images();

    // 11. Primary nav menu (runs last so Contact URL is available)
    dayanarc_import_nav_menu();

    // Rebuild rewrite rules so /portfolio/ archive URL resolves immediately.
    flush_rewrite_rules( false );
    // Set flag so the next page load also flushes (more reliable in Studio/Playground).
    update_option( 'dayanarc_flush_rewrites_pending', '1' );

    return true;
}

// ── Helper: import one image from any absolute path ──────────────────────────
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

    // Avoid filename collisions
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

    $metadata = wp_generate_attachment_metadata( $attach_id, $dest );
    wp_update_attachment_metadata( $attach_id, $metadata );
    update_post_meta( $attach_id, '_dayanarc_source_file', $key );

    return (int) $attach_id;
}

// ── 1. Upload theme asset images to Media Library ─────────────────────────────
function dayanarc_import_images() {
    $theme_dir = get_template_directory();
    $ids       = [];

    $files = [
        'project1.png', 'project2.png', 'project3.png', 'project4.png',
        'project5.png', 'project6.png', 'project7.png', 'project8.png',
        'project9.png', 'project10.png', 'project11.png',
        'interior1.jpg', 'interior2.jpg',
    ];

    foreach ( $files as $filename ) {
        $id = dayanarc_import_image_file( $theme_dir . '/assets/' . $filename, $filename );
        if ( $id ) $ids[ $filename ] = $id;
    }

    return $ids;
}

// ── 2. Portfolio items (4 projects from sample images) ───────────────────────
function dayanarc_import_portfolio( $image_ids ) {
    $sample = ABSPATH . 'Dayan Arc website/sample/';

    $items = [
        [
            'title'    => 'Georgia Residence',
            'content'  => 'A refined residential project in Tbilisi blending contemporary design with local architectural traditions. Dayan Arc delivered the full scope from concept through execution, creating warm and livable spaces that honour both the setting and the client\'s lifestyle.',
            'excerpt'  => 'A refined residential project blending contemporary design with local architectural traditions.',
            'location' => 'Tbilisi, Georgia',
            'concept'  => 'Full residential design and fit-out',
            'palette'  => 'Warm neutrals, natural stone, textured plaster',
            'thumb'    => $sample . 'Georgia/Georgia 01.jpg',
            'thumb_key'=> 'georgia-01',
            'gallery'  => [
                [ $sample . 'Georgia/Georgia 02.jpg', 'georgia-02' ],
                [ $sample . 'Georgia/Georgia 03.jpg', 'georgia-03' ],
                [ $sample . 'Georgia/Georgia 04.jpg', 'georgia-04' ],
                [ $sample . 'Georgia/Georgia 05.jpg', 'georgia-05' ],
                [ $sample . 'Georgia/Georgia 06.jpg', 'georgia-06' ],
                [ $sample . 'Georgia/Georgia 07.jpg', 'georgia-07' ],
                [ $sample . 'Georgia/Georgia 08.jpg', 'georgia-08' ],
            ],
        ],
        [
            'title'    => 'GCC Pavilion',
            'content'  => 'A landmark hospitality and commercial pavilion designed from concept to execution with precision and care. Every detail — from the structural skin to the interior finishes — was orchestrated to create an immersive and memorable guest experience.',
            'excerpt'  => 'A landmark hospitality space designed from concept to execution with precision and care.',
            'location' => 'GCC Region',
            'concept'  => 'Complete hospitality design and fit-out',
            'palette'  => 'Monochromatic accents, reflective surfaces, deep tones',
            'thumb'    => $sample . 'gcc final/gcc 1.jpg',
            'thumb_key'=> 'gcc-01',
            'gallery'  => [
                [ $sample . 'gcc final/gcc 2.jpg', 'gcc-02' ],
                [ $sample . 'gcc final/gcc 4.jpg', 'gcc-04' ],
                [ $sample . 'gcc final/gcc 5.jpg', 'gcc-05' ],
                [ $sample . 'gcc final/gcc 6.jpg', 'gcc-06' ],
                [ $sample . 'gcc final/gcc 7.jpg', 'gcc-07' ],
                [ $sample . 'gcc final/gcc 8.jpg', 'gcc-08' ],
            ],
        ],
        [
            'title'    => 'Germany Office HQ',
            'content'  => 'A modern office headquarters in Berlin that balances open collaboration with focused workspaces. The design draws on Bauhaus proportions while integrating contemporary materials and biophilic elements for a productive and inspiring environment.',
            'excerpt'  => 'A modern office design that balances open collaboration with focused work environments.',
            'location' => 'Berlin, Germany',
            'concept'  => 'Office and workplace design',
            'palette'  => 'Concrete grey, warm oak, matte black accents',
            'thumb'    => $sample . 'germany/Germany 01.jpg',
            'thumb_key'=> 'germany-01',
            'gallery'  => [
                [ $sample . 'germany/Germany 02.jpg', 'germany-02' ],
                [ $sample . 'germany/Germany 03.jpg', 'germany-03' ],
                [ $sample . 'germany/Germany 04.jpg', 'germany-04' ],
            ],
        ],
        [
            'title'    => 'Modern Interior Studio',
            'content'  => 'Transforming a raw open-plan space into a warm, functional creative studio. The brief called for a space that would inspire without distracting — achieved through a restrained material palette, considered lighting, and custom joinery.',
            'excerpt'  => 'Transforming a raw space into a warm, functional studio that reflects the client\'s creative identity.',
            'location' => 'Dubai, UAE',
            'concept'  => 'Studio and creative workspace design',
            'palette'  => 'Linen white, brushed brass, warm timber',
            'thumb'    => null,
            'thumb_key'=> null,
            'thumb_id' => isset( $image_ids['interior1.jpg'] ) ? $image_ids['interior1.jpg'] : 0,
            'gallery'  => [],
        ],
    ];

    foreach ( $items as $item ) {
        if ( dayanarc_post_exists( $item['title'], 'portfolio' ) ) continue;

        $post_id = wp_insert_post( [
            'post_title'   => $item['title'],
            'post_content' => $item['content'],
            'post_excerpt' => $item['excerpt'],
            'post_status'  => 'publish',
            'post_type'    => 'portfolio',
        ] );
        if ( is_wp_error( $post_id ) ) continue;

        update_post_meta( $post_id, '_portfolio_location', $item['location'] );
        update_post_meta( $post_id, '_portfolio_concept',  $item['concept'] );
        update_post_meta( $post_id, '_portfolio_palette',  $item['palette'] );

        // Featured image
        if ( ! empty( $item['thumb_id'] ) ) {
            set_post_thumbnail( $post_id, $item['thumb_id'] );
        } elseif ( $item['thumb'] ) {
            $thumb_id = dayanarc_import_image_file( $item['thumb'], $item['thumb_key'] );
            if ( $thumb_id ) set_post_thumbnail( $post_id, $thumb_id );
        }

        // Gallery images
        $gallery_ids = [];
        foreach ( $item['gallery'] as $g ) {
            $gid = dayanarc_import_image_file( $g[0], $g[1] );
            if ( $gid ) $gallery_ids[] = $gid;
        }
        if ( ! empty( $gallery_ids ) ) {
            update_post_meta( $post_id, '_portfolio_gallery', wp_json_encode( $gallery_ids ) );
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
    // Already configured
    $existing_front = (int) get_option( 'page_on_front' );
    if ( $existing_front && get_post( $existing_front ) ) return;

    if ( dayanarc_post_exists( 'Home', 'page' ) ) {
        // Page exists but option not set — look it up and re-apply
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

// ── 5. Journal page + set as blog posts page ─────────────────────────────────
function dayanarc_import_journal_page() {
    // Already configured to a valid page — skip
    $existing_posts_page = (int) get_option( 'page_for_posts' );
    if ( $existing_posts_page && get_post( $existing_posts_page ) ) return;

    // Find or create the Journal page
    $journal_id = null;

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
    // Ensure static front page mode is still active
    update_option( 'show_on_front', 'page' );
}

// ── 5b. Portfolio page ────────────────────────────────────────────────────────
function dayanarc_import_portfolio_page() {
    $existing_id = (int) get_option( 'dayanarc_portfolio_page_id', 0 );
    if ( $existing_id && get_post( $existing_id ) ) return $existing_id;

    if ( dayanarc_post_exists( 'Portfolio', 'page' ) ) {
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
            update_post_meta( $id, '_wp_page_template', 'page-portfolio.php' );
            update_option( 'dayanarc_portfolio_page_id', $id );
            return $id;
        }
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

// ── 9. Primary navigation menu ────────────────────────────────────────────────
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
        // Menu already exists — update stale URLs
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

    // Create fresh menu
    $menu_id = wp_create_nav_menu( $menu_name );
    if ( is_wp_error( $menu_id ) ) return;

    $items = [
        [ 'label' => 'About Us',  'url' => home_url( '/' ) ],
        [ 'label' => 'Portfolio', 'url' => $portfolio_url ],
        [ 'label' => 'Services',  'url' => home_url( '/' ) ],
        [ 'label' => 'Journal',   'url' => $journal_url ],
        [ 'label' => 'Contact',   'url' => $contact_url ],
    ];

    foreach ( $items as $item ) {
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

// ── 6. Contact Form 7 form ────────────────────────────────────────────────────
function dayanarc_import_contact_form() {
    // Already exists and valid
    $existing_id = (int) get_option( 'dayanarc_contact_form_id', 0 );
    if ( $existing_id && get_post( $existing_id ) && get_post_type( $existing_id ) === 'wpcf7_contact_form' ) {
        return $existing_id;
    }

    // CF7 not installed
    if ( ! post_type_exists( 'wpcf7_contact_form' ) ) {
        return 0;
    }

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
<span style="font-size:11px;text-transform:uppercase;letter-spacing:0.15em;font-weight:600;color:#2c221a;">SEND REQUEST</span>
<svg width="16" height="10" viewBox="0 0 16 10" fill="none" stroke="#2c221a" stroke-width="1.2"><path d="M11 1L15 5M15 5L11 9M15 5H0" stroke-linecap="round" stroke-linejoin="round"/></svg>
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

    if ( dayanarc_post_exists( 'Contact', 'page' ) ) {
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

// ── 8. Service pages ──────────────────────────────────────────────────────────
function dayanarc_import_service_pages( $image_ids ) {
    $services = [
        [
            'title'      => 'Residential Excellence',
            'old_title'  => 'Architecture',
            'slug'       => 'architecture',
            'number'     => '01',
            'card_label' => 'Residential',
            'option'     => 'dayanarc_service_architecture_id',
            'excerpt'    => 'Crafting bespoke luxury villas and high-end residential complexes that redefine modern living through elegance and comfort.',
            'content'    => 'At Dayan Arc, our architectural services span the full design journey — from initial concept through schematic design, design development, construction documentation, and project administration. We work closely with each client to understand their vision, program requirements, and site conditions, delivering spaces that are both beautiful and precisely functional.

Our architects bring deep expertise in residential, commercial, and hospitality projects across the region, blending innovative design thinking with a rigorous attention to detail and technical excellence.',
            'features'   => "Concept Development\nSchematic Design\nDesign Development\nConstruction Documentation\nProject Administration\nSite Supervision",
            'image'      => 'interior1.jpg',
            'thumb_slug' => 'architecture',
        ],
        [
            'title'      => 'Commercial & Hospitality',
            'old_title'  => 'Interior Design',
            'slug'       => 'interior-design',
            'number'     => '02',
            'card_label' => 'Commercial',
            'option'     => 'dayanarc_service_interior_design_id',
            'excerpt'    => 'Designing dynamic corporate offices, retail spaces, and world-class restaurants that enhance brand identity and user experience.',
            'content'    => 'Our interior design team transforms spaces into experiences. Working from a deep understanding of light, material, proportion, and the human scale, we craft interiors that feel both intentional and alive. Every project begins with listening — understanding how a space will be lived in, worked in, or experienced — then translating that into a coherent design language.

From concept mood boards through furniture selection, lighting design, and final installation, Dayan Arc manages the complete interior design process with precision and care.',
            'features'   => "Space Planning\nConcept & Mood Boards\nMaterial & Finish Selection\nFurniture & FF&E Procurement\nLighting Design\n3D Visualization",
            'image'      => 'interior2.jpg',
            'thumb_slug' => 'interior-design',
        ],
        [
            'title'      => 'Public & Institutional',
            'old_title'  => '3D Visualization',
            'slug'       => '3d-visualization',
            'number'     => '03',
            'card_label' => 'Public',
            'option'     => 'dayanarc_service_3d_viz_id',
            'excerpt'    => 'Creating functional and inspiring public environments, including cultural centers and educational facilities, tailored for community engagement.',
            'content'    => 'Seeing a space before it is built changes everything. Our 3D visualization studio produces photorealistic still renders, animated walkthroughs, and interactive virtual tours that allow clients, contractors, and stakeholders to fully understand a design before a single wall is raised.

Using the latest rendering technology, we capture light, texture, and atmosphere with a level of realism that blurs the line between the designed and the built — helping clients make confident decisions at every stage.',
            'features'   => "Photorealistic Still Renders\nArchitectural Walkthroughs\nVirtual Reality Tours\nExterior & Interior Renders\nMaterial Studies\nPresentation Boards",
            'image'      => 'project10.png',
            'thumb_slug' => '3d-visualization',
        ],
        [
            'title'      => 'Infrastructure & Large-Scale',
            'old_title'  => 'Project Management',
            'slug'       => 'project-management',
            'number'     => '04',
            'card_label' => 'Infrastructure',
            'option'     => 'dayanarc_service_project_mgmt_id',
            'excerpt'    => 'Specialized engineering and design for high-complexity projects, such as international airports and major transportation hubs.',
            'content'    => 'Great design is only realised through great execution. Dayan Arc offers comprehensive project management services that bridge the gap between the design studio and the construction site. Our project managers coordinate all parties — contractors, consultants, suppliers, and authorities — ensuring the project stays on schedule, within budget, and true to design intent.

We are on-site when it matters most, resolving issues proactively and maintaining the quality standards that define every Dayan Arc project.',
            'features'   => "Timeline & Schedule Planning\nBudget Management\nContractor Coordination\nQuality Control & Inspection\nAuthority Approvals\nHandover & Close-out",
            'image'      => 'project9.png',
            'thumb_slug' => 'project-management',
        ],
    ];

    $theme_dir = get_template_directory();

    foreach ( $services as $svc ) {
        $existing_id = (int) get_option( $svc['option'], 0 );

        if ( $existing_id && get_post( $existing_id ) ) {
            // Update existing page with new title
            $page_id = $existing_id;
            wp_update_post( [ 'ID' => $page_id, 'post_title' => $svc['title'] ] );
        } else {
            // Search by new title, then old title
            $page_id = 0;
            foreach ( [ $svc['title'], $svc['old_title'] ] as $search_title ) {
                if ( dayanarc_post_exists( $search_title, 'page' ) ) {
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
            }

            if ( ! $page_id ) {
                $page_id = wp_insert_post( [
                    'post_title'   => $svc['title'],
                    'post_content' => $svc['content'],
                    'post_excerpt' => $svc['excerpt'],
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_name'    => $svc['slug'],
                ] );
            }
        }

        if ( ! $page_id || is_wp_error( $page_id ) ) continue;

        update_post_meta( $page_id, '_wp_page_template',         'page-service.php' );
        update_post_meta( $page_id, '_service_number',           $svc['number'] );
        update_post_meta( $page_id, '_service_card_description', $svc['excerpt'] );
        update_post_meta( $page_id, '_service_card_tagline',     $svc['card_label'] );
        update_post_meta( $page_id, '_service_features',         $svc['features'] );

        // Import thumbnail from content/images/services/ if available
        $thumb_file = $theme_dir . '/content/images/services/' . $svc['thumb_slug'] . '/thumbnail.jpg';
        if ( file_exists( $thumb_file ) ) {
            $upload = wp_upload_bits( basename( $thumb_file ), null, file_get_contents( $thumb_file ) );
            if ( empty( $upload['error'] ) ) {
                $mime   = wp_check_filetype( $upload['file'] );
                $att_id = wp_insert_attachment( [
                    'guid'           => $upload['url'],
                    'post_mime_type' => $mime['type'],
                    'post_title'     => sanitize_file_name( basename( $thumb_file ) ),
                    'post_status'    => 'inherit',
                ], $upload['file'] );
                if ( ! is_wp_error( $att_id ) ) {
                    wp_update_attachment_metadata( $att_id, wp_generate_attachment_metadata( $att_id, $upload['file'] ) );
                    set_post_thumbnail( $page_id, $att_id );
                }
            }
        } elseif ( isset( $image_ids[ $svc['image'] ] ) ) {
            set_post_thumbnail( $page_id, $image_ids[ $svc['image'] ] );
        }

        update_option( $svc['option'], $page_id );
    }
}

// ── Apply all text theme mods (mirrors content/manifest.php) ─────────────────
function dayanarc_apply_content_theme_mods() {
    $mods = [
        // Hero
        'hero_word_1'               => 'VISION.',
        'hero_word_2'               => 'DESIGN.',
        'hero_word_3'               => 'REALITY.',
        'hero_cta_label'            => 'Get in touch',
        'hero_tagline'              => 'At Dayan Arc, we blend creativity and expertise to craft exceptional architectural and interior design experiences. From concept to completion, we bring spaces to life with innovation, precision, and a passion for design excellence.',
        // About
        'about_heading_line1'       => 'A VISION BEYOND',
        'about_heading_line2'       => 'BORDERS',
        'about_cta_label'           => 'GET IN TOUCH',
        'about_body'                => 'At Dayan Arc, we believe that architecture is more than just designing structures; it is the art of crafting experiences and building legacies. With over 20 years of expertise and a track record of more than 400 global projects, my team and I have bridged the gap between German engineering precision and creative luxury. From our strategic hubs in Germany, Dubai, and Georgia, we personally ensure that every project — whether a bespoke villa or a complex international airport — meets the highest global standards of excellence.',
        // Our Service
        'our_service_heading'       => 'OUR SERVICE',
        'our_service_description'   => 'From architectural vision to flawless execution — our integrated services cover every discipline, every scale, and every geography.',
        'our_service_image_1_desc'  => 'Concept development and schematic design services tailored to your architectural vision.',
        'our_service_image_2_desc'  => 'Comprehensive construction documentation and technical drawings executed with precision.',
        // Portfolio
        'portfolio_heading'         => 'OUR WORKS',
        // Services
        'services_heading_line1'    => 'CORE DESIGN',
        'services_heading_line2'    => 'CONCEPTS',
        'services_cta_label'        => 'GET IN TOUCH',
        'services_intro'            => 'Our integrated design services are applied across a diverse range of sectors, ensuring that every concept — from private luxury to public infrastructure — is executed with unrivaled precision and global standards.',
        'services_tagline'          => 'Transforming ideas into inspiring, functional spaces.',
        // Journal
        'journal_heading'           => 'OUR GLOBAL FOOTPRINT',
        // Contact
        'fp_contact_heading_line1'  => "LET'S BEGIN A",
        'fp_contact_heading_line2'  => 'CONVERSATION',
        'fp_contact_description'    => "Tell us more about your space, your ideas, and your aspirations. We'll guide you through the next steps with care and intention.",
        'contact_page_heading'      => "LET'S BEGIN A CONVERSATION",
        'contact_page_description'  => "Tell us more about your space, your ideas, and your aspirations. We'll guide you through the next steps with care and intention.",
        // Footer / Brand
        'footer_tagline'            => 'Bringing together creativity, expertise, and passion to deliver exceptional design solutions.',
        'contact_location'          => 'Business Bay, Dubai, UAE',
        'contact_email'             => 'support@dayanarc.com',
        'contact_website'           => 'http://dayanarc.com',
        // Social
        'social_instagram'          => '#',
        'social_pinterest'          => '#',
        'social_behance'            => '#',
        'social_linkedin'           => '#',
    ];

    foreach ( $mods as $key => $value ) {
        set_theme_mod( $key, $value );
    }

    // Also update service page meta for the new card descriptions / titles
    $service_meta = [
        'dayanarc_service_architecture_id'   => [
            'title'            => 'Residential Excellence',
            'card_description' => 'Crafting bespoke luxury villas and high-end residential complexes that redefine modern living through elegance and comfort.',
            'card_tagline'     => 'Residential',
            'what_we_offer'    => 'WHAT WE OFFER',
            'cta_heading'      => 'READY TO START YOUR PROJECT?',
            'cta_description'  => "Let's discuss your vision and bring it to life with the expertise and care that defines Dayan Arc.",
            'cta_label'        => 'CONTACT US',
            'features'         => "Concept Development\nSchematic Design\nDesign Development\nConstruction Documentation\nProject Administration\nSite Supervision",
        ],
        'dayanarc_service_interior_design_id' => [
            'title'            => 'Commercial & Hospitality',
            'card_description' => 'Designing dynamic corporate offices, retail spaces, and world-class restaurants that enhance brand identity and user experience.',
            'card_tagline'     => 'Commercial',
            'what_we_offer'    => 'WHAT WE OFFER',
            'cta_heading'      => 'READY TO START YOUR PROJECT?',
            'cta_description'  => "Let's discuss your vision and bring it to life with the expertise and care that defines Dayan Arc.",
            'cta_label'        => 'CONTACT US',
            'features'         => "Space Planning\nConcept & Mood Boards\nMaterial & Finish Selection\nFurniture & FF&E Procurement\nLighting Design\n3D Visualization",
        ],
        'dayanarc_service_3d_viz_id' => [
            'title'            => 'Public & Institutional',
            'card_description' => 'Creating functional and inspiring public environments, including cultural centers and educational facilities, tailored for community engagement.',
            'card_tagline'     => 'Public',
            'what_we_offer'    => 'WHAT WE OFFER',
            'cta_heading'      => 'READY TO START YOUR PROJECT?',
            'cta_description'  => "Let's discuss your vision and bring it to life with the expertise and care that defines Dayan Arc.",
            'cta_label'        => 'CONTACT US',
            'features'         => "Photorealistic Still Renders\nArchitectural Walkthroughs\nVirtual Reality Tours\nExterior & Interior Renders\nMaterial Studies\nPresentation Boards",
        ],
        'dayanarc_service_project_mgmt_id' => [
            'title'            => 'Infrastructure & Large-Scale',
            'card_description' => 'Specialized engineering and design for high-complexity projects, such as international airports and major transportation hubs.',
            'card_tagline'     => 'Infrastructure',
            'what_we_offer'    => 'WHAT WE OFFER',
            'cta_heading'      => 'READY TO START YOUR PROJECT?',
            'cta_description'  => "Let's discuss your vision and bring it to life with the expertise and care that defines Dayan Arc.",
            'cta_label'        => 'CONTACT US',
            'features'         => "Timeline & Schedule Planning\nBudget Management\nContractor Coordination\nQuality Control & Inspection\nAuthority Approvals\nHandover & Close-out",
        ],
    ];

    foreach ( $service_meta as $option_key => $data ) {
        $post_id = (int) get_option( $option_key );
        if ( ! $post_id || ! get_post( $post_id ) ) continue;
        wp_update_post( [ 'ID' => $post_id, 'post_title' => $data['title'] ] );
        update_post_meta( $post_id, '_service_card_description', $data['card_description'] );
        update_post_meta( $post_id, '_service_card_tagline',     $data['card_tagline'] );
        update_post_meta( $post_id, '_service_what_we_offer',    $data['what_we_offer'] );
        update_post_meta( $post_id, '_service_cta_heading',      $data['cta_heading'] );
        update_post_meta( $post_id, '_service_cta_description',  $data['cta_description'] );
        update_post_meta( $post_id, '_service_cta_label',        $data['cta_label'] );
        update_post_meta( $post_id, '_service_features',         $data['features'] );
    }
}

// ── Import content/images/ (about + our-service) ──────────────────────────────
function dayanarc_import_content_images() {
    $content_dir = get_template_directory() . '/content/images/';

    $map = [
        [ 'file' => 'about/main.jpg',       'type' => 'theme_mod', 'key' => 'about_image_main' ],
        [ 'file' => 'about/detail.jpg',     'type' => 'theme_mod', 'key' => 'about_image_detail' ],
        [ 'file' => 'our-service/image-1.jpg', 'type' => 'theme_mod', 'key' => 'our_service_image_1' ],
        [ 'file' => 'our-service/image-2.jpg', 'type' => 'theme_mod', 'key' => 'our_service_image_2' ],
    ];

    foreach ( $map as $entry ) {
        $full_path = $content_dir . $entry['file'];
        if ( ! file_exists( $full_path ) ) continue;

        $upload = wp_upload_bits( basename( $full_path ), null, file_get_contents( $full_path ) );
        if ( ! empty( $upload['error'] ) ) continue;

        $mime   = wp_check_filetype( $upload['file'] );
        $att_id = wp_insert_attachment( [
            'guid'           => $upload['url'],
            'post_mime_type' => $mime['type'],
            'post_title'     => sanitize_file_name( basename( $full_path ) ),
            'post_status'    => 'inherit',
        ], $upload['file'] );

        if ( is_wp_error( $att_id ) ) continue;

        wp_update_attachment_metadata( $att_id, wp_generate_attachment_metadata( $att_id, $upload['file'] ) );
        set_theme_mod( $entry['key'], $upload['url'] );
    }
}

// ── Helper: check if post exists ──────────────────────────────────────────────
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

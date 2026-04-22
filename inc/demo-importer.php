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
                <li><strong>13 images</strong> uploaded to the Media Library</li>
                <li><strong>3 Portfolio items</strong> with featured images and meta data</li>
                <li><strong>12 Journal posts</strong> with featured images and excerpts</li>
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

    // 9. Primary nav menu (runs last so Contact URL is available)
    dayanarc_import_nav_menu();

    // Rebuild rewrite rules so /portfolio/ archive URL resolves immediately.
    flush_rewrite_rules( false );
    // Set flag so the next page load also flushes (more reliable in Studio/Playground).
    update_option( 'dayanarc_flush_rewrites_pending', '1' );

    return true;
}

// ── 1. Upload images to Media Library ────────────────────────────────────────
function dayanarc_import_images() {
    $theme_dir  = get_template_directory();
    $upload_dir = wp_upload_dir();
    $ids        = [];

    $files = [
        'project1.png', 'project2.png', 'project3.png', 'project4.png',
        'project5.png', 'project6.png', 'project7.png', 'project8.png',
        'project9.png', 'project10.png', 'project11.png',
        'interior1.jpg', 'interior2.jpg',
    ];

    foreach ( $files as $filename ) {
        $src = $theme_dir . '/assets/' . $filename;
        if ( ! file_exists( $src ) ) continue;

        // Skip if already imported (match by original filename stored in meta)
        $existing = new WP_Query( [
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'meta_key'       => '_dayanarc_source_file',
            'meta_value'     => $filename,
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ] );
        if ( $existing->have_posts() ) {
            $ids[ $filename ] = $existing->posts[0];
            continue;
        }

        $dest = $upload_dir['path'] . '/' . $filename;
        if ( ! @copy( $src, $dest ) ) continue;

        $mime      = wp_check_filetype( $filename );
        $attach_id = wp_insert_attachment( [
            'post_mime_type' => $mime['type'],
            'post_title'     => pathinfo( $filename, PATHINFO_FILENAME ),
            'post_status'    => 'inherit',
        ], $dest );

        if ( is_wp_error( $attach_id ) ) continue;

        $metadata = wp_generate_attachment_metadata( $attach_id, $dest );
        wp_update_attachment_metadata( $attach_id, $metadata );
        update_post_meta( $attach_id, '_dayanarc_source_file', $filename );

        $ids[ $filename ] = $attach_id;
    }

    return $ids;
}

// ── 2. Portfolio items ────────────────────────────────────────────────────────
function dayanarc_import_portfolio( $image_ids ) {
    $items = [
        [
            'title'    => 'Point Hotel Ballroom',
            'content'  => 'This project features the ballroom of a 5-star Point Hotel. Dayan Arc was responsible for the complete scope of work, delivering the design from concept development to execution, ensuring a refined and cohesive interior experience.',
            'location' => 'Riyadh, KSA',
            'concept'  => 'Complete hospitality design and fit-out',
            'palette'  => 'Balancing structural architecture with high-end styling',
            'image'    => 'interior1.jpg',
        ],
        [
            'title'    => 'Modern Elegance',
            'content'  => 'Where modern elegance meets contemporary comfort. Our latest project showcases sleek lines, thoughtful details, and a harmonious design that inspires.',
            'location' => 'Riyadh, KSA',
            'concept'  => 'Luxury living with architectural precision',
            'palette'  => 'Accent palette: neutral tones, sophisticated finishes',
            'image'    => 'interior2.jpg',
        ],
        [
            'title'    => 'Modern Kitchen',
            'content'  => 'Transform your culinary space with Dayan Arc. Where modern elegance meets functional design. Every detail crafted to inspire cooking and living.',
            'location' => 'Riyadh, KSA',
            'concept'  => 'Full range of services from architecture to fit-out',
            'palette'  => 'Accent palette: contemporary materials, clean lines',
            'image'    => 'project11.png',
        ],
    ];

    foreach ( $items as $item ) {
        if ( dayanarc_post_exists( $item['title'], 'portfolio' ) ) continue;

        $post_id = wp_insert_post( [
            'post_title'   => $item['title'],
            'post_content' => $item['content'],
            'post_excerpt' => $item['content'],
            'post_status'  => 'publish',
            'post_type'    => 'portfolio',
        ] );

        if ( is_wp_error( $post_id ) ) continue;

        update_post_meta( $post_id, '_portfolio_location', $item['location'] );
        update_post_meta( $post_id, '_portfolio_concept',  $item['concept'] );
        update_post_meta( $post_id, '_portfolio_palette',  $item['palette'] );

        if ( isset( $image_ids[ $item['image'] ] ) ) {
            set_post_thumbnail( $post_id, $image_ids[ $item['image'] ] );
        }
    }
}

// ── 3. Journal posts ──────────────────────────────────────────────────────────
function dayanarc_import_journal_posts( $image_ids ) {
    $posts = [
        [ 'title' => 'Design Excellence',     'content' => 'Design is the art of creating solutions that blend form with function, bringing innovation and beauty to everyday spaces.',                                                          'image' => 'project1.png'  ],
        [ 'title' => 'Modern Living',          'content' => 'Creating spaces that elevate everyday living through thoughtful design, where every element serves a purpose and adds to the overall harmony.',                                   'image' => 'project2.png'  ],
        [ 'title' => 'Functional Luxury',      'content' => 'Balancing high-end aesthetics with practical functionality — where beauty and purpose coexist seamlessly.',                                                                       'image' => 'project3.png'  ],
        [ 'title' => 'Creative Vision',        'content' => 'Innovation starts with a spark of creativity, igniting the world of design and transforming ordinary spaces into extraordinary experiences.',                                      'image' => 'project4.png'  ],
        [ 'title' => 'Architectural Harmony',  'content' => 'The firm designed a cohesive hospitality space that balances structural architecture with high-end interior styling, creating a unified and memorable environment.',               'image' => 'project5.png'  ],
        [ 'title' => 'Material Selection',     'content' => 'Expert guidance in choosing materials that guarantee durability and comfort, ensuring every surface tells a story of quality and craftsmanship.',                                  'image' => 'project6.png'  ],
        [ 'title' => 'Space Planning',         'content' => 'Thoughtful space planning that maximizes functionality and flow, creating environments that feel both expansive and intimate.',                                                    'image' => 'project7.png'  ],
        [ 'title' => 'Design Process',         'content' => 'From concept to execution, we design spaces tailored to your vision — a collaborative journey that transforms ideas into living, breathing environments.',                        'image' => 'project8.png'  ],
        [ 'title' => 'Transformative Design',  'content' => 'Dayan Arc brings a new approach and fresh opportunities to transform your spaces. From vision to reality, we design environments that elevate everyday living.',                  'image' => 'project9.png'  ],
        [ 'title' => 'Detail Focused',         'content' => 'Design is in the details. We believe in precision and attention to every element, from the curve of a handle to the texture of a wall.',                                         'image' => 'project10.png' ],
        [ 'title' => 'Finishing Touches',      'content' => 'Accessories have the power to elevate any design, adding the perfect finishing touch that brings a space to life and makes it uniquely yours.',                                   'image' => 'project11.png' ],
        [ 'title' => 'Inspired Spaces',        'content' => 'Your space deserves to become something greater and we make it happen — through vision, craft, and an unwavering commitment to design excellence.',                               'image' => 'interior1.jpg' ],
    ];

    foreach ( $posts as $post ) {
        if ( dayanarc_post_exists( $post['title'], 'post' ) ) continue;

        $post_id = wp_insert_post( [
            'post_title'   => $post['title'],
            'post_content' => $post['content'],
            'post_excerpt' => $post['content'],
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
            'title'    => 'Architecture',
            'slug'     => 'architecture',
            'number'   => '01',
            'option'   => 'dayanarc_service_architecture_id',
            'excerpt'  => 'Complete architectural design from concept to execution, tailored to your unique vision and functional needs.',
            'content'  => 'At Dayan Arc, our architectural services span the full design journey — from initial concept through schematic design, design development, construction documentation, and project administration. We work closely with each client to understand their vision, program requirements, and site conditions, delivering spaces that are both beautiful and precisely functional.

Our architects bring deep expertise in residential, commercial, and hospitality projects across the region, blending innovative design thinking with a rigorous attention to detail and technical excellence.',
            'features' => "Concept Development\nSchematic Design\nDesign Development\nConstruction Documentation\nProject Administration\nSite Supervision",
            'image'    => 'interior1.jpg',
        ],
        [
            'title'    => 'Interior Design',
            'slug'     => 'interior-design',
            'number'   => '02',
            'option'   => 'dayanarc_service_interior_design_id',
            'excerpt'  => 'Comprehensive interior design services from space planning to material selection and 3D visualization.',
            'content'  => 'Our interior design team transforms spaces into experiences. Working from a deep understanding of light, material, proportion, and the human scale, we craft interiors that feel both intentional and alive. Every project begins with listening — understanding how a space will be lived in, worked in, or experienced — then translating that into a coherent design language.

From concept mood boards through furniture selection, lighting design, and final installation, Dayan Arc manages the complete interior design process with precision and care.',
            'features' => "Space Planning\nConcept & Mood Boards\nMaterial & Finish Selection\nFurniture & FF&E Procurement\nLighting Design\n3D Visualization",
            'image'    => 'interior2.jpg',
        ],
        [
            'title'    => '3D Visualization',
            'slug'     => '3d-visualization',
            'number'   => '03',
            'option'   => 'dayanarc_service_3d_viz_id',
            'excerpt'  => 'High-quality 3D renderings and visualization to help you see your vision before construction begins.',
            'content'  => 'Seeing a space before it is built changes everything. Our 3D visualization studio produces photorealistic still renders, animated walkthroughs, and interactive virtual tours that allow clients, contractors, and stakeholders to fully understand a design before a single wall is raised.

Using the latest rendering technology, we capture light, texture, and atmosphere with a level of realism that blurs the line between the designed and the built — helping clients make confident decisions at every stage.',
            'features' => "Photorealistic Still Renders\nArchitectural Walkthroughs\nVirtual Reality Tours\nExterior & Interior Renders\nMaterial Studies\nPresentation Boards",
            'image'    => 'project10.png',
        ],
        [
            'title'    => 'Project Management',
            'slug'     => 'project-management',
            'number'   => '04',
            'option'   => 'dayanarc_service_project_mgmt_id',
            'excerpt'  => 'End-to-end project management ensuring every detail is executed with precision and attention.',
            'content'  => 'Great design is only realised through great execution. Dayan Arc offers comprehensive project management services that bridge the gap between the design studio and the construction site. Our project managers coordinate all parties — contractors, consultants, suppliers, and authorities — ensuring the project stays on schedule, within budget, and true to design intent.

We are on-site when it matters most, resolving issues proactively and maintaining the quality standards that define every Dayan Arc project.',
            'features' => "Timeline & Schedule Planning\nBudget Management\nContractor Coordination\nQuality Control & Inspection\nAuthority Approvals\nHandover & Close-out",
            'image'    => 'project9.png',
        ],
    ];

    foreach ( $services as $svc ) {
        // Check if already created
        $existing_id = (int) get_option( $svc['option'], 0 );
        if ( $existing_id && get_post( $existing_id ) ) continue;

        if ( dayanarc_post_exists( $svc['title'], 'page' ) ) {
            $q = new WP_Query( [
                'post_type'      => 'page',
                'title'          => $svc['title'],
                'post_status'    => 'any',
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'no_found_rows'  => true,
            ] );
            if ( $q->have_posts() ) {
                $id = $q->posts[0];
                update_post_meta( $id, '_wp_page_template',  'page-service.php' );
                update_post_meta( $id, '_service_number',    $svc['number'] );
                update_post_meta( $id, '_service_features',  $svc['features'] );
                update_option( $svc['option'], $id );
            }
            continue;
        }

        $page_id = wp_insert_post( [
            'post_title'   => $svc['title'],
            'post_content' => $svc['content'],
            'post_excerpt' => $svc['excerpt'],
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_name'    => $svc['slug'],
        ] );

        if ( is_wp_error( $page_id ) || ! $page_id ) continue;

        update_post_meta( $page_id, '_wp_page_template', 'page-service.php' );
        update_post_meta( $page_id, '_service_number',   $svc['number'] );
        update_post_meta( $page_id, '_service_features', $svc['features'] );

        if ( isset( $image_ids[ $svc['image'] ] ) ) {
            set_post_thumbnail( $page_id, $image_ids[ $svc['image'] ] );
        }

        update_option( $svc['option'], $page_id );
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

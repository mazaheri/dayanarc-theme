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
                <li><strong>Primary navigation menu</strong></li>
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

    // 4. Home page + front page settings
    dayanarc_import_home_page();

    // 5. Primary nav menu
    dayanarc_import_nav_menu();

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
    if ( dayanarc_post_exists( 'Home', 'page' ) ) return;

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

// ── 5. Primary navigation menu ────────────────────────────────────────────────
function dayanarc_import_nav_menu() {
    $menu_name = 'Primary Menu';

    if ( wp_get_nav_menu_object( $menu_name ) ) return;

    $menu_id = wp_create_nav_menu( $menu_name );
    if ( is_wp_error( $menu_id ) ) return;

    $items = [ 'About Us', 'Portfolio', 'Services', 'Journal', 'Contact' ];
    foreach ( $items as $label ) {
        wp_update_nav_menu_item( $menu_id, 0, [
            'menu-item-title'  => $label,
            'menu-item-url'    => home_url( '/' ),
            'menu-item-status' => 'publish',
            'menu-item-type'   => 'custom',
        ] );
    }

    $locations             = get_theme_mod( 'nav_menu_locations', [] );
    $locations['primary']  = $menu_id;
    set_theme_mod( 'nav_menu_locations', $locations );
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

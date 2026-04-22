<?php

$_dayanarc_dir = dirname( __FILE__ );
if ( file_exists( $_dayanarc_dir . '/inc/updater.php' ) )       require_once $_dayanarc_dir . '/inc/updater.php';
if ( file_exists( $_dayanarc_dir . '/inc/demo-importer.php' ) ) require_once $_dayanarc_dir . '/inc/demo-importer.php';

function dayanarc_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
    add_theme_support( 'custom-logo' );
    register_nav_menus( [ 'primary' => __( 'Primary Menu', 'dayanarc' ) ] );
}
add_action( 'after_setup_theme', 'dayanarc_setup' );

// Tailwind CDN must load as a script in <head> before content renders
function dayanarc_add_tailwind() {
    echo '<script src="https://cdn.tailwindcss.com"></script>' . "\n";
}
add_action( 'wp_head', 'dayanarc_add_tailwind', 1 );

function dayanarc_enqueue() {
    $ver = wp_get_theme()->get( 'Version' );
    $uri = get_template_directory_uri();

    wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Inter:wght@300;400;500;600&display=swap', [], null );

    if ( is_front_page() ) {
        wp_enqueue_style( 'fullpage-css', 'https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/4.0.20/fullpage.min.css', [], '4.0.20' );
        wp_enqueue_style( 'dayanarc', get_stylesheet_uri(), [ 'fullpage-css' ], $ver );
        wp_enqueue_script( 'fullpage-js', 'https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/4.0.20/fullpage.min.js', [], '4.0.20', true );
        wp_enqueue_script( 'dayanarc-main', $uri . '/assets/js/main.js', [ 'fullpage-js' ], $ver, true );
        wp_localize_script( 'dayanarc-main', 'dayanarcData', dayanarc_get_localized_data() );
    } else {
        wp_enqueue_style( 'dayanarc', get_stylesheet_uri(), [], $ver );
        wp_enqueue_script( 'dayanarc-main', $uri . '/assets/js/main.js', [], $ver, true );
        wp_localize_script( 'dayanarc-main', 'dayanarcData', [
            'themeUrl'      => $uri,
            'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
            'nonce'         => wp_create_nonce( 'dayanarc_contact' ),
            'portfolioData' => [],
            'journalPages'  => [],
        ] );
        wp_enqueue_script( 'dayanarc-inner', $uri . '/assets/js/inner.js', [], $ver, true );
        wp_localize_script( 'dayanarc-inner', 'dayanarcInner', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'dayanarc_load_more' ),
        ] );
    }
}
add_action( 'wp_enqueue_scripts', 'dayanarc_enqueue' );

function dayanarc_breadcrumb() {
    $home = esc_url( home_url( '/' ) );
    echo '<nav class="inner-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'dayanarc' ) . '">';
    echo '<a href="' . $home . '">HOME</a>';

    if ( is_post_type_archive( 'portfolio' ) || is_page_template( 'page-portfolio.php' ) ) {
        echo '<span class="breadcrumb-sep"> — </span>';
        echo '<span class="breadcrumb-current">PORTFOLIO</span>';
    } elseif ( is_home() ) {
        echo '<span class="breadcrumb-sep"> — </span>';
        echo '<span class="breadcrumb-current">JOURNAL</span>';
    } elseif ( is_singular( 'portfolio' ) ) {
        echo '<span class="breadcrumb-sep"> — </span>';
        echo '<a href="' . esc_url( dayanarc_portfolio_url() ) . '">PORTFOLIO</a>';
        echo '<span class="breadcrumb-sep"> — </span>';
        echo '<span class="breadcrumb-current">' . esc_html( strtoupper( get_the_title() ) ) . '</span>';
    } elseif ( is_single() ) {
        echo '<span class="breadcrumb-sep"> — </span>';
        echo '<a href="' . esc_url( dayanarc_journal_url() ) . '">JOURNAL</a>';
        echo '<span class="breadcrumb-sep"> — </span>';
        echo '<span class="breadcrumb-current">' . esc_html( strtoupper( get_the_title() ) ) . '</span>';
    } elseif ( is_page_template( 'page-contact.php' ) ) {
        echo '<span class="breadcrumb-sep"> — </span>';
        echo '<span class="breadcrumb-current">CONTACT</span>';
    } elseif ( is_page_template( 'page-service.php' ) ) {
        echo '<span class="breadcrumb-sep"> — </span>';
        echo '<a href="' . esc_url( home_url( '/' ) ) . '">SERVICES</a>';
        echo '<span class="breadcrumb-sep"> — </span>';
        echo '<span class="breadcrumb-current">' . esc_html( strtoupper( get_the_title() ) ) . '</span>';
    } elseif ( is_404() ) {
        echo '<span class="breadcrumb-sep"> — </span>';
        echo '<span class="breadcrumb-current">404</span>';
    }

    echo '</nav>';
}

function dayanarc_flush_rewrites() {
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'dayanarc_flush_rewrites' );

// Deferred rewrite flush — demo importer sets this option; the next page load flushes properly.
function dayanarc_maybe_flush_rewrites() {
    if ( get_option( 'dayanarc_flush_rewrites_pending' ) ) {
        delete_option( 'dayanarc_flush_rewrites_pending' );
        flush_rewrite_rules( false );
    }
}
add_action( 'init', 'dayanarc_maybe_flush_rewrites', 25 );

function dayanarc_portfolio_url() {
    $id = (int) get_option( 'dayanarc_portfolio_page_id', 0 );
    if ( $id && get_post( $id ) ) {
        return get_permalink( $id );
    }
    $url = get_post_type_archive_link( 'portfolio' );
    return $url ?: home_url( '/portfolio/' );
}

function dayanarc_journal_url() {
    $page_id = (int) get_option( 'page_for_posts' );
    return $page_id ? get_permalink( $page_id ) : home_url( '/journal/' );
}

function dayanarc_contact_page_url() {
    $id = (int) get_option( 'dayanarc_contact_page_id', 0 );
    if ( $id && get_post( $id ) ) {
        return get_permalink( $id );
    }
    return home_url( '/contact/' );
}

function dayanarc_service_url( $slug ) {
    $map = [
        'architecture'       => 'dayanarc_service_architecture_id',
        'interior-design'    => 'dayanarc_service_interior_design_id',
        '3d-visualization'   => 'dayanarc_service_3d_viz_id',
        'project-management' => 'dayanarc_service_project_mgmt_id',
    ];
    if ( isset( $map[ $slug ] ) ) {
        $id = (int) get_option( $map[ $slug ], 0 );
        if ( $id && get_post( $id ) ) {
            return get_permalink( $id );
        }
    }
    return home_url( '/' . $slug . '/' );
}

// Returns the stored CF7 form ID for "Dayan Arc Contact", or 0 if not found.
function dayanarc_get_contact_form_id() {
    $id = (int) get_option( 'dayanarc_contact_form_id', 0 );
    if ( $id && get_post( $id ) && get_post_type( $id ) === 'wpcf7_contact_form' ) {
        return $id;
    }
    // Fallback: return first available CF7 form
    if ( post_type_exists( 'wpcf7_contact_form' ) ) {
        $forms = get_posts( [
            'post_type'      => 'wpcf7_contact_form',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'orderby'        => 'date',
            'order'          => 'ASC',
            'fields'         => 'ids',
        ] );
        if ( $forms ) {
            update_option( 'dayanarc_contact_form_id', $forms[0] );
            return $forms[0];
        }
    }
    return 0;
}

function dayanarc_get_localized_data() {
    $uri = get_template_directory_uri();

    // Portfolio data from CPT, with fallback to defaults
    $portfolio_posts = get_posts( [ 'post_type' => 'portfolio', 'numberposts' => 10, 'post_status' => 'publish' ] );
    $portfolio_data  = [];

    if ( ! empty( $portfolio_posts ) ) {
        $total = count( $portfolio_posts );
        foreach ( $portfolio_posts as $i => $post ) {
            $num          = str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) . '/' . str_pad( $total, 2, '0', STR_PAD_LEFT );
            $location     = get_post_meta( $post->ID, '_portfolio_location', true ) ?: 'Riyadh, KSA';
            $concept      = get_post_meta( $post->ID, '_portfolio_concept', true ) ?: '';
            $palette      = get_post_meta( $post->ID, '_portfolio_palette', true ) ?: '';
            $img_large    = get_the_post_thumbnail_url( $post->ID, 'large' ) ?: 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1200&q=80';
            $detail_id    = get_post_meta( $post->ID, '_portfolio_detail_image', true );
            $img_small    = $detail_id ? wp_get_attachment_image_url( $detail_id, 'medium' ) : 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=600&q=80';

            $portfolio_data[] = [
                'id'          => $num,
                'title'       => strtoupper( $post->post_title ),
                'location'    => $location,
                'description' => wp_strip_all_tags( $post->post_content ),
                'concept'     => $concept,
                'palette'     => $palette,
                'imgLarge'    => $img_large,
                'imgSmall'    => $img_small,
            ];
        }
    } else {
        $portfolio_data = [
            [
                'id'          => '01/03',
                'title'       => 'POINT HOTEL BALLROOM',
                'location'    => 'Riyadh, KSA',
                'description' => 'This project features the ballroom of a 5-star Point Hotel. Dayan Arc was responsible for the complete scope of work, delivering the design from concept development to execution, ensuring a refined and cohesive interior experience.',
                'concept'     => 'Complete hospitality design and fit-out',
                'palette'     => 'Balancing structural architecture with high-end styling',
                'imgLarge'    => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1200&q=80',
                'imgSmall'    => 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'id'          => '02/03',
                'title'       => 'MODERN ELEGANCE',
                'location'    => 'Riyadh, KSA',
                'description' => 'Where modern elegance meets contemporary comfort. Our latest project showcases sleek lines, thoughtful details, and a harmonious design that inspires.',
                'concept'     => 'Luxury living with architectural precision',
                'palette'     => 'Accent palette: neutral tones, sophisticated finishes',
                'imgLarge'    => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1200&q=80',
                'imgSmall'    => $uri . '/assets/interior2.jpg',
            ],
            [
                'id'          => '03/03',
                'title'       => 'MODERN KITCHEN',
                'location'    => 'Riyadh, KSA',
                'description' => 'Transform your culinary space with Dayan Arc. Where modern elegance meets functional design. Every detail crafted to inspire cooking and living.',
                'concept'     => 'Full range of services from architecture to fit-out',
                'palette'     => 'Accent palette: contemporary materials, clean lines',
                'imgLarge'    => 'https://images.unsplash.com/photo-1617103996702-96ff29b1c467?auto=format&fit=crop&w=1200&q=80',
                'imgSmall'    => $uri . '/assets/project11.png',
            ],
        ];
    }

    // Journal data from posts, with fallback to defaults
    $posts         = get_posts( [ 'numberposts' => 12, 'post_status' => 'publish', 'orderby' => 'date', 'order' => 'DESC' ] );
    $journal_pages = [];

    if ( ! empty( $posts ) ) {
        $fallback_imgs = [ 'project1', 'project2', 'project3', 'project4', 'project5', 'project6', 'project7', 'project8', 'project9', 'project10', 'project11', 'interior1' ];
        $page          = [];

        foreach ( $posts as $i => $post ) {
            $thumb   = get_the_post_thumbnail_url( $post->ID, 'large' );
            $fb_name = $fallback_imgs[ $i % count( $fallback_imgs ) ];
            $ext     = str_contains( $fb_name, 'interior' ) ? '.jpg' : '.png';

            if ( ! $thumb ) {
                $thumb = $uri . '/assets/' . $fb_name . $ext;
            }

            $excerpt = has_excerpt( $post->ID )
                ? get_the_excerpt( $post )
                : wp_trim_words( $post->post_content, 20, '...' );

            $page[] = [
                'title' => strtoupper( $post->post_title ),
                'desc'  => $excerpt,
                'img'   => $thumb,
                'url'   => get_permalink( $post->ID ),
            ];

            if ( count( $page ) === 4 ) {
                $journal_pages[] = $page;
                $page            = [];
            }
        }

        if ( ! empty( $page ) ) {
            $journal_pages[] = $page;
        }
    } else {
        $journal_pages = [
            [
                [ 'title' => 'DESIGN EXCELLENCE',    'desc' => 'Design is the art of creating solutions that blend form with function, bringing innovation and beauty to everyday spaces.',                                                         'img' => $uri . '/assets/project1.png',  'url' => '#' ],
                [ 'title' => 'MODERN LIVING',         'desc' => 'Creating spaces that elevate everyday living through thoughtful design.',                                                                                                              'img' => $uri . '/assets/project2.png',  'url' => '#' ],
                [ 'title' => 'FUNCTIONAL LUXURY',     'desc' => 'Balancing high-end aesthetics with practical functionality.',                                                                                                                          'img' => $uri . '/assets/project3.png',  'url' => '#' ],
                [ 'title' => 'CREATIVE VISION',       'desc' => 'Innovation starts with a spark of creativity, igniting the world of design.',                                                                                                         'img' => $uri . '/assets/project4.png',  'url' => '#' ],
            ],
            [
                [ 'title' => 'ARCHITECTURAL HARMONY', 'desc' => 'The firm designed a cohesive hospitality space that balances structural architecture with high-end interior styling.',                                                                 'img' => $uri . '/assets/project5.png',  'url' => '#' ],
                [ 'title' => 'MATERIAL SELECTION',    'desc' => 'Expert guidance in choosing materials that guarantee durability and comfort.',                                                                                                         'img' => $uri . '/assets/project6.png',  'url' => '#' ],
                [ 'title' => 'SPACE PLANNING',        'desc' => 'Thoughtful space planning that maximizes functionality and flow.',                                                                                                                     'img' => $uri . '/assets/project7.png',  'url' => '#' ],
                [ 'title' => 'DESIGN PROCESS',        'desc' => 'From concept to execution, we design spaces tailored to your vision.',                                                                                                                'img' => $uri . '/assets/project8.png',  'url' => '#' ],
            ],
            [
                [ 'title' => 'TRANSFORMATIVE DESIGN', 'desc' => 'Dayan Arc brings a new approach and fresh opportunities to transform your spaces. From vision to reality, we design environments that elevate everyday living.',                       'img' => $uri . '/assets/project9.png',  'url' => '#' ],
                [ 'title' => 'DETAIL FOCUSED',        'desc' => 'Design is in the details. We believe in precision and attention to every element.',                                                                                                   'img' => $uri . '/assets/project10.png', 'url' => '#' ],
                [ 'title' => 'FINISHING TOUCHES',     'desc' => 'Accessories have the power to elevate any design, adding the perfect finishing touch.',                                                                                               'img' => $uri . '/assets/project11.png', 'url' => '#' ],
                [ 'title' => 'INSPIRED SPACES',       'desc' => 'Your space deserves to become something greater and we make it happen.',                                                                                                              'img' => $uri . '/assets/interior1.jpg', 'url' => '#' ],
            ],
        ];
    }

    return [
        'themeUrl'      => $uri,
        'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
        'nonce'         => wp_create_nonce( 'dayanarc_contact' ),
        'portfolioData' => $portfolio_data,
        'journalPages'  => $journal_pages,
    ];
}

// Contact form AJAX handler
function dayanarc_contact_submit() {
    check_ajax_referer( 'dayanarc_contact', 'nonce' );

    $name    = sanitize_text_field( $_POST['name'] ?? '' );
    $phone   = sanitize_text_field( $_POST['phone'] ?? '' );
    $email   = sanitize_email( $_POST['email'] ?? '' );
    $message = sanitize_textarea_field( $_POST['message'] ?? '' );

    if ( ! $name || ! $email || ! $message ) {
        wp_send_json_error( [ 'message' => 'Please fill in all required fields.' ] );
    }

    $to      = get_option( 'admin_email' );
    $subject = "New contact request from {$name}";
    $body    = "Name: {$name}\nPhone: {$phone}\nEmail: {$email}\n\nMessage:\n{$message}";
    $headers = [ "Content-Type: text/plain; charset=UTF-8", "Reply-To: {$email}" ];

    wp_mail( $to, $subject, $body, $headers )
        ? wp_send_json_success( [ 'message' => 'Your message has been sent. We\'ll be in touch soon.' ] )
        : wp_send_json_error( [ 'message' => 'Failed to send. Please try again or email us directly.' ] );
}
add_action( 'wp_ajax_dayanarc_contact', 'dayanarc_contact_submit' );
add_action( 'wp_ajax_nopriv_dayanarc_contact', 'dayanarc_contact_submit' );

// Portfolio custom post type
function dayanarc_register_cpts() {
    register_post_type( 'portfolio', [
        'labels'        => [
            'name'          => __( 'Portfolio', 'dayanarc' ),
            'singular_name' => __( 'Portfolio Item', 'dayanarc' ),
            'add_new_item'  => __( 'Add New Portfolio Item', 'dayanarc' ),
        ],
        'public'        => true,
        'has_archive'   => false,
        'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
        'menu_icon'     => 'dashicons-portfolio',
        'show_in_rest'  => true,
    ] );
}
add_action( 'init', 'dayanarc_register_cpts' );

// Portfolio meta boxes
function dayanarc_add_portfolio_meta_box() {
    add_meta_box( 'portfolio_details', __( 'Portfolio Details', 'dayanarc' ), 'dayanarc_render_portfolio_meta', 'portfolio', 'normal', 'default' );
}
add_action( 'add_meta_boxes', 'dayanarc_add_portfolio_meta_box' );

function dayanarc_render_portfolio_meta( $post ) {
    wp_nonce_field( 'dayanarc_portfolio_nonce', 'portfolio_nonce' );
    $location = get_post_meta( $post->ID, '_portfolio_location', true );
    $concept  = get_post_meta( $post->ID, '_portfolio_concept', true );
    $palette  = get_post_meta( $post->ID, '_portfolio_palette', true );
    ?>
    <table class="form-table">
        <tr><th><label for="portfolio_location"><?php _e( 'Location', 'dayanarc' ); ?></label></th>
            <td><input type="text" id="portfolio_location" name="portfolio_location" value="<?php echo esc_attr( $location ); ?>" class="regular-text" placeholder="e.g. Riyadh, KSA"></td></tr>
        <tr><th><label for="portfolio_concept"><?php _e( 'Concept', 'dayanarc' ); ?></label></th>
            <td><input type="text" id="portfolio_concept" name="portfolio_concept" value="<?php echo esc_attr( $concept ); ?>" class="regular-text"></td></tr>
        <tr><th><label for="portfolio_palette"><?php _e( 'Palette / Note', 'dayanarc' ); ?></label></th>
            <td><input type="text" id="portfolio_palette" name="portfolio_palette" value="<?php echo esc_attr( $palette ); ?>" class="regular-text"></td></tr>
    </table>
    <p class="description"><?php _e( 'Set the Featured Image as the main large image. Use the Palette field for the small secondary image caption.', 'dayanarc' ); ?></p>
    <?php
}

function dayanarc_save_portfolio_meta( $post_id ) {
    if ( ! isset( $_POST['portfolio_nonce'] ) || ! wp_verify_nonce( $_POST['portfolio_nonce'], 'dayanarc_portfolio_nonce' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $fields = [ 'portfolio_location' => '_portfolio_location', 'portfolio_concept' => '_portfolio_concept', 'portfolio_palette' => '_portfolio_palette' ];
    foreach ( $fields as $key => $meta_key ) {
        if ( isset( $_POST[ $key ] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $key ] ) );
        }
    }
}
add_action( 'save_post_portfolio', 'dayanarc_save_portfolio_meta' );

// ── Service page meta box ─────────────────────────────────────────────────────
function dayanarc_add_service_meta_box() {
    add_meta_box(
        'service_details',
        __( 'Service Details', 'dayanarc' ),
        'dayanarc_render_service_meta',
        'page',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'dayanarc_add_service_meta_box' );

function dayanarc_render_service_meta( $post ) {
    if ( get_page_template_slug( $post->ID ) !== 'page-service.php' ) {
        echo '<p style="color:#999;font-size:12px;">Only visible on pages using the <em>Service Page</em> template.</p>';
        return;
    }
    wp_nonce_field( 'dayanarc_service_nonce', 'service_nonce' );
    $number         = get_post_meta( $post->ID, '_service_number', true );
    $card_desc      = get_post_meta( $post->ID, '_service_card_description', true );
    $card_label     = get_post_meta( $post->ID, '_service_card_tagline', true );
    $features       = get_post_meta( $post->ID, '_service_features', true );
    $offer_heading  = get_post_meta( $post->ID, '_service_what_we_offer', true );
    $cta_heading    = get_post_meta( $post->ID, '_service_cta_heading', true );
    $cta_desc       = get_post_meta( $post->ID, '_service_cta_description', true );
    $cta_label      = get_post_meta( $post->ID, '_service_cta_label', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="service_number"><?php _e( 'Service Number', 'dayanarc' ); ?></label></th>
            <td>
                <input type="text" id="service_number" name="service_number"
                       value="<?php echo esc_attr( $number ); ?>"
                       class="regular-text" placeholder="e.g. 01">
            </td>
        </tr>
        <tr>
            <th><label for="service_card_description"><?php _e( 'Card Description', 'dayanarc' ); ?></label></th>
            <td>
                <textarea id="service_card_description" name="service_card_description"
                          rows="3" class="large-text"><?php echo esc_textarea( $card_desc ); ?></textarea>
                <p class="description"><?php _e( 'Short description shown on the homepage service card.', 'dayanarc' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="service_card_tagline"><?php _e( 'Card Label', 'dayanarc' ); ?></label></th>
            <td>
                <input type="text" id="service_card_tagline" name="service_card_tagline"
                       value="<?php echo esc_attr( $card_label ); ?>"
                       class="regular-text" placeholder="e.g. Consultation">
                <p class="description"><?php _e( 'Short label shown at the bottom of the homepage card.', 'dayanarc' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="service_what_we_offer"><?php _e( '"What We Offer" Heading', 'dayanarc' ); ?></label></th>
            <td>
                <input type="text" id="service_what_we_offer" name="service_what_we_offer"
                       value="<?php echo esc_attr( $offer_heading ); ?>"
                       class="regular-text" placeholder="WHAT WE OFFER">
            </td>
        </tr>
        <tr>
            <th><label for="service_features"><?php _e( 'Features (one per line)', 'dayanarc' ); ?></label></th>
            <td>
                <textarea id="service_features" name="service_features"
                          rows="8" class="large-text"><?php echo esc_textarea( $features ); ?></textarea>
                <p class="description"><?php _e( 'Each line becomes one bullet point in the "What We Offer" list.', 'dayanarc' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="service_cta_heading"><?php _e( 'CTA Strip Heading', 'dayanarc' ); ?></label></th>
            <td>
                <input type="text" id="service_cta_heading" name="service_cta_heading"
                       value="<?php echo esc_attr( $cta_heading ); ?>"
                       class="regular-text" placeholder="READY TO START YOUR PROJECT?">
            </td>
        </tr>
        <tr>
            <th><label for="service_cta_description"><?php _e( 'CTA Strip Description', 'dayanarc' ); ?></label></th>
            <td>
                <textarea id="service_cta_description" name="service_cta_description"
                          rows="3" class="large-text"><?php echo esc_textarea( $cta_desc ); ?></textarea>
            </td>
        </tr>
        <tr>
            <th><label for="service_cta_label"><?php _e( 'CTA Button Label', 'dayanarc' ); ?></label></th>
            <td>
                <input type="text" id="service_cta_label" name="service_cta_label"
                       value="<?php echo esc_attr( $cta_label ); ?>"
                       class="regular-text" placeholder="CONTACT US">
            </td>
        </tr>
    </table>
    <?php
}

function dayanarc_save_service_meta( $post_id ) {
    if ( ! isset( $_POST['service_nonce'] ) || ! wp_verify_nonce( $_POST['service_nonce'], 'dayanarc_service_nonce' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['service_number'] ) ) {
        update_post_meta( $post_id, '_service_number', sanitize_text_field( $_POST['service_number'] ) );
    }
    if ( isset( $_POST['service_card_description'] ) ) {
        update_post_meta( $post_id, '_service_card_description', sanitize_textarea_field( $_POST['service_card_description'] ) );
    }
    if ( isset( $_POST['service_card_tagline'] ) ) {
        update_post_meta( $post_id, '_service_card_tagline', sanitize_text_field( $_POST['service_card_tagline'] ) );
    }
    if ( isset( $_POST['service_what_we_offer'] ) ) {
        update_post_meta( $post_id, '_service_what_we_offer', sanitize_text_field( $_POST['service_what_we_offer'] ) );
    }
    if ( isset( $_POST['service_features'] ) ) {
        update_post_meta( $post_id, '_service_features', sanitize_textarea_field( $_POST['service_features'] ) );
    }
    if ( isset( $_POST['service_cta_heading'] ) ) {
        update_post_meta( $post_id, '_service_cta_heading', sanitize_text_field( $_POST['service_cta_heading'] ) );
    }
    if ( isset( $_POST['service_cta_description'] ) ) {
        update_post_meta( $post_id, '_service_cta_description', sanitize_textarea_field( $_POST['service_cta_description'] ) );
    }
    if ( isset( $_POST['service_cta_label'] ) ) {
        update_post_meta( $post_id, '_service_cta_label', sanitize_text_field( $_POST['service_cta_label'] ) );
    }
}
add_action( 'save_post_page', 'dayanarc_save_service_meta' );

// ── Journal mosaic card renderer ──────────────────────────────────────────────
// Pattern: 4-item cycle — [wide landscape, tall portrait, tall portrait, wide landscape]
// This creates a natural brick mosaic when rows alternate wide+narrow items.
function dayanarc_render_journal_card( $post, $index ) {
    static $fallback = 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1200&q=80';

    $slot_map = [
        0 => [ 'cols' => 2, 'aspect' => '16/9',  'class' => 'mosaic-wide' ],
        1 => [ 'cols' => 1, 'aspect' => '3/4',   'class' => 'mosaic-tall' ],
        2 => [ 'cols' => 1, 'aspect' => '3/4',   'class' => 'mosaic-tall' ],
        3 => [ 'cols' => 2, 'aspect' => '16/9',  'class' => 'mosaic-wide' ],
    ];
    $slot  = $index % 4;
    $p     = $slot_map[ $slot ];

    $img     = get_the_post_thumbnail_url( $post->ID, 'large' ) ?: $fallback;
    $title   = get_the_title( $post );
    $url     = get_permalink( $post );
    $date    = get_the_date( 'M j, Y', $post );
    $excerpt = has_excerpt( $post->ID )
        ? get_the_excerpt( $post )
        : wp_trim_words( $post->post_content, 18, '...' );

    ob_start();
    ?>
    <a href="<?php echo esc_url( $url ); ?>"
       class="journal-mosaic-item <?php echo esc_attr( $p['class'] ); ?>"
       style="grid-column: span <?php echo $p['cols']; ?>;">
        <img src="<?php echo esc_url( $img ); ?>"
             alt="<?php echo esc_attr( $title ); ?>"
             loading="lazy">
        <div class="journal-mosaic-overlay">
            <span class="journal-mosaic-date"><?php echo esc_html( $date ); ?></span>
            <h3 class="journal-mosaic-title title-text"><?php echo esc_html( strtoupper( $title ) ); ?></h3>
            <div class="journal-mosaic-excerpt">
                <p><?php echo esc_html( $excerpt ); ?></p>
                <span class="journal-mosaic-read-more">READ MORE</span>
            </div>
        </div>
    </a>
    <?php
    return ob_get_clean();
}

// ── Journal Load More AJAX ────────────────────────────────────────────────────
function dayanarc_load_more_journal() {
    check_ajax_referer( 'dayanarc_load_more', 'nonce' );

    $offset   = absint( $_POST['offset'] ?? 0 );
    $per_page = 4;

    $posts = get_posts( [
        'post_type'      => 'post',
        'posts_per_page' => $per_page,
        'offset'         => $offset,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ] );

    if ( empty( $posts ) ) {
        wp_send_json_success( [ 'html' => '', 'has_more' => false ] );
        return;
    }

    $html = '';
    foreach ( $posts as $i => $post ) {
        $html .= dayanarc_render_journal_card( $post, $offset + $i );
    }

    $total    = (int) wp_count_posts( 'post' )->publish;
    $has_more = ( $offset + $per_page ) < $total;

    wp_send_json_success( [ 'html' => $html, 'has_more' => $has_more ] );
}
add_action( 'wp_ajax_dayanarc_load_more_journal',        'dayanarc_load_more_journal' );
add_action( 'wp_ajax_nopriv_dayanarc_load_more_journal', 'dayanarc_load_more_journal' );

// ── Customizer: auto-navigate preview when a section is opened ────────────────
add_action( 'customize_controls_enqueue_scripts', function () {
    $ver = wp_get_theme()->get( 'Version' );
    wp_enqueue_script(
        'dayanarc-customizer-nav',
        get_template_directory_uri() . '/assets/js/customizer-nav.js',
        [ 'customize-controls' ],
        $ver,
        true
    );
    wp_localize_script( 'dayanarc-customizer-nav', 'dayanarcCustomizerUrls', [
        'home'      => home_url( '/' ),
        'contact'   => dayanarc_contact_page_url(),
        'journal'   => dayanarc_journal_url(),
        'portfolio' => dayanarc_portfolio_url(),
    ] );
} );

// ── Customizer: Dayan Arc editable fields ─────────────────────────────────────
add_action( 'customize_register', function ( $wp_customize ) {

    // ══ PANELS ════════════════════════════════════════════════════════════════

    $wp_customize->add_panel( 'dayanarc_homepage', [
        'title'       => 'Homepage',
        'description' => 'Settings for front page sections.',
        'priority'    => 30,
    ] );

    $wp_customize->add_panel( 'dayanarc_pages', [
        'title'       => 'Other Pages',
        'description' => 'Settings for individual inner pages.',
        'priority'    => 31,
    ] );

    $wp_customize->add_panel( 'dayanarc_general', [
        'title'       => 'General Settings',
        'description' => 'Sitewide settings that appear on every page.',
        'priority'    => 32,
    ] );

    // ══ HOMEPAGE PANEL ════════════════════════════════════════════════════════

    // ── Hero ──────────────────────────────────────────────────────────────
    $wp_customize->add_section( 'dayanarc_hero', [
        'title'    => 'Hero Section',
        'panel'    => 'dayanarc_homepage',
        'priority' => 10,
    ] );
    foreach ( [
        'hero_word_1' => [ 'label' => 'Hero Word 1', 'default' => 'VISION.' ],
        'hero_word_2' => [ 'label' => 'Hero Word 2', 'default' => 'DESIGN.' ],
        'hero_word_3' => [ 'label' => 'Hero Word 3', 'default' => 'REALITY.' ],
        'hero_cta_label' => [ 'label' => 'CTA Button Label', 'default' => 'Get in touch' ],
    ] as $key => $field ) {
        $wp_customize->add_setting( $key, [
            'default'           => $field['default'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( $key, [
            'label'   => $field['label'],
            'section' => 'dayanarc_hero',
            'type'    => 'text',
        ] );
    }
    $wp_customize->add_setting( 'hero_tagline', [
        'default'           => 'At Dayan Arc, we blend creativity and expertise to craft exceptional architectural and interior design experiences. From concept to completion, we bring spaces to life with innovation, precision, and a passion for design excellence.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'hero_tagline', [
        'label'       => 'Hero Tagline',
        'description' => 'Paragraph below the main headline.',
        'section'     => 'dayanarc_hero',
        'type'        => 'textarea',
    ] );

    // ── About ─────────────────────────────────────────────────────────────
    $wp_customize->add_section( 'dayanarc_about', [
        'title'    => 'About Section',
        'panel'    => 'dayanarc_homepage',
        'priority' => 20,
    ] );
    foreach ( [
        'about_heading_line1' => [ 'label' => 'Heading Line 1', 'default' => 'DESIGN WITH' ],
        'about_heading_line2' => [ 'label' => 'Heading Line 2', 'default' => 'PASSION' ],
        'about_cta_label'     => [ 'label' => 'CTA Link Label', 'default' => 'LEARN MORE' ],
    ] as $key => $field ) {
        $wp_customize->add_setting( $key, [
            'default'           => $field['default'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( $key, [
            'label'   => $field['label'],
            'section' => 'dayanarc_about',
            'type'    => 'text',
        ] );
    }
    $wp_customize->add_setting( 'about_body', [
        'default'           => 'At Dayan Arc, our team brings together the best talent from around the world, combining creativity, expertise, and passion. Together, we strive to deliver exceptional design solutions that exceed expectations and create spaces that inspire and delight.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'about_body', [
        'label'   => 'About Description',
        'section' => 'dayanarc_about',
        'type'    => 'textarea',
    ] );
    $wp_customize->add_setting( 'about_image_main', [
        'default'           => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1200&q=80',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'about_image_main', [
        'label'   => 'About Main Image',
        'section' => 'dayanarc_about',
    ] ) );
    $wp_customize->add_setting( 'about_image_detail', [
        'default'           => 'https://images.unsplash.com/photo-1631679706909-1844bbd07221?q=80&w=800&auto=format&fit=crop',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'about_image_detail', [
        'label'   => 'About Detail Image (desktop only)',
        'section' => 'dayanarc_about',
    ] ) );

    // ── Portfolio ─────────────────────────────────────────────────────────
    $wp_customize->add_section( 'dayanarc_portfolio', [
        'title'    => 'Portfolio Section',
        'panel'    => 'dayanarc_homepage',
        'priority' => 25,
    ] );
    $wp_customize->add_setting( 'portfolio_heading', [
        'default'           => 'OUR WORKS',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'portfolio_heading', [
        'label'       => 'Portfolio Heading',
        'description' => 'Used on the homepage section and the portfolio archive page.',
        'section'     => 'dayanarc_portfolio',
        'type'        => 'text',
    ] );

    // ── Services ──────────────────────────────────────────────────────────
    $wp_customize->add_section( 'dayanarc_services', [
        'title'    => 'Services Section',
        'panel'    => 'dayanarc_homepage',
        'priority' => 30,
    ] );
    foreach ( [
        'services_heading_line1' => [ 'label' => 'Heading Line 1', 'default' => 'COMPREHENSIVE' ],
        'services_heading_line2' => [ 'label' => 'Heading Line 2', 'default' => 'SOLUTIONS' ],
        'services_cta_label'     => [ 'label' => 'CTA Button Label', 'default' => 'GET IN TOUCH' ],
    ] as $key => $field ) {
        $wp_customize->add_setting( $key, [
            'default'           => $field['default'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( $key, [
            'label'   => $field['label'],
            'section' => 'dayanarc_services',
            'type'    => 'text',
        ] );
    }
    $wp_customize->add_setting( 'services_intro', [
        'default'           => 'At Dayan Arc, we offer comprehensive architectural and interior design services, from concept development to project management.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'services_intro', [
        'label'   => 'Services Intro',
        'section' => 'dayanarc_services',
        'type'    => 'textarea',
    ] );
    $wp_customize->add_setting( 'services_tagline', [
        'default'           => 'Transforming ideas into inspiring, functional spaces.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'services_tagline', [
        'label'   => 'Services Tagline',
        'section' => 'dayanarc_services',
        'type'    => 'text',
    ] );

    // ── Journal ───────────────────────────────────────────────────────────
    $wp_customize->add_section( 'dayanarc_journal', [
        'title'       => 'Journal Section',
        'panel'       => 'dayanarc_homepage',
        'description' => 'Used on the homepage section and the journal archive page.',
        'priority'    => 35,
    ] );
    $wp_customize->add_setting( 'journal_heading', [
        'default'           => 'DESIGN INSIGHTS',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'journal_heading', [
        'label'   => 'Journal Heading',
        'section' => 'dayanarc_journal',
        'type'    => 'text',
    ] );

    // ── Contact Section (front page) ──────────────────────────────────────
    $wp_customize->add_section( 'dayanarc_fp_contact', [
        'title'    => 'Contact Section',
        'panel'    => 'dayanarc_homepage',
        'priority' => 40,
    ] );
    foreach ( [
        'fp_contact_heading_line1' => [ 'label' => 'Heading Line 1', 'default' => "LET'S BEGIN A" ],
        'fp_contact_heading_line2' => [ 'label' => 'Heading Line 2', 'default' => 'CONVERSATION' ],
    ] as $key => $field ) {
        $wp_customize->add_setting( $key, [
            'default'           => $field['default'],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( $key, [
            'label'   => $field['label'],
            'section' => 'dayanarc_fp_contact',
            'type'    => 'text',
        ] );
    }
    $wp_customize->add_setting( 'fp_contact_description', [
        'default'           => "Tell us more about your space, your ideas, and your aspirations. We'll guide you through the next steps with care and intention.",
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'fp_contact_description', [
        'label'       => 'Description',
        'description' => 'Paragraph under the contact heading on the home page.',
        'section'     => 'dayanarc_fp_contact',
        'type'        => 'textarea',
    ] );

    // ══ OTHER PAGES PANEL ════════════════════════════════════════════════════

    // ── Contact Page ──────────────────────────────────────────────────────
    $wp_customize->add_section( 'dayanarc_contact_page', [
        'title'    => 'Contact Page',
        'panel'    => 'dayanarc_pages',
        'priority' => 10,
    ] );
    $wp_customize->add_setting( 'contact_page_heading', [
        'default'           => "LET'S BEGIN A CONVERSATION",
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'contact_page_heading', [
        'label'   => 'Heading',
        'section' => 'dayanarc_contact_page',
        'type'    => 'text',
    ] );
    $wp_customize->add_setting( 'contact_page_description', [
        'default'           => "Tell us more about your space, your ideas, and your aspirations. We'll guide you through the next steps with care and intention.",
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'contact_page_description', [
        'label'   => 'Description',
        'section' => 'dayanarc_contact_page',
        'type'    => 'textarea',
    ] );

    // ══ GENERAL SETTINGS PANEL ════════════════════════════════════════════════

    // ── Brand ─────────────────────────────────────────────────────────────
    $wp_customize->add_section( 'dayanarc_brand', [
        'title'    => 'Brand',
        'panel'    => 'dayanarc_general',
        'priority' => 5,
    ] );
    $wp_customize->add_setting( 'footer_tagline', [
        'default'           => 'Bringing together creativity, expertise, and passion to deliver exceptional design solutions.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'footer_tagline', [
        'label'       => 'Brand Tagline',
        'description' => 'Short line under the logo in every footer.',
        'section'     => 'dayanarc_brand',
        'type'        => 'textarea',
    ] );

    // ── Contact Info ──────────────────────────────────────────────────────
    $wp_customize->add_section( 'dayanarc_contact_info', [
        'title'    => 'Contact Info',
        'panel'    => 'dayanarc_general',
        'priority' => 10,
    ] );
    foreach ( [
        'contact_location' => [ 'label' => 'Location',   'default' => 'Riyadh, Saudi Arabia',     'type' => 'text' ],
        'contact_email'    => [ 'label' => 'Email',       'default' => 'dayanarc.co@gmail.com',    'type' => 'text' ],
        'contact_website'  => [ 'label' => 'Website URL', 'default' => 'https://www.dayanarc.com', 'type' => 'url'  ],
    ] as $key => $field ) {
        $wp_customize->add_setting( $key, [
            'default'           => $field['default'],
            'sanitize_callback' => $field['type'] === 'url' ? 'esc_url_raw' : 'sanitize_text_field',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( $key, [
            'label'   => $field['label'],
            'section' => 'dayanarc_contact_info',
            'type'    => $field['type'],
        ] );
    }

    // ── Social Links ──────────────────────────────────────────────────────
    $wp_customize->add_section( 'dayanarc_social', [
        'title'    => 'Social Links',
        'panel'    => 'dayanarc_general',
        'priority' => 20,
    ] );
    foreach ( [
        'social_instagram' => 'Instagram URL',
        'social_pinterest' => 'Pinterest URL',
        'social_behance'   => 'Behance URL',
        'social_linkedin'  => 'LinkedIn URL',
    ] as $key => $label ) {
        $wp_customize->add_setting( $key, [
            'default'           => '#',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( $key, [
            'label'   => $label,
            'section' => 'dayanarc_social',
            'type'    => 'url',
        ] );
    }

} );

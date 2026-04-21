<?php

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
    wp_enqueue_style( 'fullpage-css', 'https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/4.0.20/fullpage.min.css', [], '4.0.20' );
    wp_enqueue_style( 'dayanarc', get_stylesheet_uri(), [ 'fullpage-css' ], $ver );

    wp_enqueue_script( 'fullpage-js', 'https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/4.0.20/fullpage.min.js', [], '4.0.20', true );
    wp_enqueue_script( 'dayanarc-main', $uri . '/assets/js/main.js', [ 'fullpage-js' ], $ver, true );

    wp_localize_script( 'dayanarc-main', 'dayanarcData', dayanarc_get_localized_data() );
}
add_action( 'wp_enqueue_scripts', 'dayanarc_enqueue' );

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

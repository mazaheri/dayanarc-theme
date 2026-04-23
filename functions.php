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
        wp_enqueue_style( 'glightbox', 'https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css', [], '3.2.0' );
        wp_enqueue_style( 'dayanarc', get_stylesheet_uri(), [ 'fullpage-css', 'glightbox' ], $ver );
        wp_enqueue_script( 'fullpage-js', 'https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/4.0.20/fullpage.min.js', [], '4.0.20', true );
        wp_enqueue_script( 'glightbox', 'https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js', [], '3.2.0', true );
        wp_add_inline_script( 'glightbox', 'document.addEventListener("DOMContentLoaded",function(){GLightbox({selector:".glightbox",touchNavigation:true,loop:false});});' );
        wp_enqueue_script( 'dayanarc-main', $uri . '/assets/js/main.js', [ 'fullpage-js' ], $ver, true );
        wp_localize_script( 'dayanarc-main', 'dayanarcData', dayanarc_get_localized_data() );
    } else {
        wp_enqueue_style( 'dayanarc', get_stylesheet_uri(), [], $ver );
        if ( is_singular( 'portfolio' ) ) {
            wp_enqueue_style( 'glightbox', 'https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css', [], '3.2.0' );
            wp_enqueue_script( 'glightbox', 'https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js', [], '3.2.0', true );
            wp_add_inline_script( 'glightbox', 'document.addEventListener("DOMContentLoaded",function(){GLightbox({selector:".glightbox",touchNavigation:true,loop:true});});' );
        }
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

    // ── portfolioData → now blog posts (feeds Section 7 journal carousel) ──────
    $blog_posts     = get_posts( [ 'post_type' => 'post', 'numberposts' => 10, 'post_status' => 'publish', 'orderby' => 'date', 'order' => 'DESC' ] );
    $portfolio_data = [];

    if ( ! empty( $blog_posts ) ) {
        $total = count( $blog_posts );
        foreach ( $blog_posts as $i => $post ) {
            $num     = str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) . '/' . str_pad( $total, 2, '0', STR_PAD_LEFT );
            $img     = get_the_post_thumbnail_url( $post->ID, 'large' );
            if ( ! $img ) {
                $fb = [ 'project1.png', 'project2.png', 'project3.png', 'interior1.jpg' ];
                $img = $uri . '/assets/' . $fb[ $i % count( $fb ) ];
            }
            $excerpt = has_excerpt( $post->ID )
                ? get_the_excerpt( $post )
                : wp_trim_words( $post->post_content, 30, '...' );

            $portfolio_data[] = [
                'id'          => $num,
                'title'       => strtoupper( $post->post_title ),
                'date'        => date_i18n( 'F Y', strtotime( $post->post_date ) ),
                'description' => wp_strip_all_tags( $excerpt ),
                'img'         => $img,
                'permalink'   => get_permalink( $post->ID ),
            ];
        }
    } else {
        $portfolio_data = [
            [
                'id'          => '01/03',
                'title'       => 'DESIGN EXCELLENCE',
                'date'        => 'April 2026',
                'description' => 'Design is the art of creating solutions that blend form with function, bringing innovation and beauty to everyday spaces.',
                'img'         => $uri . '/assets/project1.png',
                'permalink'   => '#',
            ],
            [
                'id'          => '02/03',
                'title'       => 'MODERN LIVING',
                'date'        => 'March 2026',
                'description' => 'Creating spaces that elevate everyday living through thoughtful design, where every element serves a purpose and adds to the overall harmony.',
                'img'         => $uri . '/assets/project2.png',
                'permalink'   => '#',
            ],
            [
                'id'          => '03/03',
                'title'       => 'FUNCTIONAL LUXURY',
                'date'        => 'February 2026',
                'description' => 'Balancing high-end aesthetics with practical functionality — where beauty and purpose coexist seamlessly.',
                'img'         => $uri . '/assets/project3.png',
                'permalink'   => '#',
            ],
        ];
    }

    // ── journalPages → now portfolio CPT (feeds Section 5 portfolio grid) ───────
    $portfolio_cpt = get_posts( [ 'post_type' => 'portfolio', 'numberposts' => 12, 'post_status' => 'publish', 'orderby' => 'date', 'order' => 'DESC' ] );
    $journal_pages = [];

    if ( ! empty( $portfolio_cpt ) ) {
        $fallback_imgs = [ 'project1', 'project2', 'project3', 'project4', 'project5', 'project6', 'project7', 'project8', 'project9', 'project10', 'project11', 'interior1' ];
        $page          = [];

        foreach ( $portfolio_cpt as $i => $post ) {
            $thumb = get_the_post_thumbnail_url( $post->ID, 'large' );
            if ( ! $thumb ) {
                $fb_name = $fallback_imgs[ $i % count( $fallback_imgs ) ];
                $ext     = str_contains( $fb_name, 'interior' ) ? '.jpg' : '.png';
                $thumb   = $uri . '/assets/' . $fb_name . $ext;
            }
            $excerpt = wp_trim_words( $post->post_content, 20, '...' );

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
                [ 'title' => 'GEORGIA RESIDENCE',     'desc' => 'A refined residential project blending contemporary design with local architectural traditions.',                          'img' => $uri . '/assets/project1.png',  'url' => '#' ],
                [ 'title' => 'GCC PAVILION',           'desc' => 'A landmark hospitality space designed from concept to execution with precision and care.',                                'img' => $uri . '/assets/project2.png',  'url' => '#' ],
                [ 'title' => 'GERMANY OFFICE HQ',      'desc' => 'Modern office design that balances open collaboration with focused work environments.',                                   'img' => $uri . '/assets/project3.png',  'url' => '#' ],
                [ 'title' => 'MODERN INTERIOR STUDIO', 'desc' => 'Transforming a raw space into a warm, functional studio that reflects the client\'s creative identity.',                  'img' => $uri . '/assets/interior1.jpg', 'url' => '#' ],
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

// ── Portfolio gallery meta box ────────────────────────────────────────────────
add_action( 'add_meta_boxes', function () {
    add_meta_box( 'portfolio_gallery', __( 'Gallery Images', 'dayanarc' ), 'dayanarc_render_portfolio_gallery_meta', 'portfolio', 'normal', 'high' );
} );

function dayanarc_render_portfolio_gallery_meta( $post ) {
    wp_nonce_field( 'dayanarc_gallery_nonce', 'portfolio_gallery_nonce' );
    $ids = json_decode( get_post_meta( $post->ID, '_portfolio_gallery', true ), true );
    if ( ! is_array( $ids ) ) $ids = [];
    ?>
    <div id="portfolio-gallery-wrap" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px;min-height:32px;">
        <?php foreach ( $ids as $id ) :
            $url = wp_get_attachment_image_url( $id, 'thumbnail' );
            if ( ! $url ) continue;
        ?>
        <div class="gallery-item" style="position:relative;width:80px;height:80px;">
            <img src="<?php echo esc_url( $url ); ?>" style="width:80px;height:80px;object-fit:cover;display:block;">
            <button type="button" class="remove-gallery-img" data-id="<?php echo (int) $id; ?>"
                style="position:absolute;top:2px;right:2px;background:#cc0000;color:#fff;border:none;border-radius:2px;padding:0 5px;cursor:pointer;font-size:12px;line-height:1.6;">×</button>
        </div>
        <?php endforeach; ?>
    </div>
    <input type="hidden" id="portfolio_gallery_ids" name="portfolio_gallery_ids" value="<?php echo esc_attr( wp_json_encode( array_values( $ids ) ) ); ?>">
    <button type="button" id="add-gallery-images" class="button"><?php _e( 'Add / Edit Gallery Images', 'dayanarc' ); ?></button>
    <p class="description" style="margin-top:6px;"><?php _e( 'These images appear in the grid on the single portfolio page.', 'dayanarc' ); ?></p>
    <script>
    jQuery(function($){
        var frame;
        $('#add-gallery-images').on('click', function(e){
            e.preventDefault();
            if ( frame ) { frame.open(); return; }
            frame = wp.media({
                title: '<?php echo esc_js( __( 'Select Gallery Images', 'dayanarc' ) ); ?>',
                button: { text: '<?php echo esc_js( __( 'Add to Gallery', 'dayanarc' ) ); ?>' },
                multiple: true
            });
            frame.on('select', function(){
                var ids = JSON.parse( $('#portfolio_gallery_ids').val() || '[]' );
                frame.state().get('selection').each(function(att){
                    var id = att.get('id');
                    if ( ids.indexOf(id) !== -1 ) return;
                    ids.push(id);
                    var sizes = att.get('sizes');
                    var url   = (sizes && sizes.thumbnail) ? sizes.thumbnail.url : att.get('url');
                    $('#portfolio-gallery-wrap').append(
                        '<div class="gallery-item" style="position:relative;width:80px;height:80px;">' +
                        '<img src="' + url + '" style="width:80px;height:80px;object-fit:cover;display:block;">' +
                        '<button type="button" class="remove-gallery-img" data-id="' + id + '" ' +
                        'style="position:absolute;top:2px;right:2px;background:#cc0000;color:#fff;border:none;border-radius:2px;padding:0 5px;cursor:pointer;font-size:12px;line-height:1.6;">×</button>' +
                        '</div>'
                    );
                    $('#portfolio_gallery_ids').val(JSON.stringify(ids));
                });
            });
            frame.open();
        });

        $(document).on('click', '.remove-gallery-img', function(){
            var id  = parseInt( $(this).data('id'), 10 );
            var ids = JSON.parse( $('#portfolio_gallery_ids').val() || '[]' );
            ids = ids.filter(function(i){ return i !== id; });
            $('#portfolio_gallery_ids').val(JSON.stringify(ids));
            $(this).closest('.gallery-item').remove();
        });
    });
    </script>
    <?php
}

function dayanarc_save_portfolio_gallery( $post_id ) {
    if ( ! isset( $_POST['portfolio_gallery_nonce'] ) || ! wp_verify_nonce( $_POST['portfolio_gallery_nonce'], 'dayanarc_gallery_nonce' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $ids = isset( $_POST['portfolio_gallery_ids'] ) ? json_decode( wp_unslash( $_POST['portfolio_gallery_ids'] ), true ) : [];
    if ( ! is_array( $ids ) ) $ids = [];
    $ids = array_values( array_filter( array_map( 'absint', $ids ) ) );

    if ( empty( $ids ) ) {
        delete_post_meta( $post_id, '_portfolio_gallery' );
    } else {
        update_post_meta( $post_id, '_portfolio_gallery', wp_json_encode( $ids ) );
    }
}
add_action( 'save_post_portfolio', 'dayanarc_save_portfolio_gallery' );

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
        'label'   => 'About Detail Image (desktop only, opens in lightbox)',
        'section' => 'dayanarc_about',
    ] ) );
    $wp_customize->add_setting( 'about_video_url', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'about_video_url', [
        'label'       => 'About Video URL',
        'description' => 'YouTube or Vimeo URL. When set, the main image becomes a video thumbnail with a play button.',
        'section'     => 'dayanarc_about',
        'type'        => 'url',
    ] );
    $wp_customize->add_setting( 'about_video_thumb', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'about_video_thumb', [
        'label'       => 'About Video Thumbnail',
        'description' => 'Thumbnail image shown before the video plays. Falls back to About Main Image if empty.',
        'section'     => 'dayanarc_about',
    ] ) );

    // ── Our Service ───────────────────────────────────────────────────────
    $wp_customize->add_section( 'dayanarc_our_service', [
        'title'    => 'Our Service Section',
        'panel'    => 'dayanarc_homepage',
        'priority' => 23,
    ] );
    $wp_customize->add_setting( 'our_service_heading', [
        'default'           => 'OUR SERVICE',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'our_service_heading', [
        'label'   => 'Section Heading',
        'section' => 'dayanarc_our_service',
        'type'    => 'text',
    ] );
    $wp_customize->add_setting( 'our_service_description', [
        'default'           => 'From architectural vision to flawless execution — our integrated services cover every discipline, every scale, and every geography.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'our_service_description', [
        'label'   => 'Section Description',
        'section' => 'dayanarc_our_service',
        'type'    => 'textarea',
    ] );
    foreach ( [
        'our_service_image_1'      => [ 'label' => 'Image 1', 'type' => 'image' ],
        'our_service_image_2'      => [ 'label' => 'Image 2', 'type' => 'image' ],
    ] as $key => $field ) {
        $wp_customize->add_setting( $key, [ 'default' => '', 'sanitize_callback' => 'esc_url_raw', 'transport' => 'refresh' ] );
        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $key, [
            'label'   => $field['label'],
            'section' => 'dayanarc_our_service',
        ] ) );
    }
    foreach ( [
        'our_service_image_1_desc' => [ 'label' => 'Image 1 Description', 'default' => 'Concept development and schematic design services tailored to your architectural vision.' ],
        'our_service_image_2_desc' => [ 'label' => 'Image 2 Description', 'default' => 'Comprehensive construction documentation and technical drawings executed with precision.' ],
    ] as $key => $field ) {
        $wp_customize->add_setting( $key, [ 'default' => $field['default'], 'sanitize_callback' => 'sanitize_textarea_field', 'transport' => 'refresh' ] );
        $wp_customize->add_control( $key, [ 'label' => $field['label'], 'section' => 'dayanarc_our_service', 'type' => 'textarea' ] );
    }

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

    // ── Our Offices ──────────────────────────────────────────────────────
    $wp_customize->add_section( 'dayanarc_offices', [
        'title'    => 'Our Offices',
        'panel'    => 'dayanarc_general',
        'priority' => 15,
    ] );
    foreach ( [
        'office_germany' => 'Germany Office Location',
        'office_georgia' => 'Georgia Office Location',
        'office_dubai'   => 'Dubai Office Location',
    ] as $key => $label ) {
        $defaults = [
            'office_germany' => 'Berlin, Germany',
            'office_georgia' => 'Tbilisi, Georgia',
            'office_dubai'   => 'Business Bay, Dubai, UAE',
        ];
        $wp_customize->add_setting( $key, [
            'default'           => $defaults[ $key ],
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( $key, [
            'label'       => $label,
            'description' => 'Shown in the footer and links to the Contact page.',
            'section'     => 'dayanarc_offices',
            'type'        => 'text',
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
        'social_youtube'   => 'YouTube URL',
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

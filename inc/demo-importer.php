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
    $synced   = [];
    $errors   = [];

    if ( isset( $_POST['dayanarc_run_import'] ) && check_admin_referer( 'dayanarc_import_nonce' ) ) {
        $result = dayanarc_run_import();
        if ( is_wp_error( $result ) ) $errors[] = $result->get_error_message();
        else $imported = true;
    }

    if ( isset( $_POST['dayanarc_run_reset'] ) && check_admin_referer( 'dayanarc_import_nonce' ) ) {
        dayanarc_reset_content();
        $result = dayanarc_run_import();
        if ( is_wp_error( $result ) ) $errors[] = $result->get_error_message();
        else $reset = true;
    }

    if ( isset( $_POST['dayanarc_sync_selected'] ) && check_admin_referer( 'dayanarc_import_nonce' ) ) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        $selected = isset( $_POST['components'] ) ? array_map( 'sanitize_key', (array) $_POST['components'] ) : [];
        foreach ( $selected as $key ) {
            $comp = dayanarc_get_component( $key );
            if ( $comp && is_callable( $comp['fn'] ) ) {
                call_user_func( $comp['fn'] );
                $synced[] = $comp['label'];
            }
        }
        flush_rewrite_rules( false );
    }

    $check      = dayanarc_check_import_status();
    $components = dayanarc_get_components();
    ?>
    <div class="wrap">
        <h1 style="font-family:Georgia,serif;">🏛 Dayan Arc — Import &amp; Sync</h1>

        <?php if ( $imported ) : ?>
            <div class="notice notice-success is-dismissible"><p><strong>Full sync complete!</strong> <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">View site →</a></p></div>
        <?php endif; ?>

        <?php if ( $reset ) : ?>
            <div class="notice notice-success is-dismissible"><p><strong>Reset &amp; reimport complete!</strong> <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">View site →</a></p></div>
        <?php endif; ?>

        <?php if ( ! empty( $synced ) ) : ?>
            <div class="notice notice-success is-dismissible"><p><strong>Synced:</strong> <?php echo esc_html( implode( ', ', $synced ) ); ?> — <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">View site →</a></p></div>
        <?php endif; ?>

        <?php foreach ( $errors as $err ) : ?>
            <div class="notice notice-error"><p><?php echo esc_html( $err ); ?></p></div>
        <?php endforeach; ?>

        <div style="max-width:700px; margin-top:1.5rem;">

            <?php if ( $check['status'] === 'ok' ) : ?>
                <div class="notice notice-success inline" style="margin-bottom:1.5rem;"><p>✅ Site content is up to date.</p></div>
            <?php elseif ( $check['status'] === 'needs_sync' ) : ?>
                <div class="notice notice-warning inline" style="margin-bottom:1.5rem;"><p>⚠️ Some values are out of sync with theme files. Run a sync to apply.</p></div>
            <?php else : ?>
                <div class="notice notice-error inline" style="margin-bottom:1.5rem;"><p>🔴 Content not fully set up. Use <strong>Reset &amp; Reimport Everything</strong> below.</p></div>
            <?php endif; ?>

            <!-- ── Component selector ──────────────────────────────────────── -->
            <form method="post">
                <?php wp_nonce_field( 'dayanarc_import_nonce' ); ?>

                <h3 style="margin-bottom:.25rem;">Select what to sync</h3>
                <p style="color:#666;font-size:13px;margin-top:0;margin-bottom:1rem;">
                    Only the checked items will be updated. Unchanged files are skipped automatically (MD5 hash check).
                </p>

                <table class="wp-list-table widefat striped" style="margin-bottom:1rem;">
                    <thead>
                        <tr>
                            <th style="width:36px;padding:8px 10px;">
                                <input type="checkbox" id="da-select-all" title="Select / deselect all">
                            </th>
                            <th style="padding:8px 10px;">Component</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $components as $key => $comp ) : ?>
                        <tr>
                            <td style="padding:10px;"><input type="checkbox" name="components[]" value="<?php echo esc_attr( $key ); ?>"></td>
                            <td style="padding:10px;">
                                <strong><?php echo $comp['label']; ?></strong><br>
                                <span style="color:#666;font-size:12px;"><?php echo esc_html( $comp['desc'] ); ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <script>
                document.getElementById('da-select-all').addEventListener('change', function () {
                    document.querySelectorAll('input[name="components[]"]').forEach(function (cb) {
                        cb.checked = document.getElementById('da-select-all').checked;
                    });
                });
                </script>

                <?php submit_button( 'Sync Selected', 'primary large', 'dayanarc_sync_selected', false ); ?>
            </form>

            <!-- ── Import Portfolio Images (AJAX) ──────────────────────────── -->
            <div style="margin-top:2rem; padding:1rem; background:#f0f6fc; border-left:4px solid #0073aa;">
                <h3 style="margin:0 0 .4rem;">Import Portfolio Images</h3>
                <p style="color:#555;font-size:13px;margin:0 0 .75rem;">
                    Registers portfolio images as real WordPress attachments (featured image + gallery).<br>
                    Run this <strong>after</strong> syncing Portfolio Projects. Processes one project per request to avoid timeouts.
                </p>
                <button id="da-img-import-btn" class="button button-primary">Start Image Import</button>
                <div id="da-img-progress" style="margin-top:.75rem;font-size:13px;color:#333;display:none;">
                    <div id="da-img-bar-wrap" style="background:#ddd;border-radius:3px;overflow:hidden;height:10px;margin-bottom:.5rem;">
                        <div id="da-img-bar" style="background:#0073aa;height:10px;width:0%;transition:width .3s;"></div>
                    </div>
                    <div id="da-img-log" style="max-height:140px;overflow-y:auto;font-family:monospace;font-size:12px;background:#fff;padding:.5rem;border:1px solid #ddd;border-radius:3px;"></div>
                </div>
            </div>

            <script>
            (function(){
                var btn   = document.getElementById('da-img-import-btn');
                var prog  = document.getElementById('da-img-progress');
                var bar   = document.getElementById('da-img-bar');
                var log   = document.getElementById('da-img-log');
                var nonce = '<?php echo esc_js( wp_create_nonce( 'dayanarc_img_chunk_nonce' ) ); ?>';
                var total = 15;
                var done  = 0;

                function addLog(msg){ log.innerHTML += msg + '<br>'; log.scrollTop = log.scrollHeight; }
                function setBar(pct){ bar.style.width = pct + '%'; }

                function runChunk(){
                    fetch(ajaxurl, {
                        method:'POST',
                        headers:{'Content-Type':'application/x-www-form-urlencoded'},
                        body:'action=dayanarc_portfolio_img_chunk&nonce='+encodeURIComponent(nonce)
                    })
                    .then(function(r){ return r.json(); })
                    .then(function(r){
                        if(!r.success){ addLog('❌ Error: '+(r.data||'unknown')); btn.disabled=false; btn.textContent='Retry'; return; }
                        var d = r.data;
                        if(d.skipped){ addLog('⚠️ Skipped — '+d.message); }
                        else { done++; addLog('✅ '+(d.message||d.title)); }
                        setBar(Math.min(100, Math.round((done/total)*100)));
                        if(d.done){ addLog('<strong>🎉 All done! All portfolio images imported.</strong>'); btn.disabled=false; btn.textContent='Done'; return; }
                        runChunk();
                    })
                    .catch(function(e){ addLog('❌ Network error: '+e); btn.disabled=false; btn.textContent='Retry'; });
                }

                btn.addEventListener('click', function(){
                    btn.disabled = true;
                    btn.textContent = 'Importing…';
                    prog.style.display = 'block';
                    log.innerHTML = '';
                    done = 0;
                    setBar(0);
                    runChunk();
                });
            })();
            </script>

            <!-- ── Sync all ─────────────────────────────────────────────────── -->
            <details style="margin-top:2rem;">
                <summary style="cursor:pointer;font-weight:600;color:#1a5c2e;font-size:14px;">▶ Sync all components at once</summary>
                <div style="margin-top:.75rem;padding:.75rem 1rem;background:#d4edda;border-left:4px solid #28a745;">
                    <p style="margin:0 0 .75rem;font-size:13px;">Runs every component in one shot. Unchanged files are skipped.</p>
                    <form method="post">
                        <?php wp_nonce_field( 'dayanarc_import_nonce' ); ?>
                        <?php submit_button( 'Sync All', 'secondary', 'dayanarc_run_import', false ); ?>
                    </form>
                </div>
            </details>

            <!-- ── Reset ────────────────────────────────────────────────────── -->
            <details style="margin-top:.75rem;">
                <summary style="cursor:pointer;font-weight:600;color:#856404;font-size:14px;">▶ Reset &amp; Reimport Everything (destructive)</summary>
                <div style="margin-top:.75rem;padding:.75rem 1rem;background:#fff3cd;border-left:4px solid #ffc107;">
                    <p style="margin:0 0 .75rem;font-size:13px;">Deletes <em>all</em> importer-managed images and clears every stored ID and theme mod, then runs a full fresh import. Use only when content looks seriously broken.</p>
                    <form method="post" onsubmit="return confirm('This will delete all imported images and reset all content. Are you sure?');">
                        <?php wp_nonce_field( 'dayanarc_import_nonce' ); ?>
                        <?php submit_button( 'Reset & Reimport Everything', 'secondary', 'dayanarc_run_reset', false, [ 'style' => 'background:#856404;color:#fff;border-color:#856404;' ] ); ?>
                    </form>
                </div>
            </details>

        </div>
    </div>
    <?php
}

// ── Component registry ────────────────────────────────────────────────────────
function dayanarc_get_components() {
    return [
        'logos'       => [
            'label' => 'Header &amp; Footer Logos',
            'desc'  => 'PNG logo used in the site header and footer',
            'fn'    => 'dayanarc_import_logo_images',
        ],
        'about'       => [
            'label' => 'About Section Images',
            'desc'  => 'Main image and detail image in the About Us section',
            'fn'    => 'dayanarc_import_about_images',
        ],
        'our_service' => [
            'label' => 'What We Do Images',
            'desc'  => 'Two side-by-side images in the Our Service (What We Do) section',
            'fn'    => 'dayanarc_import_our_service_images',
        ],
        'services'    => [
            'label' => 'Service Cards (6)',
            'desc'  => '6 service pages with titles, descriptions, and thumbnail images',
            'fn'    => 'dayanarc_import_services_full',
        ],
        'portfolio'   => [
            'label' => 'Portfolio Projects',
            'desc'  => '15 portfolio projects with gallery images from uploaded files',
            'fn'    => 'dayanarc_import_portfolio',
        ],
        'journal'     => [
            'label' => 'Journal / Blog Posts',
            'desc'  => '3 journal posts with featured images',
            'fn'    => 'dayanarc_import_journal_full',
        ],
        'pages'       => [
            'label' => 'Static Pages &amp; Contact Form',
            'desc'  => 'Home, Journal, Portfolio, Contact pages + CF7 contact form',
            'fn'    => 'dayanarc_import_pages_full',
        ],
        'text'        => [
            'label' => 'Site Text &amp; Copy',
            'desc'  => 'All headings, taglines, descriptions, social links, and contact info',
            'fn'    => 'dayanarc_apply_content_theme_mods',
        ],
        'menu'        => [
            'label' => 'Navigation Menu',
            'desc'  => 'Primary navigation menu links',
            'fn'    => 'dayanarc_import_nav_menu',
        ],
    ];
}

function dayanarc_get_component( $key ) {
    $components = dayanarc_get_components();
    return isset( $components[ $key ] ) ? $components[ $key ] : null;
}

// ── Status check: tells us whether sync, reset, or nothing is needed ──────────
function dayanarc_check_import_status() {
    $problems = [];
    $warnings = [];

    // Service pages — all 6 must exist
    $missing_svc = 0;
    foreach ( [
        'dayanarc_service_architecture_id',
        'dayanarc_service_interior_design_id',
        'dayanarc_service_3d_viz_id',
        'dayanarc_service_project_mgmt_id',
        'dayanarc_service_5_id',
        'dayanarc_service_6_id',
    ] as $opt ) {
        $id = (int) get_option( $opt );
        if ( ! $id || ! get_post( $id ) ) $missing_svc++;
    }
    if ( $missing_svc ) $problems[] = "$missing_svc of 6 service pages not found";

    // Portfolio posts — all 15 must exist
    $portfolio_count = (int) wp_count_posts( 'portfolio' )->publish;
    if ( $portfolio_count < 15 ) $problems[] = "Only $portfolio_count of 15 portfolio projects found";

    // Logos must be set
    if ( ! get_theme_mod( 'header_logo_id' ) ) $problems[] = 'Header logo not imported';
    if ( ! get_theme_mod( 'footer_logo_id' ) ) $problems[] = 'Footer logo not imported';

    // Key theme mod values must match current manifest
    foreach ( [
        'our_service_heading' => 'WHAT WE DO',
        'portfolio_heading'   => 'OUR PROJECTS',
        'journal_heading'     => 'LATEST PROJECTS',
        'hero_word_1'         => 'VISION.',
    ] as $key => $expected ) {
        if ( get_theme_mod( $key, '' ) !== $expected ) {
            $warnings[] = "Outdated value: <code>$key</code>";
        }
    }

    // Social links must not be empty or placeholder
    $instagram = get_theme_mod( 'social_instagram', '' );
    if ( ! $instagram || $instagram === '#' ) {
        $warnings[] = 'Social links are missing or set to placeholder values';
    }

    if ( ! empty( $problems ) ) {
        return [ 'status' => 'needs_reset', 'problems' => $problems, 'warnings' => $warnings ];
    }
    if ( ! empty( $warnings ) ) {
        return [ 'status' => 'needs_sync', 'problems' => [], 'warnings' => $warnings ];
    }
    return [ 'status' => 'ok', 'problems' => [], 'warnings' => [] ];
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

// ── Reset: clear all stored IDs, theme mods, and importer-managed images ──────
// Called before a fresh reimport so nothing is treated as "already done".
function dayanarc_reset_content() {
    // Delete all importer-managed attachments (smart-import and legacy)
    $managed = get_posts( [
        'post_type'      => 'attachment',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [ [
            'relation' => 'OR',
            [ 'key' => '_source_file_path',    'compare' => 'EXISTS' ],
            [ 'key' => '_dayanarc_source_file', 'compare' => 'EXISTS' ],
            [ 'key' => '_dayan_upload_path',    'compare' => 'EXISTS' ],
        ] ],
    ] );
    foreach ( $managed as $att_id ) {
        wp_delete_attachment( $att_id, true );
    }

    // Clear _portfolio_thumb_imported so AJAX importer starts fresh
    $portfolio_posts = get_posts( [
        'post_type'      => 'portfolio',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ] );
    foreach ( $portfolio_posts as $pid ) {
        delete_post_meta( $pid, '_portfolio_thumb_imported' );
    }

    // Clear all option IDs stored by the importer
    foreach ( [
        'dayanarc_service_architecture_id',
        'dayanarc_service_interior_design_id',
        'dayanarc_service_3d_viz_id',
        'dayanarc_service_project_mgmt_id',
        'dayanarc_service_5_id',
        'dayanarc_service_6_id',
        'dayanarc_portfolio_moon_restaurant_id',
        'dayanarc_portfolio_signaghi_warehouse_id',
        'dayanarc_portfolio_alhudaib_villa_id',
        'dayanarc_portfolio_ankara_tower_id',
        'dayanarc_portfolio_hotel_ballroom_id',
        'dayanarc_portfolio_frankfurt_villa_id',
        'dayanarc_portfolio_cologne_warehouse_id',
        'dayanarc_portfolio_french_restaurant_id',
        'dayanarc_portfolio_nini_villa_id',
        'dayanarc_portfolio_yas_palace_id',
        'dayanarc_portfolio_emirates_hills_id',
        'dayanarc_portfolio_private_villa_dubai_id',
        'dayanarc_portfolio_mina_residential_id',
        'dayanarc_portfolio_erbil_penthouse_id',
        'dayanarc_portfolio_sharjah_warehouse_id',
        'dayanarc_contact_form_id',
        'dayanarc_contact_page_id',
        'dayanarc_portfolio_page_id',
    ] as $opt ) {
        delete_option( $opt );
    }

    // Clear all theme mods set by the importer
    foreach ( [
        'header_logo_id', 'footer_logo_id',
        'about_image_main', 'about_image_detail',
        'our_service_image_1', 'our_service_image_2',
        'about_video_url',
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
    ] as $mod ) {
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

// ── Image imports split by component ─────────────────────────────────────────

function dayanarc_import_logo_images() {
    $dir = get_template_directory() . '/content/images/';
    foreach ( [
        [ 'file' => 'logos/header.png', 'key' => 'header_logo_id' ],
        [ 'file' => 'logos/footer.png', 'key' => 'footer_logo_id' ],
    ] as $entry ) {
        $result = dayanarc_smart_import_file( $dir . $entry['file'] );
        if ( ! is_wp_error( $result ) ) set_theme_mod( $entry['key'], $result['id'] );
    }
}

function dayanarc_import_about_images() {
    $dir = get_template_directory() . '/content/images/';
    foreach ( [
        [ 'file' => 'about/main.webp',   'key' => 'about_image_main' ],
        [ 'file' => 'about/detail.webp', 'key' => 'about_image_detail' ],
    ] as $entry ) {
        $result = dayanarc_smart_import_file( $dir . $entry['file'] );
        if ( ! is_wp_error( $result ) ) set_theme_mod( $entry['key'], wp_get_attachment_url( $result['id'] ) );
    }
}

function dayanarc_import_our_service_images() {
    $dir = get_template_directory() . '/content/images/';
    foreach ( [
        [ 'file' => 'our-service/image-1.jpg', 'key' => 'our_service_image_1' ],
        [ 'file' => 'our-service/image-2.png', 'key' => 'our_service_image_2' ],
    ] as $entry ) {
        $result = dayanarc_smart_import_file( $dir . $entry['file'] );
        if ( ! is_wp_error( $result ) ) set_theme_mod( $entry['key'], wp_get_attachment_url( $result['id'] ) );
    }
}

function dayanarc_import_service_thumbnails() {
    $dir = get_template_directory() . '/content/images/';
    foreach ( [
        [ 'folder' => 'services/architecture/',           'option' => 'dayanarc_service_architecture_id' ],
        [ 'folder' => 'services/industrial-warehouse/',   'option' => 'dayanarc_service_interior_design_id' ],
        [ 'folder' => 'services/structural-engineering/', 'option' => 'dayanarc_service_3d_viz_id' ],
        [ 'folder' => 'services/mep-smart-systems/',      'option' => 'dayanarc_service_project_mgmt_id' ],
        [ 'folder' => 'services/facade-landscape/',       'option' => 'dayanarc_service_5_id' ],
        [ 'folder' => 'services/technical-coordination/', 'option' => 'dayanarc_service_6_id' ],
    ] as $entry ) {
        $result = dayanarc_smart_import_file( $dir . $entry['folder'] . 'thumbnail.png' );
        if ( ! is_wp_error( $result ) ) {
            $post_id = (int) get_option( $entry['option'] );
            if ( $post_id && get_post( $post_id ) ) set_post_thumbnail( $post_id, $result['id'] );
        }
    }
}

// Wrapper — keeps dayanarc_run_import() working unchanged
function dayanarc_import_content_images() {
    dayanarc_import_logo_images();
    dayanarc_import_about_images();
    dayanarc_import_our_service_images();
    dayanarc_import_service_thumbnails();
}

// ── Component wrapper functions ───────────────────────────────────────────────

function dayanarc_import_services_full() {
    dayanarc_import_service_pages();
    dayanarc_import_service_thumbnails();
}

function dayanarc_import_journal_full() {
    $ids = dayanarc_import_images();
    dayanarc_import_journal_posts( $ids );
}

function dayanarc_import_pages_full() {
    dayanarc_import_home_page();
    dayanarc_import_journal_page();
    dayanarc_import_portfolio_page();
    dayanarc_import_contact_form();
    dayanarc_import_contact_page();
}

// ── Helper: register an image already present in wp-content/uploads/ ─────────
// Inserts an attachment record with basic dimension metadata.
// Skips wp_generate_attachment_metadata() (thumbnail resizing) to avoid timeouts.
function dayanarc_register_upload_image( $file_path, $title = '' ) {
    if ( ! file_exists( $file_path ) ) return 0;

    $norm = wp_normalize_path( $file_path );

    $existing = get_posts( [
        'post_type'      => 'attachment',
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_query'     => [ [ 'key' => '_dayan_upload_path', 'value' => $norm ] ],
    ] );
    if ( $existing ) return (int) $existing[0];

    $upload_dir = wp_upload_dir();
    $base_dir   = wp_normalize_path( $upload_dir['basedir'] );
    $file_url   = str_replace( $base_dir, $upload_dir['baseurl'], $norm );

    $mime   = wp_check_filetype( $file_path );
    $att_id = wp_insert_attachment( [
        'guid'           => $file_url,
        'post_mime_type' => $mime['type'] ?: 'image/webp',
        'post_title'     => $title ?: pathinfo( $file_path, PATHINFO_FILENAME ),
        'post_status'    => 'inherit',
    ], $file_path );

    if ( is_wp_error( $att_id ) || ! $att_id ) return 0;

    // Store minimal metadata (dimensions only) — avoids slow thumbnail generation
    $size = @getimagesize( $file_path );
    if ( $size ) {
        wp_update_attachment_metadata( $att_id, [
            'width'  => $size[0],
            'height' => $size[1],
            'file'   => str_replace( trailingslashit( $base_dir ), '', $norm ),
            'sizes'  => [],
        ] );
    }

    update_post_meta( $att_id, '_dayan_upload_path', $norm );

    return (int) $att_id;
}

// ── 2. Portfolio projects (15 projects) ──────────────────────────────────────
// Creates posts with text only; stores _portfolio_folder for the AJAX image importer.
// Images are imported separately via dayanarc_ajax_portfolio_img_chunk().
function dayanarc_import_portfolio() {
    // Delete all existing portfolio posts before creating fresh ones
    $existing = get_posts( [
        'post_type'      => 'portfolio',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ] );
    foreach ( $existing as $pid ) {
        wp_delete_post( $pid, true );
    }

    $projects = [
        [
            'option'    => 'dayanarc_portfolio_moon_restaurant_id',
            'folder'    => ' 002  MOON RESTURANT',
            'title'     => 'MOON RESTAURANT & TEA LOUNGE',
            'excerpt'   => 'A commercial hospitality project in Georgia using fluid, curvilinear spatial zoning to orchestrate guest circulation and elevate the dining experience into a visual narrative.',
            'location'  => 'Georgia',
            'year'      => '2018',
            'typology'  => 'Commercial Food & Beverage Architecture',
            'aesthetic' => 'Hospitality-Driven Elegance, Curvilinear Zoning, Textural Gastronomy',
            'materials' => 'White Engineered Stone, Walnut Veneers, Textured Silver Leaf, Bas-Relief Stone, Wool Weave Fabrics',
            'scope'     => 'Full Cycle (Concept, Detail Design & Site Supervision)',
            'content'   => '<p>This commercial food and beverage hospitality project relies on fluid, curvilinear spatial zoning to orchestrate guest circulation and elevate the dining experience into a visual narrative. Departing from rigid commercial grids, the design incorporates sweeping architectural enclosures, bespoke organic millwork, and micro-zoned seating clusters that balance grand presentation with cozy, human-scale privacy.</p>
<p>The Tea Lounge volume is shaped by a sweeping, semi-circular screen wall featuring integrated artisanal display niches for curation, reflecting an overhead oval ceiling recess finished in textured silver leaf. Soft, tub-shaped armchairs upholstered in textured grey wool weave create a spacious yet communal landscape over seamless, high-polished marble flooring.</p>
<p>The dining zone introduces a richer material canvas featuring deep walnut-toned paneling and structured geometric shelving units, anchored by a monolithic capsule-shaped buffet island clad in seamless white engineered stone, backed by a dramatic wall featuring custom-carved floral bas-relief masonry.</p>',
        ],
        [
            'option'    => 'dayanarc_portfolio_signaghi_warehouse_id',
            'folder'    => ' 014 warehouse heorgia signaghi',
            'title'     => 'SIGNAGHI INDUSTRIAL WAREHOUSE',
            'excerpt'   => 'An industrial architecture project in Signaghi optimizing a high-clearance warehouse shell through calculated tectonic insertions, maximizing floor-area ratio through a comprehensive steel mezzanine framework.',
            'location'  => 'Signaghi, Georgia',
            'year'      => '2026',
            'typology'  => 'Industrial Warehouse & Office Architecture',
            'aesthetic' => 'Industrial Tectonics, Pragmatic Minimalism, Structural Optimization',
            'materials' => 'I-Beams, HSS Steel Columns, Prefabricated Decking, Insulated Sandwich Panels, Concrete Screed',
            'scope'     => 'Full Cycle (Concept, Detail Design & Structural Supervision)',
            'content'   => '<p>This industrial architecture project in Signaghi optimizes a high-clearance warehouse shell through calculated tectonic insertions. The design prioritizes structural integrity and precise spatial coordination, introducing a comprehensive steel mezzanine framework to maximize floor-area ratio. By exposing the industrial skeleton and treating functional circulation as a prominent graphic element, the space achieves maximum storage and operational efficiency while maintaining an uncompromised structural layout.</p>
<p>The vertical volume is zoned via a heavy-duty structural steel mezzanine system, supported by a precision matrix of hollow structural section (HSS) columns. Vertical movement is handled by a custom-fabricated steel utility staircase with open risers, bordered by a modular, high-visibility white safety guardrail system that wraps continuously around the mezzanine perimeter.</p>',
        ],
        [
            'option'    => 'dayanarc_portfolio_alhudaib_villa_id',
            'folder'    => '001 ALHUDAIB VILLA',
            'title'     => 'ALHUDAIB VILLA',
            'excerpt'   => 'A private villa in Istanbul reinterpreting contemporary minimalism by treating structural mass as a sculptural entity — bold stacked rectangular volumes and extensive floor-to-ceiling glass envelopes.',
            'location'  => 'Istanbul, Turkey',
            'year'      => '2026',
            'typology'  => 'Private Residential Villa',
            'aesthetic' => 'Contemporary Minimalism, Cantilevered Geometry, Transparent Volumes',
            'materials' => 'White Architectural Render, Dark Basalt Stone Cladding, Panoramic Glass, Anodized Aluminium',
            'scope'     => 'Full Cycle (Concept, Detail Design & Site Supervision)',
            'content'   => '<p>This private villa architecture in Istanbul reinterprets contemporary minimalism by treating structural mass as a sculptural entity. Positioned to engage with the natural terrain, the design trades traditional solid perimeter walls for bold, stacked rectangular volumes and extensive floor-to-ceiling glass envelopes. The architecture establishes an intentional visual tension between heavy, grounded stone surfaces and dramatic, floating cantilevers that reach out toward the landscape.</p>
<p>The exterior architecture utilizes a multi-level layout of intersecting horizontal planes. A heavy, dark-textured natural stone block forms the foundational anchor of the lower level, supporting an upper residential volume finished in smooth white architectural render that cantilevers sharply over the outdoor terrace. Floor-to-ceiling panoramic glass panels completely dissolve the barrier between indoor spaces and manicured lawns.</p>',
        ],
        [
            'option'    => 'dayanarc_portfolio_ankara_tower_id',
            'folder'    => '003 ANKARA RESIDENTIAL TOWER',
            'title'     => 'ANKARA RESIDENTIAL TOWER',
            'excerpt'   => 'A mid-rise residential architecture in Ankara introducing soft, undulating floor plates that mimic organic movement, establishing a prominent landmark in the city\'s evolving architectural fabric.',
            'location'  => 'Ankara, Turkey',
            'year'      => '2024',
            'typology'  => 'Luxury High-Rise Residential',
            'aesthetic' => 'Modern Sensual Minimalism, Organic High-Rise Contours, Biophilic Integration',
            'materials' => 'White Architectural Render, Slatted Exterior Timber, Panoramic Glass, Curved Frameless Glass Balustrades',
            'scope'     => 'Full Cycle (Concept, Detail Design & Site Supervision)',
            'content'   => '<p>Breaking away from rigid, block-like urban high-rises, this mid-rise residential architecture introduces soft, undulating floor plates that mimic organic movement. The design language emphasizes continuous horizontal bands and sweeping curvilinear balconies, creating a highly dynamic facade that balances deep transparency with structural warmth, establishing a prominent landmark within Ankara\'s evolving architectural fabric.</p>
<p>The facade is defined by staggered, floating concrete balconies with softly rounded edges, underscored by warm, natural timber-clad soffits. Floor-to-ceiling panoramic glass windows maximize natural daylight while sweeping views are framed by lush, integrated balcony planters that inject vibrant biophilic elements directly into the vertical architecture.</p>',
        ],
        [
            'option'    => 'dayanarc_portfolio_hotel_ballroom_id',
            'folder'    => '004 BRIDAL SALON',
            'title'     => '5-STAR HOTEL BALLROOM',
            'excerpt'   => 'A luxury hospitality interior in Georgia coordinating massive spatial volume with a multi-layered ornamental envelope — deep grid geometry and highly reflective faceted planes creating a grand, celebratory atmosphere.',
            'location'  => 'Georgia',
            'year'      => '2019',
            'typology'  => 'Luxury Hospitality Architecture',
            'aesthetic' => 'High-End Hospitality, Layered Geometry, Textural Opulence',
            'materials' => 'Faceted Glass Panels, Mashrabiya Metal Lattice, Polished Marble, Brass Trim, Velvet',
            'scope'     => 'Full Cycle (Concept, Detail Design & Site Supervision)',
            'content'   => '<p>This hospitality interior architecture coordinates massive spatial volume with an intricate, multi-layered ornamental envelope. The design utilizes deep grid geometry and highly reflective, faceted planes to expand the overhead limits of the space, creating a grand, celebratory atmosphere where clean structural framing balances highly detailed surface textures.</p>
<p>The ceiling functions as the primary architectural focal point, executed via a deeply recessed coffered grid with intricate, faceted geometric glass paneling mimicking raw crystal formations, outlined by integrated linear brass channels. Monolithic stone portals frame expansive wall panels inset with mashrabiya-inspired metal lattice screens, while a grand multi-flight monumental staircase wrapped in custom metal balustrades serves as the primary visual anchor of the sprawling, double-height volume.</p>',
        ],
        [
            'option'    => 'dayanarc_portfolio_frankfurt_villa_id',
            'folder'    => '005 frankfurt villa n01villa',
            'title'     => 'FRANKFURT VILLA',
            'excerpt'   => 'A residential interior in Frankfurt coordinating a highly precise European minimal layout with warm organic accents — bold monochromatic volumes, graphite fluted paneling, and a biophilic vertical green wall.',
            'location'  => 'Frankfurt, Germany',
            'year'      => '2017',
            'typology'  => 'Private Residential Villa',
            'aesthetic' => 'High-Contrast Minimalism, European Contemporary, Biophilic Integration',
            'materials' => 'White Marble, Matte Black Lacquer, Slatted Timber, Fluted Panels, Concrete Screed',
            'scope'     => 'Full Cycle (Concept, Detail Design & Site Supervision)',
            'content'   => '<p>This residential interior architecture coordinates a highly precise, European minimal layout with warm organic accents to mitigate Germany\'s overcast seasonal light. The spatial hierarchy centers on a bold monochromatic foundation — matte black volumes, graphite fluted paneling, and clean concrete screed floors — intersected by rich timber surfaces and lush indoor verticality.</p>
<p>The open-concept kitchen and lounge represent a study in stark, sophisticated contrast: floor-to-ceiling cabinetry in ultra-matte black creates a seamless structural wall integrated with a professional wine-cellar enclosure, broken by a monolithic white marble island. The formal dining space shifts toward a softer canvas with a rich timber slatted baffle ceiling and a dense, floor-to-ceiling vertical green wall that brings natural life directly into the interior.</p>',
        ],
        [
            'option'    => 'dayanarc_portfolio_cologne_warehouse_id',
            'folder'    => '006 germany-koln-warehouse',
            'title'     => 'COLOGNE INDUSTRIAL WAREHOUSE',
            'excerpt'   => 'An industrial architecture project in Cologne focusing on structural modification of a high-volume warehouse facility, utilizing heavy-duty skeletal framing to insert a wide-span mezzanine deck.',
            'location'  => 'Cologne, Germany',
            'year'      => '2024',
            'typology'  => 'Industrial Warehouse & Office Architecture',
            'aesthetic' => 'Pragmatic Tectonics, Utilitarian Minimalism, Structural Logistics',
            'materials' => 'Wide-Flange Steel Columns, Steel Gabled Trusses, Corrugated Roof Panels, Concrete Slab, Skylights',
            'scope'     => 'Full Cycle (Concept, Detail Design & On-Site Structural Supervision)',
            'content'   => '<p>This industrial architecture project in Cologne focuses on the core structural modification of a high-volume warehouse facility to meet intensive logistics and operational demands. The intervention centers on a calculated space-planning strategy, utilizing heavy-duty skeletal framing to insert a wide-span mezzanine deck — leaving engineering components exposed and optimizing natural light penetration for high volumetric flexibility.</p>
<p>The main architectural insertion consists of a rigid structural steel platform system coated in an anti-corrosive industrial forest green finish. The gabled steel truss system supports corrugated metal roof sandwich panels, integrated with rows of linear frosted skylight panels that flood the facility with natural daylight. A continuous steel safety guardrail wraps the upper mezzanine edge while a deep concrete vehicle ramp connects the internal floor to external shipping points.</p>',
        ],
        [
            'option'    => 'dayanarc_portfolio_french_restaurant_id',
            'folder'    => '007 paris-france-FRENCH RESTURANT',
            'title'     => 'JE T\'AIME FRENCH RESTAURANT',
            'excerpt'   => 'A fine-dining restaurant interior in Paris capturing the romance of the city through bold, high-contrast structural intervention — monumental intersecting arches, deep crimson planes, and highly reflective metallic accents.',
            'location'  => 'Paris, France',
            'year'      => '2018',
            'typology'  => 'Commercial Food & Beverage Architecture',
            'aesthetic' => 'Contemporary Parisian, High-Contrast Luxury, Dramatic Symmetrical Forms',
            'materials' => 'Brushed Gold Leaf, Velvet Fabric Wall Coverings, Dark Marble, Crystal Rods, Velvet Upholstery',
            'scope'     => 'Full Cycle (Concept, Detail Design & Site Supervision)',
            'content'   => '<p>This fine-dining restaurant interior architecture captures the romance of Paris through a bold, high-contrast structural intervention. The design updates classical French symmetry with a contemporary, theatrical pulse — using monumental intersecting arches, deep crimson vertical planes, and highly reflective metallic accents to partition the expansive dining room into intimate, atmospheric sub-zones.</p>
<p>The main spatial volume is structured by a series of sweeping, sharp-pointed gothic arches finished in rich, brushed gold leaf running sequentially through the floor plan. Deep crimson fabric wall coverings provide a velvet-textured backdrop that contrasts with the clean cream masonry. The culinary theater is grounded by dark, heavily veined marble dining tables paired with curved tub chairs in muted taupe velvet, while massive rectangular crystal-rod chandeliers cascade from the arches overhead.</p>',
        ],
        [
            'option'    => 'dayanarc_portfolio_nini_villa_id',
            'folder'    => '008 NINI VILLA',
            'title'     => 'NINI VILLA',
            'excerpt'   => 'A residential architecture project in Batumi reinterpreting classical European monumentalism for a modern coastal context — strict axial symmetry, deep window embrasures, and rhythmic structural massing.',
            'location'  => 'Batumi, Georgia',
            'year'      => '2025',
            'typology'  => 'Private Residential Villa',
            'aesthetic' => 'Classical European, Neoclassical Symmetry, Coastal Permanence',
            'materials' => 'Architectural Plaster Molding, Light Stone Cladding, Wrought Iron, Dark-Aluminium Glazing Profiles',
            'scope'     => 'Full Cycle (Concept, Detail Design & Site Supervision)',
            'content'   => '<p>This residential architecture project in Batumi reinterprets classical European monumentalism for a modern coastal context. Moving away from purely decorative historical pastiche, the design focuses on strict axial symmetry, deep window embrasures, and rhythmic structural massing. The resulting exterior presents a powerful, enduring silhouette that responds gracefully to changing daylight while maintaining absolute privacy for the private estate.</p>
<p>The primary facade is defined by a rigid grid of classical pilasters and pristine white plaster molding work, balanced by clean, dark-profiled window framing and minimalist wrought-iron balustrades. The lower level incorporates a deep, stone-framed loggia creating a soft structural transition between the landscaped gardens and the interior core, while integrated accent lighting shifts the monumental facade into a soft, illuminated sculpture at night.</p>',
        ],
        [
            'option'    => 'dayanarc_portfolio_yas_palace_id',
            'folder'    => '009 villa riyaz ahmed',
            'title'     => 'YAS PALACE VILLA',
            'excerpt'   => 'A palatial estate interior in Riyadh synthesizing grand neoclassical volumes with custom-tailored luxury elements — soaring verticality, axial symmetry, and highly detailed textural surfaces.',
            'location'  => 'Riyadh, Saudi Arabia',
            'year'      => '2025',
            'typology'  => 'Private Residential Villa',
            'aesthetic' => 'Neoclassical Palatial, Royal Majlis Elegance, Intricate Symmetry',
            'materials' => 'Gold Onyx, Hand-Carved Timber, Plaster Molding, Silk Wall Coverings, Bronze, Crystal',
            'scope'     => 'Full Cycle (Concept, Detail Design & Site Supervision)',
            'content'   => '<p>This palatial estate interior architecture synthesizes grand neoclassical volumes with custom-tailored luxury elements. Designed to function effortlessly for both intimate residential living and high-profile formal hosting, the spatial layout relies on soaring verticality, axial symmetry, and highly detailed textural surfaces to establish a permanent sense of architectural permanence.</p>
<p>The Grand Majlis Hall — a double-height volume for formal assembly — is anchored by a massive custom-framed golden mosaic mural juxtaposed against a monumental hand-carved timber portal door. The formal dining hall is defined by repeating wall molding panels inset with silk fabric coverings and illuminated by a trio of crystal chandeliers. The powder room features a custom-engineered monolithic counter sculpted from heavily veined gold onyx, set against hand-painted silk wall coverings depicting subtle organic motifs.</p>',
        ],
        [
            'option'    => 'dayanarc_portfolio_emirates_hills_id',
            'folder'    => '010 VILLA ABU DHABI',
            'title'     => 'EMIRATES HILLS VILLA',
            'excerpt'   => 'A private villa interior in Emirates Hills embracing dark, grounded materiality — monolithic bronze columns, deep timber planes, and expansive glazing zoning a highly communicative family living and dining hall.',
            'location'  => 'Dubai, UAE',
            'year'      => '2026',
            'typology'  => 'Private Residential Villa',
            'aesthetic' => 'Sensual Minimalism, Earthy Luxury, Bold Structural Contrasts',
            'materials' => 'Brushed Bronze, Dark Walnut Veneer, Slate Micro-Cement, Matte Stone Tiles, Linen',
            'scope'     => 'Full Cycle (Concept, Detail Design & Site Supervision)',
            'content'   => '<p>This private villa interior architecture in Emirates Hills embraces a dark, grounded materiality that contrasts with Dubai\'s high-glare desert light. The design strips away decorative overlay to let large structural components do the visual work, relying on monolithic bronze columns, deep timber planes, and expansive glazing to zone a highly communicative family living and dining hall.</p>
<p>The open floor plan is anchored by a massive, floor-to-ceiling structural column clad in matte, brushed bronze, coordinating with an oversized custom-sculpted suspension light assembly of sweeping bronze bands. Deep walnut-veneer wall paneling intersects with textured slate-grey micro-cement slabs, while the furniture layout remains intentionally low-profile to preserve outward sightlines through panoramic floor-to-ceiling glass apertures.</p>',
        ],
        [
            'option'    => 'dayanarc_portfolio_private_villa_dubai_id',
            'folder'    => '011 Villa 06-villa erbil.no6',
            'title'     => 'PRIVATE VILLA RESIDENCE',
            'excerpt'   => 'A private residential architecture in Dubai rejecting sterile modernism in favor of layered textures and fluid geometry — massive glass envelopes combined with architectural screening and rich material thresholds.',
            'location'  => 'Dubai, UAE',
            'year'      => '2026',
            'typology'  => 'Private Residential Villa',
            'aesthetic' => 'Sensual Minimalism, High-End Residential, Organic Geometry',
            'materials' => 'Raw Stone Cladding, Walnut Veneer, Bas-Relief Stone, Velvet, Fluted Glass',
            'scope'     => 'Full Cycle (Concept, Detail Design & Site Supervision)',
            'content'   => '<p>This private residential architecture rejects sterile modernism in favor of layered textures and fluid geometry. The design prioritizes spatial liberation, using massive floor-to-ceiling glass envelopes to invite natural light while employing architectural screening, deep recesses, and rich material thresholds to maintain absolute residential sanctuary.</p>
<p>Double-height architectural volumes are grounded by a monumental, raw-textured stone cladding feature wall paired with a cascading linear light installation. The private powder rooms feature deep, curved walnut-veneer wall panels framing twin arched vanities with custom-sculpted stone bas-relief work highlighted by hidden halo illumination. The master and guest suites feature seamless architectural wall transitions integrating flush-mount doors, fluted glass sliders, and built-in minimalist wardrobes.</p>',
        ],
        [
            'option'    => 'dayanarc_portfolio_mina_residential_id',
            'folder'    => '012 mina residential',
            'title'     => 'MINA RESIDENTIAL TOWER',
            'excerpt'   => 'A residential interior in Riyadh centered on Sensual Minimalism — where sharp angles yield to organic curves and human-centric proportions create an understated, earthy sanctuary.',
            'location'  => 'Riyadh, Saudi Arabia',
            'year'      => '2023',
            'typology'  => 'Luxury High-Rise Residential',
            'aesthetic' => 'Sensual Minimalism, Warm Organic, Monochromatic Luxury',
            'materials' => 'Travertine, Linear Timber, Fluted Rose Marble, Bouclé Fabrics, Micro-Cement',
            'scope'     => 'Full Cycle (Concept, Detail Design & Site Supervision)',
            'content'   => '<p>Departing from rigid high-rise geometry, this interior architecture centers on Sensual Minimalism — where sharp angles yield to organic curves and human-centric proportions. The spatial flow is deliberate, establishing an understated, earthy sanctuary amid Riyadh\'s urban landscape through seamless, open-plan connectivity.</p>
<p>Visual rhythm dictates the overhead volume: the lounge introduces a dynamic linear timber baffle ceiling for structural depth, contrasting with seamless micro-cement walls and warm cream travertine floor slabs. The master bath extends the micro-minimalist aesthetic with soft stone-textured wall tiles punctuated by a striking fluted accent wall in muted rose marble, floating stone vanities, and concealed under-cabinet halo lighting.</p>',
        ],
        [
            'option'    => 'dayanarc_portfolio_erbil_penthouse_id',
            'folder'    => '013 PENTHOUSE LUAY',
            'title'     => 'ERBIL PENTHOUSE',
            'excerpt'   => 'An exclusive penthouse interior in Erbil challenging standard open-plan layouts through structural transparency — operable metallic partitions and glass dividers balancing grand communal visibility with intimate privacy.',
            'location'  => 'Erbil',
            'year'      => '2019',
            'typology'  => 'Luxury High-Rise Residential',
            'aesthetic' => 'High-End Hospitality-Infused Residential, Bold Geometric Luxury',
            'materials' => 'Polished Gold Metal, Fluted Glass, High-Gloss Marble, Velvet, Dark Wood',
            'scope'     => 'Full Cycle (Concept, Detail Design & Site Supervision)',
            'content'   => '<p>Designed for an exclusive penthouse within a prominent residential tower, this interior architecture challenges standard open-plan layouts through structural transparency. By utilizing operable, high-polished metallic partitions and glass dividers, the space balances grand communal visibility with immediate, intimate privacy — the flow feels naturally continuous, guided by deep perspective lines and a deliberate interplay of light and reflection.</p>
<p>Space division relies on custom-fabricated, floor-to-ceiling gold metallic folding screens and fluted glass panels that allow the main living lounge to open toward both an indoor pool sanctuary and a dramatic sunken majlis. High-gloss marble floor slabs run throughout the circulation paths, contrasted by dark wood decking surrounding the pool and the plush velvet upholstery of the deep-set sunken lounge.</p>',
        ],
        [
            'option'    => 'dayanarc_portfolio_sharjah_warehouse_id',
            'folder'    => '016 -warehouse sharjeh',
            'title'     => 'SHARJAH INDUSTRIAL WAREHOUSE',
            'excerpt'   => 'An industrial interior in Sharjah converting a vast warehouse shell into a multi-level corporate hub — a precise floating mezzanine insertion and extensive steel-framed glazing grids achieving absolute spatial efficiency.',
            'location'  => 'Sharjah, UAE',
            'year'      => '2026',
            'typology'  => 'Industrial Warehouse & Office Architecture',
            'aesthetic' => 'Industrial Minimalism, Structural Tectonics, High-Efficiency Workspaces',
            'materials' => 'IPE Steel Beams, Black Steel Window Profiles, Polished Concrete, Clear Glass, Minimalist Planters',
            'scope'     => 'Full Cycle (Concept, Detail Design & Construction Supervision)',
            'content'   => '<p>This industrial interior architecture converts a vast, high-volume warehouse shell into a highly functional, multi-level corporate and operational hub. Retaining the raw structural expression of the warehouse, the design introduces a precise, floating mezzanine insertion and extensive steel-framed glazing grids — achieving absolute spatial efficiency while maintaining a continuous, transparent visual field across the entire facility.</p>
<p>A custom-engineered structural mezzanine level framed with exposed IPE steel beams expands the building\'s usable footprint. Floor-to-ceiling glass systems enclosed by industrial-profile steel window grids provide acoustic isolation for administrative zones while maintaining complete management oversight of the factory floor. The material palette relies on industrial honesty: seamless polished concrete screed forms the ground flooring, softened by integrated indoor planters and warm-toned light tracks recessed into the smooth office ceilings.</p>',
        ],
    ];

    foreach ( $projects as $item ) {
        $post_id = wp_insert_post( [
            'post_title'   => $item['title'],
            'post_content' => $item['content'],
            'post_excerpt' => $item['excerpt'],
            'post_status'  => 'publish',
            'post_type'    => 'portfolio',
        ] );

        if ( is_wp_error( $post_id ) || ! $post_id ) continue;

        update_option( $item['option'], $post_id );
        update_post_meta( $post_id, '_portfolio_location',  $item['location'] );
        update_post_meta( $post_id, '_portfolio_year',      $item['year'] );
        update_post_meta( $post_id, '_portfolio_concept',   $item['scope'] );
        update_post_meta( $post_id, '_portfolio_palette',   $item['materials'] );
        update_post_meta( $post_id, '_portfolio_typology',  $item['typology'] );
        update_post_meta( $post_id, '_portfolio_aesthetic', $item['aesthetic'] );

        // Store folder name so the AJAX image importer knows where to find the files
        update_post_meta( $post_id, '_portfolio_folder', $item['folder'] );
    }
}

// ── AJAX: import images for one portfolio project at a time ──────────────────
// Called by the JS loop on the Import Demo page.
// Finds the next project without imported images, registers its files as
// proper WP attachments, sets the featured image, and saves the gallery.
add_action( 'wp_ajax_dayanarc_portfolio_img_chunk', 'dayanarc_ajax_portfolio_img_chunk' );
function dayanarc_ajax_portfolio_img_chunk() {
    check_ajax_referer( 'dayanarc_img_chunk_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Unauthorized', 403 );

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    @set_time_limit( 120 );

    $upload_dir   = wp_upload_dir();
    $projects_dir = $upload_dir['basedir'] . '/dayan projects/';

    // Find the next post that has a folder but no thumbnail yet
    $posts = get_posts( [
        'post_type'      => 'portfolio',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [
            [ 'key' => '_portfolio_folder', 'compare' => 'EXISTS' ],
            [ 'key' => '_portfolio_thumb_imported', 'compare' => 'NOT EXISTS' ],
        ],
    ] );

    if ( empty( $posts ) ) {
        wp_send_json_success( [ 'done' => true, 'message' => 'All images imported.' ] );
    }

    $post_id = (int) $posts[0];
    $folder  = get_post_meta( $post_id, '_portfolio_folder', true );
    $title   = get_the_title( $post_id );

    $folder_path = $projects_dir . $folder . '/';
    if ( ! is_dir( $folder_path ) ) {
        // Mark as done so the loop doesn't get stuck
        update_post_meta( $post_id, '_portfolio_thumb_imported', '1' );
        wp_send_json_success( [
            'done'    => false,
            'skipped' => true,
            'title'   => $title,
            'message' => "Folder not found: $folder",
        ] );
    }

    $files = [];
    foreach ( scandir( $folder_path ) as $f ) {
        if ( strtolower( pathinfo( $f, PATHINFO_EXTENSION ) ) === 'webp' ) {
            $files[] = $f;
        }
    }
    sort( $files );

    if ( empty( $files ) ) {
        update_post_meta( $post_id, '_portfolio_thumb_imported', '1' );
        wp_send_json_success( [
            'done'    => false,
            'skipped' => true,
            'title'   => $title,
            'message' => "No WebP files in folder: $folder",
        ] );
    }

    // Register cover image as WP attachment and set as featured image
    $cover_path = $folder_path . $files[0];
    $cover_id   = dayanarc_register_upload_image( $cover_path, $title );
    if ( $cover_id ) {
        set_post_thumbnail( $post_id, $cover_id );
    }

    // Register remaining images as gallery
    $gallery_ids = [];
    foreach ( array_slice( $files, 1 ) as $f ) {
        $img_id = dayanarc_register_upload_image( $folder_path . $f );
        if ( $img_id ) {
            $gallery_ids[] = $img_id;
        }
    }
    if ( ! empty( $gallery_ids ) ) {
        update_post_meta( $post_id, '_portfolio_gallery', json_encode( $gallery_ids ) );
    }

    update_post_meta( $post_id, '_portfolio_thumb_imported', '1' );

    // Count remaining
    $remaining = count( get_posts( [
        'post_type'      => 'portfolio',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [
            [ 'key' => '_portfolio_folder', 'compare' => 'EXISTS' ],
            [ 'key' => '_portfolio_thumb_imported', 'compare' => 'NOT EXISTS' ],
        ],
    ] ) );

    wp_send_json_success( [
        'done'      => ( $remaining === 0 ),
        'title'     => $title,
        'images'    => count( $files ),
        'remaining' => $remaining,
        'message'   => "Imported " . count( $files ) . " image(s) for: $title",
    ] );
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
        // Hero video
        'about_video_url'          => 'https://dayanarc.com/wp-content/uploads/videos/dayan_arc_services.mp4',
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

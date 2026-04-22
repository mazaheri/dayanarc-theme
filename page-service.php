<?php
/**
 * Template Name: Service Page
 *
 * Individual service detail page.
 * Custom fields: _service_number (e.g. "01"), _service_features (newline-separated)
 */
get_header( 'inner' );

if ( have_posts() ) :
    the_post();
    $sid             = get_the_ID();
    $service_number  = get_post_meta( $sid, '_service_number', true );
    $features_raw    = get_post_meta( $sid, '_service_features', true );
    $features        = $features_raw ? array_filter( array_map( 'trim', explode( "\n", $features_raw ) ) ) : [];
    $offer_heading   = get_post_meta( $sid, '_service_what_we_offer', true ) ?: 'WHAT WE OFFER';
    $cta_heading     = get_post_meta( $sid, '_service_cta_heading', true ) ?: 'READY TO START YOUR PROJECT?';
    $cta_desc        = get_post_meta( $sid, '_service_cta_description', true ) ?: 'Let\'s discuss your vision and bring it to life with the expertise and care that defines Dayan Arc.';
    $cta_label       = get_post_meta( $sid, '_service_cta_label', true ) ?: 'CONTACT US';
    $contact_url     = dayanarc_contact_page_url();
endif;
?>

<main style="padding-bottom:6rem;">

    <!-- ── Hero ── -->
    <div style="background:#1a1814; padding:8rem 1.5rem 5rem; position:relative; overflow:hidden;">
        <div style="max-width:1440px; margin:0 auto;">

            <?php dayanarc_breadcrumb(); ?>

            <div style="margin-top:2rem; display:flex; flex-direction:column; gap:1.5rem; max-width:860px;">
                <?php if ( $service_number ) : ?>
                    <span style="font-size:10px; letter-spacing:0.25em; text-transform:uppercase; color:#8c8783; font-weight:500;"><?php echo esc_html( $service_number ); ?></span>
                <?php endif; ?>

                <h1 class="title-text" style="font-size:clamp(3rem,8vw,6rem); line-height:0.95; letter-spacing:0.02em; color:#fff;">
                    <?php echo esc_html( strtoupper( get_the_title() ) ); ?>
                </h1>

                <?php if ( has_excerpt() ) : ?>
                    <p style="font-size:14px; line-height:1.8; color:#a9a39f; font-weight:300; max-width:480px;">
                        <?php echo esc_html( get_the_excerpt() ); ?>
                    </p>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- ── Featured image ── -->
    <?php if ( has_post_thumbnail() ) : ?>
        <div style="max-width:1440px; margin:0 auto; padding:0 1.5rem;">
            <div style="width:100%; aspect-ratio:16/7; overflow:hidden; margin-top:-3rem; position:relative; z-index:10;">
                <?php the_post_thumbnail( 'full', [
                    'style' => 'width:100%; height:100%; object-fit:cover; display:block;',
                    'alt'   => esc_attr( get_the_title() ),
                ] ); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- ── Content + features ── -->
    <div style="max-width:1440px; margin:0 auto; padding:5rem 1.5rem 0;">
        <div class="service-page-grid">

            <!-- Left: description -->
            <div>
                <span style="font-size:10px; letter-spacing:0.2em; text-transform:uppercase; color:#8c8783; font-weight:500; display:block; margin-bottom:1.5rem;">OVERVIEW</span>
                <div style="font-size:15px; line-height:1.9; color:#68635f; font-weight:300;">
                    <?php the_content(); ?>
                </div>

                <a href="<?php echo esc_url( $contact_url ); ?>"
                   style="display:inline-flex; align-items:center; gap:0.75rem; margin-top:2.5rem; font-size:11px; letter-spacing:0.15em; text-transform:uppercase; font-weight:600; color:#2c221a; text-decoration:none; border-bottom:1px solid #2c221a; padding-bottom:0.2rem;">
                    GET IN TOUCH
                    <svg width="14" height="8" viewBox="0 0 16 10" fill="none" stroke="currentColor" stroke-width="1.2">
                        <path d="M11 1L15 5M15 5L11 9M15 5H0" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>

            <!-- Right: features list -->
            <?php if ( ! empty( $features ) ) : ?>
            <div>
                <span style="font-size:10px; letter-spacing:0.2em; text-transform:uppercase; color:#8c8783; font-weight:500; display:block; margin-bottom:1.5rem;"><?php echo esc_html( $offer_heading ); ?></span>
                <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:0;">
                    <?php foreach ( $features as $feature ) : ?>
                        <li style="display:flex; align-items:center; gap:1rem; padding:1rem 0; border-bottom:1px solid #ece9e5;">
                            <span style="width:5px; height:5px; border-radius:50%; background:#2c221a; flex-shrink:0;"></span>
                            <span style="font-size:13px; letter-spacing:0.05em; text-transform:uppercase; color:#2c221a; font-weight:500;"><?php echo esc_html( $feature ); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- ── CTA strip ── -->
    <div style="background:#f5f2ee; margin-top:5rem; padding:4rem 1.5rem;">
        <div style="max-width:1440px; margin:0 auto; display:flex; flex-direction:column; align-items:center; text-align:center; gap:2rem;">
            <h2 class="title-text" style="font-size:clamp(2rem,5vw,3.5rem); color:#2c221a; line-height:1.05;">
                <?php echo esc_html( $cta_heading ); ?>
            </h2>
            <p style="font-size:13px; line-height:1.8; color:#68635f; font-weight:300; max-width:440px;">
                <?php echo esc_html( $cta_desc ); ?>
            </p>
            <a href="<?php echo esc_url( $contact_url ); ?>"
               style="display:inline-flex; align-items:center; gap:0.75rem; font-size:11px; letter-spacing:0.15em; text-transform:uppercase; font-weight:600; color:#2c221a; text-decoration:none; border-bottom:1px solid #2c221a; padding-bottom:0.2rem;">
                <?php echo esc_html( $cta_label ); ?>
                <svg width="14" height="8" viewBox="0 0 16 10" fill="none" stroke="currentColor" stroke-width="1.2">
                    <path d="M11 1L15 5M15 5L11 9M15 5H0" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
    </div>

</main>

<style>
.service-page-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 3rem;
}
@media (min-width: 1024px) {
    .service-page-grid {
        grid-template-columns: 1fr 1fr;
        gap: 5rem;
        align-items: start;
    }
}
</style>

<?php get_footer( 'inner' ); ?>

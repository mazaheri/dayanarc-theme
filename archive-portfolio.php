<?php get_header( 'inner' ); ?>

<main style="max-width:1400px; margin:0 auto; padding:8rem 1.5rem 6rem;">

    <!-- Section header -->
    <div style="text-align:center; margin-bottom:1.5rem;">
        <span style="font-size:10px; letter-spacing:0.2em; text-transform:uppercase; color:#a9a39f; font-weight:500; display:block; margin-bottom:1rem;">PORTFOLIO</span>
        <h1 class="title-text" style="font-size:clamp(2rem,5vw,4rem); line-height:1.05; letter-spacing:-0.02em; color:#2c221a; text-transform:uppercase;">OUR WORKS</h1>
    </div>

    <?php dayanarc_breadcrumb(); ?>

    <?php
    $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
    $portfolio_query = new WP_Query( [
        'post_type'      => 'portfolio',
        'posts_per_page' => 4,
        'paged'          => $paged,
        'post_status'    => 'publish',
    ] );

    $fallback_large = 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1200&q=80';
    $total          = $portfolio_query->found_posts;
    $index_offset   = ( $paged - 1 ) * 4;

    if ( $portfolio_query->have_posts() ) :
        $item_count = 0;
        while ( $portfolio_query->have_posts() ) :
            $portfolio_query->the_post();

            $location  = get_post_meta( get_the_ID(), '_portfolio_location', true ) ?: 'Riyadh, KSA';
            $concept   = get_post_meta( get_the_ID(), '_portfolio_concept',  true );
            $palette   = get_post_meta( get_the_ID(), '_portfolio_palette',  true );
            $detail_id = get_post_meta( get_the_ID(), '_portfolio_detail_image', true );
            $img_large = get_the_post_thumbnail_url( get_the_ID(), 'large' ) ?: $fallback_large;
            $img_small = $detail_id
                ? wp_get_attachment_image_url( $detail_id, 'medium' )
                : 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=600&q=80';
            $num       = str_pad( $index_offset + $item_count + 1, 2, '0', STR_PAD_LEFT ) . '/' . str_pad( $total, 2, '0', STR_PAD_LEFT );
            $excerpt   = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 30, '...' );
            $item_count++;
    ?>

        <!-- Portfolio item — matches the front-page portfolio slide layout -->
        <div class="portfolio-slide" style="margin-bottom:5rem; padding-bottom:5rem; <?php echo ! $portfolio_query->have_posts() ? '' : 'border-bottom:1px solid #e5e5e5;'; ?>">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-8 items-start">

                <!-- Left: large image -->
                <div>
                    <div class="curtain-container" style="aspect-ratio:1/1; width:100%;">
                        <img src="<?php echo esc_url( $img_large ); ?>"
                             alt="<?php echo esc_attr( get_the_title() ); ?>"
                             class="curtain-img-portfolio archive-curtain"
                             style="width:100%; height:100%; object-fit:cover; clip-path:inset(0 0 0 0); transform:scale(1);">
                    </div>
                </div>

                <!-- Right: details -->
                <div class="flex flex-col" style="min-height:400px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:3rem;">
                        <span style="font-size:12px; color:#68635f; font-weight:300;"><?php echo esc_html( $location ); ?></span>
                        <span style="font-size:12px; color:#68635f; font-weight:300; letter-spacing:0.15em;"><?php echo esc_html( $num ); ?></span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8" style="margin-bottom:auto;">
                        <div class="flex flex-col">
                            <h2 class="title-text" style="font-size:clamp(1.5rem,3vw,2.25rem); margin-bottom:1.5rem; color:#2c221a; font-weight:500; letter-spacing:0.03em; text-transform:uppercase; line-height:1.1;">
                                <?php the_title(); ?>
                            </h2>
                            <p style="font-size:14px; line-height:1.8; color:#68635f; font-weight:300;">
                                <?php echo esc_html( $excerpt ); ?>
                            </p>
                        </div>
                        <div class="flex flex-col">
                            <div class="curtain-container" style="aspect-ratio:1/1; width:100%; max-width:280px; margin-left:auto;">
                                <img src="<?php echo esc_url( $img_small ); ?>"
                                     alt="Detail"
                                     class="curtain-img-portfolio archive-curtain"
                                     style="width:100%; height:100%; object-fit:cover; clip-path:inset(0 0 0 0); transform:scale(1);">
                            </div>
                            <?php if ( $palette ) : ?>
                                <p style="font-size:11px; color:#68635f; font-weight:300; margin-top:1rem; text-align:right; max-width:280px; margin-left:auto; line-height:1.6;">
                                    <?php echo esc_html( $palette ); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="margin-top:2.5rem; padding-top:2rem; border-top:1px solid #e5e5e5; display:flex; justify-content:space-between; align-items:flex-end;">
                        <?php if ( $concept ) : ?>
                            <p style="font-size:12px; color:#68635f; font-weight:300; line-height:1.6; max-width:260px;">
                                <?php echo esc_html( $concept ); ?>
                            </p>
                        <?php else : ?>
                            <span></span>
                        <?php endif; ?>
                        <a href="<?php the_permalink(); ?>" class="link-wrapper" style="opacity:1; transform:none; width:auto; gap:1rem; min-width:160px;">
                            <span class="link-text">VIEW PROJECT</span>
                            <div class="arrow-graphic">
                                <svg width="16" height="10" viewBox="0 0 16 10" fill="none">
                                    <path d="M11 1L15 5M15 5L11 9M15 5H0" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>

    <?php
        endwhile;
        wp_reset_postdata();
    else :
    ?>
        <p style="text-align:center; color:#8c8783; font-size:13px; letter-spacing:0.1em; text-transform:uppercase; margin-top:4rem;">No portfolio items found.</p>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ( $portfolio_query->max_num_pages > 1 ) : ?>
        <div class="archive-pagination">
            <?php
            echo paginate_links( [
                'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                'format'    => '?paged=%#%',
                'current'   => $paged,
                'total'     => $portfolio_query->max_num_pages,
                'prev_text' => '<svg width="14" height="10" viewBox="0 0 24 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19 8H5M5 8L12 15M5 8L12 1" stroke-linecap="round" stroke-linejoin="round"/></svg> PREV',
                'next_text' => 'NEXT <svg width="14" height="10" viewBox="0 0 24 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 8H19M19 8L12 1M19 8L12 15" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            ] );
            ?>
        </div>
    <?php endif; ?>

</main>

<?php get_footer( 'inner' ); ?>

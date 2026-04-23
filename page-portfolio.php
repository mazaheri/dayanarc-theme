<?php
/**
 * Template Name: Portfolio
 */
get_header( 'inner' );

$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
$portfolio_query = new WP_Query( [
    'post_type'      => 'portfolio',
    'posts_per_page' => 6,
    'paged'          => $paged,
    'post_status'    => 'publish',
] );
?>

<main class="inner-page-main" style="max-width:1400px; margin:0 auto;">

    <div style="margin-bottom:2.5rem;">
        <span style="font-size:10px; letter-spacing:0.2em; text-transform:uppercase; color:#a9a39f; font-weight:500; display:block; margin-bottom:1rem;">PORTFOLIO</span>
        <h1 class="title-text" style="font-size:clamp(2rem,5vw,4rem); line-height:1.05; letter-spacing:-0.02em; color:#2c221a; text-transform:uppercase; margin-bottom:1.25rem;">OUR WORKS</h1>
        <?php dayanarc_breadcrumb(); ?>
    </div>

    <?php if ( $portfolio_query->have_posts() ) : ?>

        <div class="portfolio-archive-grid">
            <?php
            $item_count = 0;
            $total = $portfolio_query->found_posts;
            while ( $portfolio_query->have_posts() ) :
                $portfolio_query->the_post();
                $location = get_post_meta( get_the_ID(), '_portfolio_location', true ) ?: 'Riyadh, KSA';
                $num      = str_pad( ( $paged - 1 ) * 6 + $item_count + 1, 2, '0', STR_PAD_LEFT ) . '/' . str_pad( $total, 2, '0', STR_PAD_LEFT );
                $excerpt  = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 22, '...' );
                $img      = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
                $item_count++;
            ?>
                <a href="<?php the_permalink(); ?>" style="display:block; text-decoration:none; color:inherit;">

                    <?php if ( $img ) : ?>
                        <div style="width:100%; aspect-ratio:4/3; overflow:hidden; margin-bottom:1.25rem;">
                            <img src="<?php echo esc_url( $img ); ?>"
                                 alt="<?php echo esc_attr( get_the_title() ); ?>"
                                 loading="lazy"
                                 style="width:100%; height:100%; object-fit:cover; display:block; transition:transform 0.6s ease;">
                        </div>
                    <?php endif; ?>

                    <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:0.5rem;">
                        <span style="font-size:10px; letter-spacing:0.15em; text-transform:uppercase; color:#a9a39f; font-weight:500;"><?php echo esc_html( $location ); ?></span>
                        <span style="font-size:10px; letter-spacing:0.1em; color:#a9a39f;"><?php echo esc_html( $num ); ?></span>
                    </div>

                    <h2 class="title-text" style="font-size:clamp(1.1rem,2vw,1.5rem); line-height:1.15; letter-spacing:0.02em; text-transform:uppercase; color:#2c221a; margin:0 0 0.75rem;">
                        <?php the_title(); ?>
                    </h2>

                    <p style="font-size:13px; line-height:1.7; color:#68635f; font-weight:300; margin:0 0 1rem;">
                        <?php echo esc_html( $excerpt ); ?>
                    </p>

                    <span class="link-wrapper" style="opacity:1; transform:none; width:auto; display:inline-flex; gap:0.75rem; min-width:auto;">
                        <span class="link-text" style="font-size:10px;">VIEW PROJECT</span>
                        <div class="arrow-graphic">
                            <svg width="14" height="9" viewBox="0 0 16 10" fill="none">
                                <path d="M11 1L15 5M15 5L11 9M15 5H0" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </span>

                </a>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <?php if ( $portfolio_query->max_num_pages > 1 ) : ?>
            <div class="archive-pagination" style="margin-top:4rem;">
                <?php
                echo paginate_links( [
                    'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                    'format'    => '?paged=%#%',
                    'current'   => $paged,
                    'total'     => $portfolio_query->max_num_pages,
                    'prev_text' => '← PREV',
                    'next_text' => 'NEXT →',
                ] );
                ?>
            </div>
        <?php endif; ?>

    <?php else : ?>
        <p style="text-align:center; color:#8c8783; font-size:13px; letter-spacing:0.1em; text-transform:uppercase; margin-top:4rem;">No portfolio items found.</p>
    <?php endif; ?>

</main>

<?php get_footer( 'inner' ); ?>

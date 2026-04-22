<?php
/**
 * Journal / Blog archive — mosaic grid with Load More.
 * Activated when this page is set as the blog posts index (is_home()).
 */
get_header( 'inner' );

$per_page = 8; // initial posts; AJAX loads 4 more per click

$journal_query = new WP_Query( [
    'post_type'      => 'post',
    'posts_per_page' => $per_page,
    'paged'          => 1,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
] );

$total_posts = $journal_query->found_posts;
?>

<main style="max-width:1400px; margin:0 auto; padding:8rem 1.5rem 6rem;">

    <!-- Header -->
    <div style="margin-bottom:2rem;">
        <span style="font-size:10px; letter-spacing:0.2em; text-transform:uppercase; color:#a9a39f; font-weight:500; display:block; margin-bottom:0.75rem;">JOURNAL</span>
        <h1 class="title-text" style="font-size:clamp(2rem,5vw,4rem); line-height:1.05; letter-spacing:-0.02em; color:#2c221a; text-transform:uppercase; margin-bottom:1.25rem;">DESIGN INSIGHTS</h1>
        <?php dayanarc_breadcrumb(); ?>
    </div>

    <?php if ( ! $journal_query->have_posts() ) : ?>
        <p style="text-align:center; color:#8c8783; font-size:13px; letter-spacing:0.1em; text-transform:uppercase; margin-top:4rem;">No posts found.</p>
    <?php else : ?>

        <!-- Mosaic grid -->
        <div id="journal-mosaic-grid" class="journal-mosaic-grid">
            <?php
            $i = 0;
            while ( $journal_query->have_posts() ) :
                $journal_query->the_post();
                echo dayanarc_render_journal_card( get_post(), $i );
                $i++;
            endwhile;
            wp_reset_postdata();
            ?>
        </div>

        <!-- Load More -->
        <?php if ( $total_posts > $per_page ) : ?>
            <div style="text-align:center; margin-top:3.5rem;">
                <button id="journal-load-more"
                        class="journal-load-more-btn"
                        data-offset="<?php echo esc_attr( $per_page ); ?>"
                        data-total="<?php echo esc_attr( $total_posts ); ?>">
                    LOAD MORE
                    <svg width="14" height="8" viewBox="0 0 16 10" fill="none" stroke="currentColor" stroke-width="1.2">
                        <path d="M8 1L8 9M8 9L4 5M8 9L12 5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        <?php endif; ?>

    <?php endif; ?>

</main>

<?php get_footer( 'inner' ); ?>

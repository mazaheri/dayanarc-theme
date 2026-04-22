<?php get_header( 'inner' ); ?>

<main style="max-width:860px; margin:0 auto; padding:8rem 2rem 6rem;">
    <?php if ( have_posts() ) : the_post(); ?>

        <div style="margin-bottom:1.5rem;">
            <span style="font-size:10px; letter-spacing:0.2em; text-transform:uppercase; color:#a9a39f; font-weight:500;">
                JOURNAL &mdash; <?php echo esc_html( get_the_date( 'F j, Y' ) ); ?>
            </span>
        </div>

        <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3.5rem); line-height:1.1; letter-spacing:-0.02em; margin-bottom:2.5rem; text-transform:uppercase;">
            <?php the_title(); ?>
        </h1>

        <?php if ( has_post_thumbnail() ) : ?>
            <div style="width:100%; aspect-ratio:16/9; overflow:hidden; margin-bottom:3rem;">
                <?php the_post_thumbnail( 'full', [ 'style' => 'width:100%; height:100%; object-fit:cover;' ] ); ?>
            </div>
        <?php endif; ?>

        <?php dayanarc_breadcrumb(); ?>

        <div style="font-size:15px; line-height:1.9; color:#4a4540; font-weight:300; max-width:680px; margin-top:2.5rem;">
            <?php the_content(); ?>
        </div>

        <!-- Previous / Next navigation -->
        <div style="margin-top:5rem; padding-top:3rem; border-top:1px solid #e5e5e5; display:flex; justify-content:space-between; gap:2rem; font-size:11px; letter-spacing:0.1em; text-transform:uppercase;">
            <?php
            $prev = get_previous_post();
            $next = get_next_post();
            ?>
            <?php if ( $prev ) : ?>
                <a href="<?php echo esc_url( get_permalink( $prev->ID ) ); ?>"
                   style="text-decoration:none; color:#8c8783; display:flex; align-items:center; gap:0.75rem; transition:color 0.3s;"
                   onmouseover="this.style.color='#2c221a'" onmouseout="this.style.color='#8c8783'">
                    <svg width="14" height="10" viewBox="0 0 24 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M19 8H5M5 8L12 15M5 8L12 1" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?php echo esc_html( strtoupper( $prev->post_title ) ); ?>
                </a>
            <?php else : ?>
                <span></span>
            <?php endif; ?>

            <?php if ( $next ) : ?>
                <a href="<?php echo esc_url( get_permalink( $next->ID ) ); ?>"
                   style="text-decoration:none; color:#8c8783; display:flex; align-items:center; gap:0.75rem; transition:color 0.3s;"
                   onmouseover="this.style.color='#2c221a'" onmouseout="this.style.color='#8c8783'">
                    <?php echo esc_html( strtoupper( $next->post_title ) ); ?>
                    <svg width="14" height="10" viewBox="0 0 24 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M5 8H19M19 8L12 1M19 8L12 15" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</main>

<?php get_footer( 'inner' ); ?>

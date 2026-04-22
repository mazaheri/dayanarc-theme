<?php get_header( 'inner' ); ?>

<main style="max-width:860px; margin:0 auto; padding:8rem 2rem 6rem;">
    <?php if ( have_posts() ) : the_post(); ?>

        <?php dayanarc_breadcrumb(); ?>

        <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3.5rem); line-height:1.1; letter-spacing:-0.02em; margin-bottom:2.5rem; text-transform:uppercase;">
            <?php the_title(); ?>
        </h1>

        <?php if ( has_post_thumbnail() ) : ?>
            <div style="width:100%; aspect-ratio:16/9; overflow:hidden; margin-bottom:3rem;">
                <?php the_post_thumbnail( 'full', [ 'style' => 'width:100%; height:100%; object-fit:cover;' ] ); ?>
            </div>
        <?php endif; ?>

        <?php $location = get_post_meta( get_the_ID(), '_portfolio_location', true ); ?>

        <div style="margin-bottom:1.5rem;">
            <span style="font-size:10px; letter-spacing:0.2em; text-transform:uppercase; color:#a9a39f; font-weight:500;">
                PORTFOLIO<?php if ( $location ) : ?> &mdash; <?php echo esc_html( $location ); ?><?php endif; ?>
            </span>
        </div>

        <div style="font-size:15px; line-height:1.9; color:#4a4540; font-weight:300; max-width:680px;">
            <?php the_content(); ?>
        </div>

        <?php
        $concept = get_post_meta( get_the_ID(), '_portfolio_concept', true );
        $palette = get_post_meta( get_the_ID(), '_portfolio_palette', true );
        if ( $concept || $palette ) :
        ?>
            <div style="margin-top:3rem; padding-top:2rem; border-top:1px solid #e5e5e5; display:flex; gap:4rem; flex-wrap:wrap;">
                <?php if ( $concept ) : ?>
                    <div>
                        <span style="font-size:10px; letter-spacing:0.15em; text-transform:uppercase; color:#a9a39f; display:block; margin-bottom:0.4rem;">CONCEPT</span>
                        <p style="font-size:13px; color:#2c221a;"><?php echo esc_html( $concept ); ?></p>
                    </div>
                <?php endif; ?>
                <?php if ( $palette ) : ?>
                    <div>
                        <span style="font-size:10px; letter-spacing:0.15em; text-transform:uppercase; color:#a9a39f; display:block; margin-bottom:0.4rem;">PALETTE</span>
                        <p style="font-size:13px; color:#2c221a;"><?php echo esc_html( $palette ); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Navigation between portfolio items -->
        <div style="margin-top:5rem; padding-top:3rem; border-top:1px solid #e5e5e5; display:flex; justify-content:space-between; gap:2rem; font-size:11px; letter-spacing:0.1em; text-transform:uppercase;">
            <?php
            $prev = get_previous_post( false, '', '' );
            $next = get_next_post( false, '', '' );
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

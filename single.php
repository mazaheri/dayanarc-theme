<?php get_header( 'inner' ); ?>

<main>
<?php if ( have_posts() ) : the_post(); ?>

    <?php if ( has_post_thumbnail() ) : ?>
        <!-- Hero image with title + date overlaid -->
        <div class="single-hero">
            <?php the_post_thumbnail( 'full', [ 'class' => 'single-hero-img', 'alt' => esc_attr( get_the_title() ) ] ); ?>
            <div class="single-hero-overlay">
                <div class="single-hero-content">
                    <span class="single-hero-meta">
                        JOURNAL &mdash; <?php echo esc_html( get_the_date( 'F j, Y' ) ); ?>
                    </span>
                    <h1 class="single-hero-title title-text">
                        <?php echo esc_html( strtoupper( get_the_title() ) ); ?>
                    </h1>
                </div>
            </div>
        </div>
    <?php else : ?>
        <!-- No featured image: text-only header -->
        <div class="single-no-hero">
            <span style="font-size:10px; letter-spacing:0.2em; text-transform:uppercase; color:#a9a39f; font-weight:500; display:block; margin-bottom:0.75rem;">
                JOURNAL &mdash; <?php echo esc_html( get_the_date( 'F j, Y' ) ); ?>
            </span>
            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3.5rem); line-height:1.1; letter-spacing:-0.02em; text-transform:uppercase; color:#2c221a; margin-bottom:0;">
                <?php echo esc_html( strtoupper( get_the_title() ) ); ?>
            </h1>
        </div>
    <?php endif; ?>

    <!-- Breadcrumb -->
    <div style="max-width:860px; margin:0 auto; padding:1.5rem 2rem 0;">
        <?php dayanarc_breadcrumb(); ?>
    </div>

    <!-- Article content -->
    <article style="max-width:860px; margin:0 auto; padding:2.5rem 2rem 6rem;">
        <div style="font-size:15px; line-height:1.9; color:#4a4540; font-weight:300;">
            <?php the_content(); ?>
        </div>

        <!-- Previous / Next navigation -->
        <div style="margin-top:5rem; padding-top:2.5rem; border-top:1px solid #e5e5e5; display:flex; justify-content:space-between; align-items:center; gap:2rem; font-size:11px; letter-spacing:0.1em; text-transform:uppercase;">
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
                   style="text-decoration:none; color:#8c8783; display:flex; align-items:center; gap:0.75rem; transition:color 0.3s; text-align:right;"
                   onmouseover="this.style.color='#2c221a'" onmouseout="this.style.color='#8c8783'">
                    <?php echo esc_html( strtoupper( $next->post_title ) ); ?>
                    <svg width="14" height="10" viewBox="0 0 24 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M5 8H19M19 8L12 1M19 8L12 15" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </article>

<?php endif; ?>
</main>

<?php get_footer( 'inner' ); ?>

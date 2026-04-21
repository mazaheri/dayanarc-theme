<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <style>
        body { overflow-y: auto !important; font-family: 'Inter', sans-serif; background: #fff; color: #2c221a; }
        * { scrollbar-width: thin; scrollbar-color: #d1ccc8 transparent; }
    </style>
</head>
<body <?php body_class( 'single-post-page' ); ?>>
<?php wp_body_open(); ?>

    <!-- Single post navigation bar -->
    <nav style="position:fixed; top:0; left:0; right:0; z-index:50; background:rgba(255,255,255,0.95); backdrop-filter:blur(10px); border-bottom:1px solid #e5e5e5; padding:1.25rem 2rem; display:flex; justify-content:space-between; align-items:center;">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="font-family:'Cormorant Garamond',serif; font-size:1.1rem; letter-spacing:0.15em; text-transform:uppercase; text-decoration:none; color:#2c221a;">DAYAN ARC</a>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="font-size:11px; letter-spacing:0.1em; text-transform:uppercase; text-decoration:none; color:#8c8783; display:flex; align-items:center; gap:0.5rem;">
            <svg width="14" height="10" viewBox="0 0 24 16" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M19 8H5M5 8L12 15M5 8L12 1" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Back to Home
        </a>
    </nav>

    <main style="max-width:860px; margin:0 auto; padding:8rem 2rem 6rem;">
        <?php if ( have_posts() ) : the_post(); ?>

            <?php if ( has_post_thumbnail() ) : ?>
                <div style="width:100%; aspect-ratio:16/9; overflow:hidden; margin-bottom:3rem;">
                    <?php the_post_thumbnail( 'full', [ 'style' => 'width:100%; height:100%; object-fit:cover;' ] ); ?>
                </div>
            <?php endif; ?>

            <div style="margin-bottom:1.5rem;">
                <span style="font-size:10px; letter-spacing:0.2em; text-transform:uppercase; color:#a9a39f; font-weight:500;">
                    JOURNAL &mdash; <?php echo esc_html( get_the_date( 'F j, Y' ) ); ?>
                </span>
            </div>

            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3.5rem); line-height:1.1; letter-spacing:-0.02em; margin-bottom:2.5rem; text-transform:uppercase;">
                <?php the_title(); ?>
            </h1>

            <div style="font-size:15px; line-height:1.9; color:#4a4540; font-weight:300; max-width:680px;">
                <?php the_content(); ?>
            </div>

            <!-- Navigation between posts -->
            <div style="margin-top:5rem; padding-top:3rem; border-top:1px solid #e5e5e5; display:flex; justify-content:space-between; gap:2rem; font-size:11px; letter-spacing:0.1em; text-transform:uppercase;">
                <?php
                $prev = get_previous_post();
                $next = get_next_post();
                ?>
                <?php if ( $prev ) : ?>
                    <a href="<?php echo esc_url( get_permalink( $prev->ID ) ); ?>" style="text-decoration:none; color:#8c8783; display:flex; align-items:center; gap:0.75rem; transition:color 0.3s;" onmouseover="this.style.color='#2c221a'" onmouseout="this.style.color='#8c8783'">
                        <svg width="14" height="10" viewBox="0 0 24 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19 8H5M5 8L12 15M5 8L12 1" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Previous
                    </a>
                <?php else : ?>
                    <span></span>
                <?php endif; ?>
                <?php if ( $next ) : ?>
                    <a href="<?php echo esc_url( get_permalink( $next->ID ) ); ?>" style="text-decoration:none; color:#8c8783; display:flex; align-items:center; gap:0.75rem; transition:color 0.3s;" onmouseover="this.style.color='#2c221a'" onmouseout="this.style.color='#8c8783'">
                        Next
                        <svg width="14" height="10" viewBox="0 0 24 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 8H19M19 8L12 1M19 8L12 15" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </main>

    <footer style="background:#f5f2ee; padding:3rem 2rem; text-align:center; font-size:10px; letter-spacing:0.15em; text-transform:uppercase; color:#8c8783;">
        COPYRIGHT <?php echo esc_html( date( 'Y' ) ); ?> &copy; DAYAN ARC &mdash;
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:inherit; text-decoration:none;">Return Home</a>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>

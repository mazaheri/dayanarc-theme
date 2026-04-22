<?php
/**
 * 404 Not Found template.
 * WordPress sets the 404 HTTP header before loading this file.
 * We call status_header(404) explicitly to be certain.
 */
status_header( 404 );
nocache_headers();
get_header( 'inner' );
?>

<main style="max-width:860px; margin:0 auto; padding:8rem 2rem 6rem; min-height:60vh; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;">

    <span style="font-size:10px; letter-spacing:0.2em; text-transform:uppercase; color:#8c8783; font-weight:500; display:block; margin-bottom:2rem;">ERROR 404</span>

    <h1 class="title-text" style="font-size:clamp(4rem,14vw,9rem); line-height:0.95; letter-spacing:0.02em; color:#2c221a; margin-bottom:2.5rem;">
        NOT<br>FOUND
    </h1>

    <p style="font-size:14px; line-height:1.8; color:#68635f; font-weight:300; max-width:360px; margin-bottom:3rem;">
        The page you're looking for doesn't exist or has been moved. Let's get you back on track.
    </p>

    <a href="<?php echo esc_url( home_url( '/' ) ); ?>"
       style="font-size:11px; letter-spacing:0.15em; text-transform:uppercase; font-weight:600; color:#2c221a; text-decoration:none; display:inline-flex; align-items:center; gap:0.75rem; border-bottom:1px solid #2c221a; padding-bottom:0.2rem;">
        BACK TO HOME
        <svg width="14" height="8" viewBox="0 0 16 10" fill="none" stroke="currentColor" stroke-width="1.2">
            <path d="M11 1L15 5M15 5L11 9M15 5H0" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

</main>

<?php get_footer( 'inner' ); ?>

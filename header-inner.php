<?php
/**
 * Inner page header — used by blog posts, portfolio pages, and other inner pages.
 * Called via get_header( 'inner' ).
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'inner-page' ); ?>>
<?php wp_body_open(); ?>

<nav class="inner-nav" id="inner-nav">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inner-nav-logo hero-title">DAYAN ARC</a>

    <div class="inner-nav-desktop">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-link inner-nav-link">HOME</a>
        <a href="<?php echo esc_url( dayanarc_portfolio_url() ); ?>" class="nav-link inner-nav-link">PORTFOLIO</a>
        <a href="<?php echo esc_url( dayanarc_journal_url() ); ?>" class="nav-link inner-nav-link">JOURNAL</a>
        <a href="<?php echo esc_url( dayanarc_contact_page_url() ); ?>" class="nav-link inner-nav-link">CONTACT</a>
    </div>

    <button id="menuBtn" class="inner-menu-btn" aria-label="Toggle menu">
        <svg id="menuIcon" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
</nav>

<!-- Mobile slide-in menu (same IDs as front-page so main.js handles toggle) -->
<div id="mobileMenu" class="mobile-menu">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-link inner-mobile-link">HOME</a>
    <a href="<?php echo esc_url( dayanarc_portfolio_url() ); ?>" class="nav-link inner-mobile-link">PORTFOLIO</a>
    <a href="<?php echo esc_url( dayanarc_journal_url() ); ?>" class="nav-link inner-mobile-link">JOURNAL</a>
    <a href="<?php echo esc_url( dayanarc_contact_page_url() ); ?>" class="nav-link inner-mobile-link">CONTACT</a>
</div>
<div id="menuOverlay" class="menu-overlay"></div>

<!-- Scroll to top (inner pages — window.scrollY based, not fullPage API) -->
<button id="innerScrollTop" class="inner-scroll-top" aria-label="Scroll to top">
    <svg width="16" height="16" viewBox="0 0 16 10" fill="none" stroke="#2c221a" stroke-width="1.2">
        <path d="M8 9L8 1M8 1L4 5M8 1L12 5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</button>

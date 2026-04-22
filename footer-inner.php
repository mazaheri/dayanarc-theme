<?php
/**
 * Inner page footer — matches the front-page footer design.
 * Called via get_footer( 'inner' ).
 */
?>

<footer class="fp-footer pt-16 pb-6 w-full flex flex-col relative">

    <div class="w-full max-w-[1440px] mx-auto px-6 md:px-12 lg:px-20 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8 mb-12">

        <!-- Brand -->
        <div class="flex flex-col">
            <div class="title-text text-2xl tracking-widest mb-6 font-medium">DAYAN ARC</div>
            <p class="text-[12px] leading-relaxed font-light max-w-[220px]"><?php echo esc_html( get_theme_mod( 'footer_tagline', 'Bringing together creativity, expertise, and passion to deliver exceptional design solutions.' ) ); ?></p>
        </div>

        <!-- Menu -->
        <div class="grid grid-cols-[auto_1fr] gap-4 lg:gap-8">
            <div><span class="text-[10px] uppercase tracking-[0.15em] font-medium footer-muted">MENU</span></div>
            <nav class="flex flex-col gap-4 text-[11px] font-semibold tracking-widest uppercase">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-link">ABOUT US</a>
                <a href="<?php echo esc_url( dayanarc_portfolio_url() ); ?>" class="footer-link">PORTFOLIO</a>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>#services-section" class="footer-link">SERVICES</a>
                <a href="<?php echo esc_url( dayanarc_journal_url() ); ?>" class="footer-link">JOURNAL</a>
            </nav>
        </div>

        <!-- Social -->
        <div class="grid grid-cols-[auto_1fr] gap-4 lg:gap-8">
            <div><span class="text-[10px] uppercase tracking-[0.15em] font-medium footer-muted">FOLLOW US</span></div>
            <nav class="flex flex-col gap-4 text-[11px] font-semibold tracking-widest uppercase">
                <a href="<?php echo esc_url( get_theme_mod( 'social_instagram', '#' ) ); ?>" class="footer-link">INSTAGRAM</a>
                <a href="<?php echo esc_url( get_theme_mod( 'social_pinterest', '#' ) ); ?>" class="footer-link">PINTEREST</a>
                <a href="<?php echo esc_url( get_theme_mod( 'social_behance',   '#' ) ); ?>" class="footer-link">BEHANCE</a>
                <a href="<?php echo esc_url( get_theme_mod( 'social_linkedin',  '#' ) ); ?>" class="footer-link">LINKEDIN</a>
            </nav>
        </div>

        <!-- Contact -->
        <div class="grid grid-cols-[auto_1fr] gap-4 lg:gap-8">
            <div><span class="text-[10px] uppercase tracking-[0.15em] font-medium footer-muted">CONTACT</span></div>
            <div class="flex flex-col gap-4 text-[11px] font-semibold tracking-widest uppercase leading-relaxed">
                <?php
                $location        = get_theme_mod( 'contact_location', 'Business Bay, Dubai, UAE' );
                $email           = get_theme_mod( 'contact_email',    'support@dayanarc.com' );
                $website         = get_theme_mod( 'contact_website',  'http://dayanarc.com' );
                $website_display = preg_replace( '#^https?://#i', '', $website );
                ?>
                <p><?php echo esc_html( strtoupper( $location ) ); ?></p>
                <a href="mailto:<?php echo antispambot( $email ); ?>" class="footer-link lowercase"><?php echo antispambot( $email ); ?></a>
                <a href="<?php echo esc_url( $website ); ?>" class="footer-link lowercase"><?php echo esc_html( $website_display ); ?></a>
            </div>
        </div>

    </div>

    <!-- Marquee -->
    <div class="marquee-container mb-6">
        <div class="marquee-content title-text">
            <span class="marquee-text">
                GET IN TOUCH <div class="diamond"></div>
                <span class="fancy-marquee">GET IN TOUCH</span> <div class="diamond"></div>
                GET IN TOUCH <div class="diamond"></div>
                <span class="fancy-marquee">GET IN TOUCH</span> <div class="diamond"></div>
            </span>
            <span class="marquee-text">
                GET IN TOUCH <div class="diamond"></div>
                <span class="fancy-marquee">GET IN TOUCH</span> <div class="diamond"></div>
                GET IN TOUCH <div class="diamond"></div>
                <span class="fancy-marquee">GET IN TOUCH</span> <div class="diamond"></div>
            </span>
        </div>
    </div>

    <!-- Copyright -->
    <div class="flex flex-col items-center">
        <div class="w-1/5 h-[1px] mb-4" style="background:rgba(246,240,218,0.2);"></div>
        <div class="text-center text-[10px] tracking-widest uppercase font-medium footer-muted">
            COPYRIGHT <?php echo esc_html( date( 'Y' ) ); ?> &copy; DESIGNED BY <a href="https://valasolution.com/" target="_blank" rel="noopener noreferrer" class="footer-link">VALASOLUTION</a>
        </div>
    </div>

</footer>

<?php wp_footer(); ?>
</body>
</html>

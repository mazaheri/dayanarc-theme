<?php
/**
 * Inner page footer — identical design to the front-page footer.
 * Called via get_footer( 'inner' ).
 */
?>

<footer class="fp-footer pt-16 pb-6 w-full flex flex-col relative">

    <div class="w-full max-w-[1440px] mx-auto px-6 md:px-12 lg:px-20 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 lg:gap-10 mb-12">

        <!-- Col 1: Logo + tagline + social icons -->
        <div class="flex flex-col">
            <div class="mb-6">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="display:inline-block; text-decoration:none;">
                    <img src="<?php echo esc_url( wp_get_attachment_image_url( get_theme_mod( 'footer_logo_id', 87 ), 'full' ) ); ?>" alt="<?php bloginfo( 'name' ); ?>" style="height:48px; width:auto;">
                </a>
            </div>
            <p class="text-[12px] leading-relaxed font-light max-w-[260px] mb-8"><?php echo esc_html( get_theme_mod( 'footer_tagline', 'Bringing together creativity, expertise, and passion to deliver exceptional design solutions.' ) ); ?></p>

            <!-- Social icons -->
            <?php
            $s_instagram = get_theme_mod( 'social_instagram', '' );
            $s_linkedin  = get_theme_mod( 'social_linkedin',  '' );
            $s_facebook  = get_theme_mod( 'social_facebook',  '' );
            $s_phone     = get_theme_mod( 'social_phone',     '' );
            $s_whatsapp  = get_theme_mod( 'social_whatsapp',  '' );
            $has_social  = $s_instagram || $s_linkedin || $s_facebook || $s_phone || $s_whatsapp;
            if ( $has_social ) : ?>
            <div class="flex items-center gap-4 flex-wrap">
                <?php if ( $s_instagram ) : ?>
                <a href="<?php echo esc_url( $s_instagram ); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-icon" aria-label="Instagram">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="currentColor" stroke="none"/></svg>
                </a>
                <?php endif; ?>
                <?php if ( $s_linkedin ) : ?>
                <a href="<?php echo esc_url( $s_linkedin ); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-icon" aria-label="LinkedIn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
                </a>
                <?php endif; ?>
                <?php if ( $s_facebook ) : ?>
                <a href="<?php echo esc_url( $s_facebook ); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-icon" aria-label="Facebook">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                </a>
                <?php endif; ?>
                <?php if ( $s_phone ) : ?>
                <a href="tel:<?php echo esc_attr( preg_replace( '/[^+0-9]/', '', $s_phone ) ); ?>" class="footer-social-icon" aria-label="Call us">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.68A2 2 0 012 .99h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 8.87a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                </a>
                <?php endif; ?>
                <?php if ( $s_whatsapp ) : ?>
                <a href="https://wa.me/<?php echo esc_attr( preg_replace( '/[^0-9]/', '', $s_whatsapp ) ); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-icon" aria-label="WhatsApp">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Col 2: Contact -->
        <div class="flex flex-col gap-2">
            <span class="text-[10px] uppercase tracking-[0.15em] font-medium footer-muted mb-4">CONTACT</span>
            <?php
            $location        = get_theme_mod( 'contact_location', 'Business Bay, Dubai, UAE' );
            $email           = get_theme_mod( 'contact_email',    'support@dayanarc.com' );
            $website         = get_theme_mod( 'contact_website',  'http://dayanarc.com' );
            $website_display = preg_replace( '#^https?://#i', '', $website );
            ?>
            <p class="text-[11px] font-semibold tracking-widest uppercase"><?php echo esc_html( strtoupper( $location ) ); ?></p>
            <a href="mailto:<?php echo antispambot( $email ); ?>" class="footer-link text-[11px] font-semibold tracking-widest lowercase mt-2"><?php echo antispambot( $email ); ?></a>
            <a href="<?php echo esc_url( $website ); ?>" class="footer-link text-[11px] font-semibold tracking-widest lowercase"><?php echo esc_html( $website_display ); ?></a>
        </div>

        <!-- Col 3: Our Offices with flags -->
        <div class="flex flex-col gap-2">
            <span class="text-[10px] uppercase tracking-[0.15em] font-medium footer-muted mb-4">OUR OFFICES</span>
            <?php
            $contact_url = dayanarc_contact_page_url();
            $offices = [
                [ 'text' => get_theme_mod( 'office_germany', 'Berlin, Germany' ),         'flag' => '🇩🇪' ],
                [ 'text' => get_theme_mod( 'office_georgia', 'Tbilisi, Georgia' ),         'flag' => '🇬🇪' ],
                [ 'text' => get_theme_mod( 'office_dubai',   'Business Bay, Dubai, UAE' ), 'flag' => '🇦🇪' ],
            ];
            foreach ( $offices as $office ) :
                if ( ! $office['text'] ) continue;
            ?>
            <a href="<?php echo esc_url( $contact_url ); ?>" class="footer-link flex items-center gap-3 text-[11px] font-semibold tracking-widest uppercase mb-2">
                <span style="font-size:1.2rem; line-height:1;"><?php echo $office['flag']; ?></span>
                <?php echo esc_html( strtoupper( $office['text'] ) ); ?>
            </a>
            <?php endforeach; ?>
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

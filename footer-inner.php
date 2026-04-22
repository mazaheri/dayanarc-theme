<?php
/**
 * Inner page footer — matches the front-page footer design.
 * Called via get_footer( 'inner' ).
 */
?>

<footer class="inner-footer" style="background:#f5f2ee; padding-top:4rem; padding-bottom:1.5rem; width:100%; display:flex; flex-direction:column; position:relative;">

    <div style="width:100%; max-width:1440px; margin:0 auto; padding:0 1.5rem 3rem; display:grid; grid-template-columns:1fr; gap:3rem;">
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:3rem;">

            <!-- Brand -->
            <div style="display:flex; flex-direction:column;">
                <div class="title-text" style="font-size:1.5rem; letter-spacing:0.15em; color:#2c221a; margin-bottom:1.5rem; font-weight:500;">DAYAN ARC</div>
                <p style="font-size:12px; line-height:1.8; color:#68635f; font-weight:300; max-width:220px;">Bringing together creativity, expertise, and passion to deliver exceptional design solutions.</p>
            </div>

            <!-- Menu -->
            <div style="display:grid; grid-template-columns:auto 1fr; gap:1rem 2rem; align-items:start;">
                <span style="font-size:10px; text-transform:uppercase; letter-spacing:0.15em; color:#8c8783; font-weight:500;">MENU</span>
                <nav style="display:flex; flex-direction:column; gap:1rem; font-size:11px; font-weight:600; letter-spacing:0.15em; text-transform:uppercase; color:#2c221a;">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-link">ABOUT US</a>
                    <a href="<?php echo esc_url( dayanarc_portfolio_url() ); ?>" class="footer-link">PORTFOLIO</a>
                    <a href="<?php echo esc_url( home_url( '/#services-section' ) ); ?>" class="footer-link">SERVICES</a>
                    <a href="<?php echo esc_url( dayanarc_journal_url() ); ?>" class="footer-link">JOURNAL</a>
                </nav>
            </div>

            <!-- Social -->
            <div style="display:grid; grid-template-columns:auto 1fr; gap:1rem 2rem; align-items:start;">
                <span style="font-size:10px; text-transform:uppercase; letter-spacing:0.15em; color:#8c8783; font-weight:500;">FOLLOW US</span>
                <nav style="display:flex; flex-direction:column; gap:1rem; font-size:11px; font-weight:600; letter-spacing:0.15em; text-transform:uppercase; color:#2c221a;">
                    <a href="#" class="footer-link">INSTAGRAM</a>
                    <a href="#" class="footer-link">PINTEREST</a>
                    <a href="#" class="footer-link">BEHANCE</a>
                    <a href="#" class="footer-link">LINKEDIN</a>
                </nav>
            </div>

            <!-- Contact -->
            <div style="display:grid; grid-template-columns:auto 1fr; gap:1rem 2rem; align-items:start;">
                <span style="font-size:10px; text-transform:uppercase; letter-spacing:0.15em; color:#8c8783; font-weight:500;">CONTACT</span>
                <div style="display:flex; flex-direction:column; gap:1rem; font-size:11px; font-weight:600; letter-spacing:0.15em; text-transform:uppercase; color:#2c221a; line-height:1.6;">
                    <p>RIYADH, SAUDI ARABIA</p>
                    <a href="mailto:<?php echo antispambot( 'dayanarc.co@gmail.com' ); ?>" class="footer-link" style="text-transform:lowercase;"><?php echo antispambot( 'dayanarc.co@gmail.com' ); ?></a>
                    <a href="<?php echo esc_url( 'https://www.dayanarc.com' ); ?>" class="footer-link" style="text-transform:lowercase;">www.dayanarc.com</a>
                </div>
            </div>

        </div>
    </div>

    <!-- Marquee -->
    <div class="marquee-container" style="margin-bottom:1.5rem;">
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
    <div style="display:flex; flex-direction:column; align-items:center;">
        <div style="width:20%; height:1px; background:#d1ccc8; margin-bottom:1rem;"></div>
        <div style="text-align:center; font-size:10px; letter-spacing:0.15em; text-transform:uppercase; color:#8c8783; font-weight:500;">
            COPYRIGHT <?php echo esc_html( date( 'Y' ) ); ?> &copy; DESIGNED BY <a href="https://valasolution.com/" target="_blank" rel="noopener noreferrer" class="vala-link">VALASOLUTION</a>
        </div>
    </div>

</footer>

<?php wp_footer(); ?>
</body>
</html>

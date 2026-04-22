<?php
/**
 * Template Name: Contact Page
 *
 * Displays the contact form (CF7) and the Our Locations map.
 */
get_header( 'inner' );
$form_id = dayanarc_get_contact_form_id();
?>

<style>
/* Map responsive layout */
.loc-map-outer {
    position: relative;
    width: 100%;
    height: 500px;
    overflow: hidden;
    margin-top: 2rem;
}
.loc-map-inner {
    position: absolute;
    width: 280%;
    left: -105%;
    top: 0;
    height: 100%;
}
.loc-pin-card {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 100;
    background: #f5f2ee;
    padding: 1.5rem 1.5rem 2rem;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.12);
}
.loc-pin-card.open { display: block; }

@media (min-width: 768px) {
    .loc-map-outer {
        height: auto;
        aspect-ratio: 2.2 / 1;
        overflow: visible;
    }
    .loc-map-inner {
        width: 100%;
        left: 0;
        position: absolute;
        top: 0;
    }
    .loc-pin-card {
        position: absolute;
        bottom: auto;
        left: auto;
        right: auto;
        top: -10px;
        left: 38px;
        width: 250px;
        z-index: 30;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
}

/* Contact page grid — stack on mobile, 2 cols on desktop */
.contact-page-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 3rem;
}
@media (min-width: 1024px) {
    .contact-page-grid {
        grid-template-columns: 1fr 1fr;
        gap: 5rem;
        align-items: start;
    }
}

/* CF7 overrides on this page */
.contact-page-form .wpcf7-form {
    display: flex;
    flex-direction: column;
    gap: 2.5rem;
    width: 100%;
}
</style>

<main>

    <!-- ── Breadcrumb ── -->
    <div style="max-width:1440px; margin:0 auto; padding:6rem 1.5rem 0;">
        <?php dayanarc_breadcrumb(); ?>
    </div>

    <!-- ── Contact section ── -->
    <section style="max-width:1440px; margin:0 auto; padding:3rem 1.5rem 5rem;">
        <div class="contact-page-grid">

            <!-- Left: intro -->
            <div>
                <span style="font-size:10px; letter-spacing:0.15em; color:#8c8783; text-transform:uppercase; font-weight:500; display:block; margin-bottom:1.5rem;">CONTACT US</span>
                <h1 class="title-text" style="font-size:clamp(2.5rem,5vw,4rem); line-height:1.1; color:#2c221a; margin-bottom:2rem;">
                    LET'S BEGIN A<br><span class="fancy-c">C</span>ONVERSATION
                </h1>
                <p style="font-size:14px; line-height:1.8; color:#68635f; font-weight:300; max-width:400px; margin-bottom:2.5rem;">
                    Tell us more about your space, your ideas, and your aspirations. We'll guide you through the next steps with care and intention.
                </p>

                <div style="display:flex; flex-direction:column; gap:1.5rem;">
                    <div>
                        <span style="font-size:10px; letter-spacing:0.15em; text-transform:uppercase; color:#8c8783; font-weight:500; display:block; margin-bottom:0.4rem;">Location</span>
                        <p style="font-size:11px; letter-spacing:0.12em; text-transform:uppercase; color:#2c221a; font-weight:600;">Riyadh, Saudi Arabia</p>
                    </div>
                    <div>
                        <span style="font-size:10px; letter-spacing:0.15em; text-transform:uppercase; color:#8c8783; font-weight:500; display:block; margin-bottom:0.4rem;">Email</span>
                        <a href="mailto:<?php echo antispambot( 'dayanarc.co@gmail.com' ); ?>"
                           style="font-size:11px; letter-spacing:0.08em; color:#2c221a; font-weight:600; text-decoration:none;">
                            <?php echo antispambot( 'dayanarc.co@gmail.com' ); ?>
                        </a>
                    </div>
                    <div>
                        <span style="font-size:10px; letter-spacing:0.15em; text-transform:uppercase; color:#8c8783; font-weight:500; display:block; margin-bottom:0.4rem;">Website</span>
                        <a href="<?php echo esc_url( 'https://www.dayanarc.com' ); ?>"
                           style="font-size:11px; letter-spacing:0.08em; color:#2c221a; font-weight:600; text-decoration:none;"
                           target="_blank" rel="noopener noreferrer">
                            www.dayanarc.com
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right: CF7 form -->
            <div class="contact-page-form">
                <?php if ( $form_id ) : ?>
                    <?php echo do_shortcode( '[contact-form-7 id="' . esc_attr( $form_id ) . '"]' ); ?>
                <?php else : ?>
                    <p style="font-size:13px; color:#8c8783; line-height:1.7;">
                        Contact form not configured yet. Please run the
                        <a href="<?php echo esc_url( admin_url( 'themes.php?page=dayanarc-demo-import' ) ); ?>"
                           style="color:#2c221a; text-decoration:underline;">demo importer</a>
                        to activate it, or create a CF7 form named <em>Dayan Arc Contact</em>.
                    </p>
                <?php endif; ?>
            </div>

        </div>
    </section>

    <!-- ── Our Locations ── -->
    <section style="max-width:1440px; margin:0 auto; padding:0 1.5rem 6rem;">

        <h2 class="title-text" style="font-size:clamp(2rem,4vw,2.75rem); letter-spacing:0.04em; color:#2c221a; margin-bottom:0.5rem;">OUR LOCATIONS</h2>
        <p style="font-size:12px; color:#8c8783; letter-spacing:0.1em; text-transform:uppercase; font-weight:500; margin-bottom:0;">Click a pin to view details</p>

        <div class="loc-map-outer">
            <div class="loc-map-inner">

                <!-- Dotted map background -->
                <div style="position:absolute; inset:0; background-image:url('https://www.xbdesign.com/wp-content/themes/twentynineteen-child/assets/images/dots-map.png'); background-size:contain; background-position:center; background-repeat:no-repeat; opacity:0.55;"></div>

                <!-- Germany -->
                <div class="location-pin" style="position:absolute; top:20%; left:51%;">
                    <div style="position:relative;">
                        <button class="pin-toggle" onclick="dayanarc_togglePin(this)" aria-label="Germany"
                                style="background:none; border:none; cursor:pointer; padding:0; position:relative;">
                            <span style="position:absolute; bottom:100%; left:50%; transform:translateX(-50%); font-size:9px; font-weight:700; color:#2c221a; background:#fff; border:1px solid #d1ccc8; padding:2px 8px; white-space:nowrap; margin-bottom:4px; letter-spacing:0.08em;">GERMANY</span>
                            <svg width="26" height="26" fill="#2c221a" viewBox="0 0 20 20" style="display:block; filter:drop-shadow(0 2px 4px rgba(0,0,0,0.25));">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <div class="loc-pin-card">
                            <button onclick="dayanarc_closePin(this)"
                                    style="position:absolute; top:0.75rem; right:0.75rem; background:none; border:none; cursor:pointer; padding:4px; line-height:1;">
                                <svg width="12" height="12" fill="none" stroke="#68635f" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            <p style="font-size:12px; font-weight:600; color:#2c221a; margin-bottom:0.5rem; letter-spacing:0.1em; text-transform:uppercase;">Germany</p>
                            <p style="font-size:12px; color:#68635f; line-height:1.7; margin-bottom:0.75rem; padding-right:1.5rem;">
                                Berlin Central Office, Alexanderplatz 1,<br>10178 Berlin, Germany
                            </p>
                            <a href="#" style="font-size:11px; color:#2c221a; text-decoration:underline; text-underline-offset:3px; letter-spacing:0.08em;">Get Direction</a>
                        </div>
                    </div>
                </div>

                <!-- Georgia -->
                <div class="location-pin" style="position:absolute; top:28%; left:60%;">
                    <div style="position:relative;">
                        <button class="pin-toggle" onclick="dayanarc_togglePin(this)" aria-label="Georgia"
                                style="background:none; border:none; cursor:pointer; padding:0; position:relative;">
                            <span style="position:absolute; bottom:100%; left:50%; transform:translateX(-50%); font-size:9px; font-weight:700; color:#2c221a; background:#fff; border:1px solid #d1ccc8; padding:2px 8px; white-space:nowrap; margin-bottom:4px; letter-spacing:0.08em;">GEORGIA</span>
                            <svg width="26" height="26" fill="#2c221a" viewBox="0 0 20 20" style="display:block; filter:drop-shadow(0 2px 4px rgba(0,0,0,0.25));">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <div class="loc-pin-card">
                            <button onclick="dayanarc_closePin(this)"
                                    style="position:absolute; top:0.75rem; right:0.75rem; background:none; border:none; cursor:pointer; padding:4px; line-height:1;">
                                <svg width="12" height="12" fill="none" stroke="#68635f" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            <p style="font-size:12px; font-weight:600; color:#2c221a; margin-bottom:0.5rem; letter-spacing:0.1em; text-transform:uppercase;">Georgia</p>
                            <p style="font-size:12px; color:#68635f; line-height:1.7; margin-bottom:0.75rem; padding-right:1.5rem;">
                                Tbilisi Office, Rustaveli Ave 1,<br>0108 Tbilisi, Georgia
                            </p>
                            <a href="#" style="font-size:11px; color:#2c221a; text-decoration:underline; text-underline-offset:3px; letter-spacing:0.08em;">Get Direction</a>
                        </div>
                    </div>
                </div>

                <!-- Dubai -->
                <div class="location-pin" style="position:absolute; top:45%; left:66%;">
                    <div style="position:relative;">
                        <button class="pin-toggle" onclick="dayanarc_togglePin(this)" aria-label="Dubai"
                                style="background:none; border:none; cursor:pointer; padding:0; position:relative;">
                            <span style="position:absolute; bottom:100%; left:50%; transform:translateX(-50%); font-size:9px; font-weight:700; color:#2c221a; background:#fff; border:1px solid #d1ccc8; padding:2px 8px; white-space:nowrap; margin-bottom:4px; letter-spacing:0.08em;">DUBAI</span>
                            <svg width="26" height="26" fill="#2c221a" viewBox="0 0 20 20" style="display:block; filter:drop-shadow(0 2px 4px rgba(0,0,0,0.25));">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <div class="loc-pin-card">
                            <button onclick="dayanarc_closePin(this)"
                                    style="position:absolute; top:0.75rem; right:0.75rem; background:none; border:none; cursor:pointer; padding:4px; line-height:1;">
                                <svg width="12" height="12" fill="none" stroke="#68635f" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            <p style="font-size:12px; font-weight:600; color:#2c221a; margin-bottom:0.5rem; letter-spacing:0.1em; text-transform:uppercase;">Dubai</p>
                            <p style="font-size:12px; color:#68635f; line-height:1.7; margin-bottom:0.75rem; padding-right:1.5rem;">
                                Suites 207 &amp; 208, B-Wing, Building 06,<br>Dubai Design District, 333253, UAE
                            </p>
                            <a href="#" style="font-size:11px; color:#2c221a; text-decoration:underline; text-underline-offset:3px; letter-spacing:0.08em;">Get Direction</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </section>

</main>

<script>
function dayanarc_togglePin(btn) {
    var card = btn.nextElementSibling;
    var isOpen = card.classList.contains('open');

    // Close all open cards first
    document.querySelectorAll('.loc-pin-card.open').forEach(function(c) {
        c.classList.remove('open');
    });

    if (!isOpen) {
        card.classList.add('open');
    }
}

function dayanarc_closePin(btn) {
    btn.closest('.loc-pin-card').classList.remove('open');
}

// ESC key closes all
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.loc-pin-card.open').forEach(function(c) {
            c.classList.remove('open');
        });
    }
});
</script>

<?php get_footer( 'inner' ); ?>

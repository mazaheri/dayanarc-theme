<?php
/**
 * Content Manifest — Dayan Arc Theme
 *
 * This file is the single source of truth for all site copy and meta content.
 * Edit the arrays below, then run ONE command to push everything to the database:
 *
 *   studio wp eval-file wp-content/themes/theme/content/manifest.php
 *
 * Safe to run repeatedly — every run overwrites with the values in this file.
 * Does NOT touch images. For images use: import-images.php
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * HOW TO TELL CLAUDE TO UPDATE CONTENT
 * ─────────────────────────────────────────────────────────────────────────────
 * "Update the hero words to DREAM., BUILD., LIVE. and change the about
 *  description to [new text]. Keep everything else the same."
 *
 * Claude will:
 *  1. Edit this file with the new values
 *  2. Run: studio wp eval-file wp-content/themes/theme/content/manifest.php
 *  3. Confirm what changed
 * ─────────────────────────────────────────────────────────────────────────────
 */

// ══ HOMEPAGE — HERO SECTION ══════════════════════════════════════════════════
// The three large words stacked on the hero screen.
// Word 2 first letter gets italic decoration (keep it a single meaningful word).

$customizer = [];

$customizer['hero_word_1']    = 'VISION.';
$customizer['hero_word_2']    = 'DESIGN.';
$customizer['hero_word_3']    = 'REALITY.';
$customizer['hero_cta_label'] = 'Get in touch';
$customizer['hero_tagline']   =
    'At Dayan Arc, we blend creativity and expertise to craft exceptional '
    . 'architectural and interior design experiences. From concept to completion, '
    . 'we bring spaces to life with innovation, precision, and a passion for design excellence.';

// ══ HOMEPAGE — ABOUT SECTION ═════════════════════════════════════════════════
// Heading is split across two lines for the reveal animation.
// Line 1 first letter gets fancy-d decoration.

$customizer['about_heading_line1'] = 'DESIGN WITH';
$customizer['about_heading_line2'] = 'PASSION';
$customizer['about_cta_label']     = 'LEARN MORE';
$customizer['about_body']          =
    'At Dayan Arc, our team brings together the best talent from around the world, '
    . 'combining creativity, expertise, and passion. Together, we strive to deliver '
    . 'exceptional design solutions that exceed expectations and create spaces that inspire and delight.';

// Images are managed separately in import-images.php.
// These keys are listed here for reference — their values come from the media library.
// $customizer['about_image_main']   = '';  // set by import-images.php
// $customizer['about_image_detail'] = '';  // set by import-images.php

// ══ HOMEPAGE — PORTFOLIO SECTION ═════════════════════════════════════════════
// Heading last letter gets fancy-e decoration.
// Also used on the portfolio archive page (/portfolio/).

$customizer['portfolio_heading'] = 'OUR WORKS';

// ══ HOMEPAGE — SERVICES SECTION ══════════════════════════════════════════════
// Two-line heading. Line 2 first letter gets fancy-r decoration.

$customizer['services_heading_line1'] = 'COMPREHENSIVE';
$customizer['services_heading_line2'] = 'SOLUTIONS';
$customizer['services_cta_label']     = 'GET IN TOUCH';
$customizer['services_intro']         =
    'At Dayan Arc, we offer comprehensive architectural and interior design services, '
    . 'from concept development to project management.';
$customizer['services_tagline']       = 'Transforming ideas into inspiring, functional spaces.';

// ══ HOMEPAGE — JOURNAL SECTION ═══════════════════════════════════════════════
// Also used on the journal archive page (/journal/).

$customizer['journal_heading'] = 'DESIGN INSIGHTS';

// ══ HOMEPAGE — CONTACT SECTION ═══════════════════════════════════════════════
// Two-line heading. Line 2 first letter gets fancy-c decoration.

$customizer['fp_contact_heading_line1'] = "LET'S BEGIN A";
$customizer['fp_contact_heading_line2'] = 'CONVERSATION';
$customizer['fp_contact_description']   =
    "Tell us more about your space, your ideas, and your aspirations. "
    . "We'll guide you through the next steps with care and intention.";

// ══ CONTACT PAGE ═════════════════════════════════════════════════════════════

$customizer['contact_page_heading']     = "LET'S BEGIN A CONVERSATION";
$customizer['contact_page_description'] =
    "Tell us more about your space, your ideas, and your aspirations. "
    . "We'll guide you through the next steps with care and intention.";

// ══ GENERAL — BRAND + FOOTER ═════════════════════════════════════════════════

$customizer['footer_tagline'] =
    'Bringing together creativity, expertise, and passion to deliver exceptional design solutions.';

// ══ GENERAL — CONTACT INFO ═══════════════════════════════════════════════════

$customizer['contact_location'] = 'Business Bay, Dubai, UAE';
$customizer['contact_email']    = 'support@dayanarc.com';
$customizer['contact_website']  = 'http://dayanarc.com';

// ══ GENERAL — SOCIAL LINKS ═══════════════════════════════════════════════════

$customizer['social_instagram'] = '#';
$customizer['social_pinterest'] = '#';
$customizer['social_behance']   = '#';
$customizer['social_linkedin']  = '#';

// ══ SERVICE PAGES ════════════════════════════════════════════════════════════
// Per-page meta for each service. option_key resolves to the post ID.
// features: one item per line (newline-separated string).

$services = [

    'architecture' => [
        'option_key'       => 'dayanarc_service_architecture_id',
        'card_description' => 'Complete architectural design from concept to execution, tailored to your unique vision and functional needs.',
        'card_label'       => 'Consultation',
        'what_we_offer'    => 'WHAT WE OFFER',
        'cta_heading'      => 'READY TO START YOUR PROJECT?',
        'cta_description'  => "Let's discuss your vision and bring it to life with the expertise and care that defines Dayan Arc.",
        'cta_label'        => 'CONTACT US',
        'features'         => implode( "\n", [
            'Concept Development',
            'Schematic Design',
            'Design Development',
            'Construction Documentation',
            'Project Administration',
            'Site Supervision',
        ] ),
    ],

    'interior_design' => [
        'option_key'       => 'dayanarc_service_interior_design_id',
        'card_description' => 'Comprehensive interior design services from space planning to material selection and 3D visualization.',
        'card_label'       => 'Full Service',
        'what_we_offer'    => 'WHAT WE OFFER',
        'cta_heading'      => 'READY TO START YOUR PROJECT?',
        'cta_description'  => "Let's discuss your vision and bring it to life with the expertise and care that defines Dayan Arc.",
        'cta_label'        => 'CONTACT US',
        'features'         => implode( "\n", [
            'Space Planning',
            'Concept & Mood Boards',
            'Material & Finish Selection',
            'Furniture & FF&E Procurement',
            'Lighting Design',
            '3D Visualization',
        ] ),
    ],

    '3d_visualization' => [
        'option_key'       => 'dayanarc_service_3d_viz_id',
        'card_description' => 'High-quality 3D renderings and visualization to help you see your vision before construction begins.',
        'card_label'       => 'Rendering',
        'what_we_offer'    => 'WHAT WE OFFER',
        'cta_heading'      => 'READY TO START YOUR PROJECT?',
        'cta_description'  => "Let's discuss your vision and bring it to life with the expertise and care that defines Dayan Arc.",
        'cta_label'        => 'CONTACT US',
        'features'         => implode( "\n", [
            'Photorealistic Still Renders',
            'Architectural Walkthroughs',
            'Virtual Reality Tours',
            'Exterior & Interior Renders',
            'Material Studies',
            'Presentation Boards',
        ] ),
    ],

    'project_management' => [
        'option_key'       => 'dayanarc_service_project_mgmt_id',
        'card_description' => 'End-to-end project management ensuring every detail is executed with precision and attention.',
        'card_label'       => 'Management',
        'what_we_offer'    => 'WHAT WE OFFER',
        'cta_heading'      => 'READY TO START YOUR PROJECT?',
        'cta_description'  => "Let's discuss your vision and bring it to life with the expertise and care that defines Dayan Arc.",
        'cta_label'        => 'CONTACT US',
        'features'         => implode( "\n", [
            'Timeline & Schedule Planning',
            'Budget Management',
            'Contractor Coordination',
            'Quality Control & Inspection',
            'Authority Approvals',
            'Handover & Close-out',
        ] ),
    ],

];

// ══ APPLY ════════════════════════════════════════════════════════════════════
// Everything below this line executes the updates. Do not edit below here.

echo "\n=== Applying content manifest ===\n\n";

// Customizer settings
$updated = 0;
foreach ( $customizer as $key => $value ) {
    set_theme_mod( $key, $value );
    $updated++;
}
echo "✓ Customizer: $updated settings applied\n";

// Service page meta
foreach ( $services as $slug => $data ) {
    $post_id = (int) get_option( $data['option_key'] );
    if ( ! $post_id || ! get_post( $post_id ) ) {
        echo "✗ Service not found: $slug (option: {$data['option_key']})\n";
        continue;
    }
    update_post_meta( $post_id, '_service_card_description', $data['card_description'] );
    update_post_meta( $post_id, '_service_card_tagline',     $data['card_label'] );
    update_post_meta( $post_id, '_service_what_we_offer',    $data['what_we_offer'] );
    update_post_meta( $post_id, '_service_cta_heading',      $data['cta_heading'] );
    update_post_meta( $post_id, '_service_cta_description',  $data['cta_description'] );
    update_post_meta( $post_id, '_service_cta_label',        $data['cta_label'] );
    update_post_meta( $post_id, '_service_features',         $data['features'] );
    echo "✓ Service: $slug (post $post_id)\n";
}

echo "\n=== Done ===\n";

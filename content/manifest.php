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

$customizer['about_heading_line1'] = 'A VISION BEYOND';
$customizer['about_heading_line2'] = 'BORDERS';
$customizer['about_cta_label']     = 'GET IN TOUCH';
$customizer['about_body']          =
    'At Dayan Arc, we believe that architecture is more than just designing '
    . 'structures; it is the art of crafting experiences and building legacies. With '
    . 'over 20 years of expertise and a track record of more than 400 global '
    . 'projects, my team and I have bridged the gap between German engineering '
    . 'precision and creative luxury. From our strategic hubs in Germany, Dubai, '
    . 'and Georgia, we personally ensure that every project — whether a bespoke '
    . 'villa or a complex international airport — meets the highest global standards '
    . 'of excellence.';

// about_video_url and about_video_thumb are set via the Customizer or import-images.php
// $customizer['about_video_url']   = '';  // set in Customizer
// $customizer['about_video_thumb'] = '';  // set by import-images.php

// ══ HOMEPAGE — OUR SERVICE SECTION ═══════════════════════════════════════════

$customizer['our_service_heading']     = 'WHAT WE DO';
$customizer['our_service_description'] =
    'From architectural vision to flawless execution — our integrated services '
    . 'cover every discipline, every scale, and every geography.';
$customizer['our_service_image_1_desc'] =
    'We turn your vision into reality through innovative design and precise planning—creating residential and commercial spaces that balance beauty and functionality.';
$customizer['our_service_image_2_desc'] =
    'We deliver advanced engineering solutions, from complex structures to large-scale facilities, ensuring efficiency, durability, and precision.';

// our_service_image_1 and our_service_image_2 are set by import-images.php

// ══ HOMEPAGE — PORTFOLIO SECTION ═════════════════════════════════════════════

$customizer['portfolio_heading'] = 'OUR PROJECTS';

// ══ HOMEPAGE — SERVICES SECTION ══════════════════════════════════════════════

$customizer['services_heading_line1'] = 'CORE DESIGN';
$customizer['services_heading_line2'] = 'CONCEPTS';
$customizer['services_cta_label']     = 'GET IN TOUCH';
$customizer['services_intro']         =
    'Our integrated design services are applied across a diverse range of sectors, '
    . 'ensuring that every concept — from private luxury to public infrastructure — '
    . 'is executed with unrivaled precision and global standards.';
$customizer['services_tagline']       = 'Transforming ideas into inspiring, functional spaces.';

// ══ HOMEPAGE — JOURNAL SECTION ═══════════════════════════════════════════════

$customizer['journal_heading'] = 'LATEST PROJECTS';

// ══ HOMEPAGE — CONTACT SECTION ═══════════════════════════════════════════════

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

$customizer['social_instagram'] = 'https://www.instagram.com/dayan.arc.co';
$customizer['social_linkedin']  = 'https://www.linkedin.com/company/dayanarc-de/?viewAsMember=true';
$customizer['social_facebook']  = 'https://www.facebook.com/share/1B5ciXyKgT/?mibextid=wwXIfr';
$customizer['social_phone']     = '+971564160061';
$customizer['social_whatsapp']  = '+971564160061';

// ══ SERVICE PAGES ════════════════════════════════════════════════════════════

$services = [

    'architecture' => [
        'option_key'       => 'dayanarc_service_architecture_id',
        'new_title'        => 'Architectural & Interior Design',
        'card_description' => 'From concept to detail, we design modern spaces that reflect lifestyle and precision—covering everything from form to interiors and custom elements.',
        'card_label'       => '',
        'what_we_offer'    => 'WHAT WE OFFER',
        'cta_heading'      => 'READY TO START YOUR PROJECT?',
        'cta_description'  => "Let's discuss your vision and bring it to life with the expertise and care that defines Dayan Arc.",
        'cta_label'        => 'CONTACT US',
        'features'         => implode( "\n", [
            'Concept Development',
            'Schematic Design',
            'Design Development',
            'Interior Styling & Layouts',
            'Custom Furniture Design',
            'Material Selection and FF&E',
        ] ),
    ],

    'industrial_warehouse' => [
        'option_key'       => 'dayanarc_service_interior_design_id',
        'new_title'        => 'Industrial Sheds & Warehouses',
        'card_description' => 'End-to-end design and execution of wide-span industrial structures, optimized for cost, efficiency, and smart space integration.',
        'card_label'       => '',
        'what_we_offer'    => 'WHAT WE OFFER',
        'cta_heading'      => 'READY TO START YOUR PROJECT?',
        'cta_description'  => "Let's discuss your vision and bring it to life with the expertise and care that defines Dayan Arc.",
        'cta_label'        => 'CONTACT US',
        'features'         => implode( "\n", [
            'Wide-Span Structural Engineering',
            'Steel Structure Execution',
            'Mezzanine & Office Integration',
            'Material Optimization',
            'Design & Planning',
            'Turnkey Industrial Solutions',
        ] ),
    ],

    'structural_engineering' => [
        'option_key'       => 'dayanarc_service_3d_viz_id',
        'new_title'        => 'Structural Engineering',
        'card_description' => 'Robust steel and concrete structures designed for safety, efficiency, and full alignment with architectural vision.',
        'card_label'       => '',
        'what_we_offer'    => 'WHAT WE OFFER',
        'cta_heading'      => 'READY TO START YOUR PROJECT?',
        'cta_description'  => "Let's discuss your vision and bring it to life with the expertise and care that defines Dayan Arc.",
        'cta_label'        => 'CONTACT US',
        'features'         => implode( "\n", [
            'Concrete Structure Analysis & Design',
            'Steel Frame Engineering & Design',
            'Seismic & Lateral Load Analysis',
            'Structural Retrofitting',
            'Specialized Foundation Design',
            'Technical Calculation Reports',
        ] ),
    ],

    'mep_smart_systems' => [
        'option_key'       => 'dayanarc_service_project_mgmt_id',
        'new_title'        => 'MEP & Smart Systems',
        'card_description' => 'Integrated mechanical, electrical, and plumbing systems with energy-efficient design and smart building technologies.',
        'card_label'       => '',
        'what_we_offer'    => 'WHAT WE OFFER',
        'cta_heading'      => 'READY TO START YOUR PROJECT?',
        'cta_description'  => "Let's discuss your vision and bring it to life with the expertise and care that defines Dayan Arc.",
        'cta_label'        => 'CONTACT US',
        'features'         => implode( "\n", [
            'HVAC System Design',
            'Electrical & Lighting Infrastructure',
            'Plumbing & Sanitary Engineering',
            'Building Management Systems (BMS)',
            'Fire Protection Systems',
            'Energy Efficiency Analysis',
        ] ),
    ],

    'facade_landscape' => [
        'option_key'       => 'dayanarc_service_5_id',
        'new_title'        => 'Facade & Landscape Architecture',
        'card_description' => 'Harmonized facade and landscape solutions combining aesthetics, materials, lighting, and environmental integration.',
        'card_label'       => '',
        'what_we_offer'    => 'WHAT WE OFFER',
        'cta_heading'      => 'READY TO START YOUR PROJECT?',
        'cta_description'  => "Let's discuss your vision and bring it to life with the expertise and care that defines Dayan Arc.",
        'cta_label'        => 'CONTACT US',
        'features'         => implode( "\n", [
            'Facade Engineering',
            'Exterior Lighting Design',
            'Landscape & Hardscape Design',
            'Water Features & Pool Design',
            'Climate-Appropriate Planting',
            'Facade Structural Detailing',
        ] ),
    ],

    'technical_coordination' => [
        'option_key'       => 'dayanarc_service_6_id',
        'new_title'        => 'Technical Coordination & Project Consultancy',
        'card_description' => 'Seamless coordination across disciplines with expert consultancy to ensure accuracy, efficiency, and flawless project delivery.',
        'card_label'       => '',
        'what_we_offer'    => 'WHAT WE OFFER',
        'cta_heading'      => 'READY TO START YOUR PROJECT?',
        'cta_description'  => "Let's discuss your vision and bring it to life with the expertise and care that defines Dayan Arc.",
        'cta_label'        => 'CONTACT US',
        'features'         => implode( "\n", [
            'Interdisciplinary Coordination',
            'Technical Engineering Consultancy',
            'Code & Standards Compliance',
            'Construction Documentation Management',
            'Design Supervision',
            'Value Engineering',
        ] ),
    ],

];

// ══ APPLY ════════════════════════════════════════════════════════════════════

echo "\n=== Applying content manifest ===\n\n";

// Customizer settings
$updated = 0;
foreach ( $customizer as $key => $value ) {
    set_theme_mod( $key, $value );
    $updated++;
}
echo "✓ Customizer: $updated settings applied\n";

// Service page meta + rename page titles
foreach ( $services as $slug => $data ) {
    $post_id = (int) get_option( $data['option_key'] );
    if ( ! $post_id || ! get_post( $post_id ) ) {
        echo "✗ Service not found: $slug (option: {$data['option_key']})\n";
        continue;
    }

    // Rename the page title
    if ( ! empty( $data['new_title'] ) ) {
        wp_update_post( [ 'ID' => $post_id, 'post_title' => $data['new_title'] ] );
    }

    update_post_meta( $post_id, '_service_card_description', $data['card_description'] );
    update_post_meta( $post_id, '_service_card_tagline',     $data['card_label'] );
    update_post_meta( $post_id, '_service_what_we_offer',    $data['what_we_offer'] );
    update_post_meta( $post_id, '_service_cta_heading',      $data['cta_heading'] );
    update_post_meta( $post_id, '_service_cta_description',  $data['cta_description'] );
    update_post_meta( $post_id, '_service_cta_label',        $data['cta_label'] );
    update_post_meta( $post_id, '_service_features',         $data['features'] );
    echo "✓ Service: {$data['new_title']} (post $post_id)\n";
}

echo "\n=== Done ===\n";

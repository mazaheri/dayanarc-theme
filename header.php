<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

    <!-- Black intro screen -->
    <div id="loader"></div>

    <!-- Scroll to top button -->
    <div id="scrollToTop">
        <svg class="progress-ring">
            <circle class="progress-ring-bg" cx="31" cy="31" r="28"></circle>
            <circle class="progress-ring-circle" cx="31" cy="31" r="28"></circle>
        </svg>
        <svg class="arrow-icon" viewBox="0 0 16 10" fill="none" stroke="#2c221a" stroke-width="1.2">
            <path d="M8 9L8 1M8 1L4 5M8 1L12 5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>

<?php get_header(); ?>

<div id="fullpage">

    <!-- ===== SECTION 1: HERO ===== -->
    <div class="section hero-section">
        <div class="background-image"></div>
        <div class="overlay"></div>
        <div class="spotlight" id="spotlight" style="--x: 50%; --y: 50%;"></div>

        <div class="content-layer px-6 md:px-16">
            <header class="flex justify-between items-center py-6">
                <div class="logo">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="display:inline-block; text-decoration:none;">
                        <img src="<?php echo esc_url( wp_get_attachment_image_url( get_theme_mod( 'header_logo_id', 86 ), 'full' ) ); ?>" alt="<?php bloginfo( 'name' ); ?>" style="height:150px; width:auto;">
                    </a>
                </div>
                <nav class="hidden md:flex space-x-12">
                    <a href="#about"      class="nav-link" onclick="event.preventDefault(); fullpage_api.moveTo(2)">About us</a>
                    <a href="#ourservice" class="nav-link" onclick="event.preventDefault(); fullpage_api.moveTo(3)">What we do</a>
                    <a href="#services"   class="nav-link" onclick="event.preventDefault(); fullpage_api.moveTo(4)">Services</a>
                    <a href="#journal"    class="nav-link" onclick="event.preventDefault(); fullpage_api.moveTo(5)">Projects</a>
                </nav>
                <div class="contact hidden md:block">
                    <a href="#contact" class="nav-link" onclick="event.preventDefault(); fullpage_api.moveTo(6)">Contact</a>
                </div>
                <div class="md:hidden">
                    <button id="menuBtn" class="text-white hover:text-gray-300 focus:outline-none relative z-[110]">
                        <svg id="menuIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </header>



            <main class="flex-grow flex flex-col justify-center pb-12 md:pb-20 relative">
                <?php
                $hw1 = get_theme_mod( 'hero_word_1', 'VISION.' );
                $hw2 = get_theme_mod( 'hero_word_2', 'DESIGN.' );
                $hw3 = get_theme_mod( 'hero_word_3', 'REALITY.' );
                $hw2_first = esc_html( mb_substr( $hw2, 0, 1 ) );
                $hw2_rest  = esc_html( mb_substr( $hw2, 1 ) );
                ?>
                <div class="flex flex-col w-full max-w-[1200px] mx-auto relative z-10">
                    <h1 class="main-heading hero-title font-light text-left md:ml-[5%]">
                        <?php echo esc_html( $hw1 ); ?>
                    </h1>
                    <h1 class="main-heading hero-title font-light text-right md:mr-[10%] mt-8 md:mt-4 flex items-end justify-end">
                        <span class="italic-m mr-1 md:mr-2"><?php echo $hw2_first; ?></span><span style="padding-bottom: 0.05em;"><?php echo $hw2_rest; ?></span>
                    </h1>
                    <h1 class="main-heading hero-title font-light text-right md:mr-[20%] mt-8 md:mt-4">
                        <?php echo esc_html( $hw3 ); ?>
                    </h1>
                </div>

                <div class="mt-12 md:mt-16 flex flex-col md:flex-row md:items-end md:justify-between w-full max-w-[1200px] mx-auto px-4 md:px-0">
                    <div class="md:ml-auto md:mr-[15%]">
                        <p class="sub-text mb-8 md:mb-12 text-sm md:text-base">
                            <?php echo esc_html( get_theme_mod( 'hero_tagline', 'At Dayan Arc, we blend creativity and expertise to craft exceptional architectural and interior design experiences. From concept to completion, we bring spaces to life with innovation, precision, and a passion for design excellence.' ) ); ?>
                        </p>
                        <a href="#contact" class="cta-link group" onclick="event.preventDefault(); fullpage_api.moveTo(6)">
                            <?php echo esc_html( get_theme_mod( 'hero_cta_label', 'Get in touch' ) ); ?>
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- ===== SECTION 2: ABOUT ===== -->
    <div class="section about-section relative w-full max-w-[1440px] mx-auto px-6 md:px-16 lg:px-20 pt-32 pb-20 flex flex-col justify-center" id="about">
        <div class="absolute top-10 md:top-20 left-6 md:left-16 lg:left-20 text-[10px] tracking-[0.15em] text-[#585058] uppercase font-medium">
            <span class="reveal-mask block"><span class="reveal-text delay-100">ABOUT US</span></span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20 items-center mt-12 lg:mt-16">
            <div class="col-span-1 lg:col-span-5 flex flex-col pt-8 lg:pt-0">
                <?php
                $ah1 = get_theme_mod( 'about_heading_line1', 'A VISION BEYOND' );
                $ah2 = get_theme_mod( 'about_heading_line2', 'BORDERS' );
                ?>
                <h1 class="title-text text-4xl md:text-5xl lg:text-[4rem] leading-[1.05] tracking-tight mb-8">
                    <span class="reveal-mask block pb-1"><span class="reveal-text delay-200"><span class="fancy-d"><?php echo esc_html( mb_substr( $ah1, 0, 1 ) ); ?></span><?php echo esc_html( mb_substr( $ah1, 1 ) ); ?></span></span>
                    <span class="reveal-mask block"><span class="reveal-text delay-300"><?php echo esc_html( $ah2 ); ?></span></span>
                </h1>

                <div class="mb-12">
                    <p class="text-[14px] md:text-[15px] leading-relaxed text-[#585058] font-light max-w-[420px] reveal-mask block">
                        <span class="reveal-text delay-400"><?php echo esc_html( get_theme_mod( 'about_body', 'At Dayan Arc, we believe that architecture is more than just designing structures; it is the art of crafting experiences and building legacies. With over 20 years of expertise and a track record of more than 400 global projects, my team and I have bridged the gap between German engineering precision and creative luxury.' ) ); ?></span>
                    </p>
                </div>

                <a href="<?php echo esc_url( dayanarc_contact_page_url() ); ?>" class="link-wrapper mt-4">
                    <span class="link-text"><?php echo esc_html( get_theme_mod( 'about_cta_label', 'GET IN TOUCH' ) ); ?></span>
                    <div class="arrow-graphic">
                        <svg width="16" height="10" viewBox="0 0 16 10" fill="none">
                            <path d="M11 1L15 5M15 5L11 9M15 5H0" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </a>
            </div>

            <?php
            $video_url   = get_theme_mod( 'about_video_url', '' );
            $video_thumb = get_theme_mod( 'about_video_thumb', '' );
            $main_img    = get_theme_mod( 'about_image_main', 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1200&q=80' );
            $detail_img  = get_theme_mod( 'about_image_detail', 'https://images.unsplash.com/photo-1631679706909-1844bbd07221?q=80&w=800&auto=format&fit=crop' );
            $thumb_src   = $video_thumb ?: $main_img;
            ?>
            <div class="col-span-1 lg:col-span-7 about-images-wrapper items-start justify-end h-[300px] md:h-[600px] lg:h-[700px] relative lg:-mt-16">

                <!-- Big image / video thumbnail — play button always visible -->
                <div class="about-img-big curtain-container">
                    <a href="<?php echo $video_url ? esc_url( $video_url ) : esc_url( $thumb_src ); ?>"
                       class="glightbox about-video-trigger"
                       data-gallery="about-media"
                       data-type="<?php echo $video_url ? 'video' : 'image'; ?>"
                       style="display:block;width:100%;height:100%;position:relative;">
                        <img src="<?php echo esc_url( $thumb_src ); ?>" alt="<?php echo esc_attr( get_theme_mod( 'about_heading_line1', 'About us' ) ); ?>" class="curtain-img" style="transition-delay: 400ms, 400ms;">
                        <div class="play-button-overlay">
                            <div class="play-btn-pulse"></div>
                            <div class="play-btn-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="#231f20">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Small detail image — opens in lightbox -->
                <a href="<?php echo esc_url( $detail_img ); ?>" class="about-img-small curtain-container glightbox hidden lg:block" data-gallery="about-media" data-type="image" style="margin-top:6rem;">
                    <img src="<?php echo esc_url( $detail_img ); ?>" alt="Interior detail" class="curtain-img" style="transition-delay: 500ms, 500ms;">
                </a>

            </div>
        </div>
    </div>

    <!-- ===== SECTION 3: OUR SERVICE ===== -->
    <div class="section our-service-section relative w-full max-w-[1440px] mx-auto px-6 md:px-16 lg:px-20 pt-24 pb-16 flex flex-col justify-center" id="ourservice">

        <div class="absolute top-10 md:top-20 left-6 md:left-16 lg:left-20 text-[10px] tracking-[0.15em] text-[#585058] uppercase font-medium">
            <span class="reveal-mask block"><span class="reveal-text delay-100">WHAT WE DO</span></span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20 items-center mt-12 lg:mt-16">

            <!-- Left: text -->
            <div class="col-span-1 lg:col-span-5 flex flex-col pt-8 lg:pt-0">
                <?php $osh = get_theme_mod( 'our_service_heading', 'OUR SERVICE' ); ?>
                <h1 class="title-text text-4xl md:text-5xl lg:text-[4rem] leading-[1.05] tracking-tight mb-8 text-[#231f20]">
                    <span class="reveal-mask block pb-1"><span class="reveal-text delay-200"><?php echo esc_html( $osh ); ?></span></span>
                </h1>
                <p class="text-[14px] md:text-[15px] leading-relaxed text-[#585058] font-light max-w-[400px] reveal-mask block">
                    <span class="reveal-text delay-300"><?php echo esc_html( get_theme_mod( 'our_service_description', 'From architectural vision to flawless execution — our integrated services cover every discipline, every scale, and every geography.' ) ); ?></span>
                </p>
            </div>

            <!-- Right: two equal 9:16 images -->
            <div class="col-span-1 lg:col-span-7 flex gap-6 lg:gap-10 items-start justify-end">
                <?php
                $fallback_svc = 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=600&q=80';
                $svc_imgs = [
                    [ 'img' => get_theme_mod( 'our_service_image_1', $fallback_svc ), 'desc' => get_theme_mod( 'our_service_image_1_desc', 'Concept development and schematic design services tailored to your architectural vision.' ) ],
                    [ 'img' => get_theme_mod( 'our_service_image_2', $fallback_svc ), 'desc' => get_theme_mod( 'our_service_image_2_desc', 'Comprehensive construction documentation and technical drawings executed with precision.' ) ],
                ];
                foreach ( $svc_imgs as $si ) : ?>
                <div class="flex flex-col items-center flex-1">
                    <a href="<?php echo esc_url( $si['img'] ); ?>" class="our-service-img curtain-container w-full glightbox" data-type="image" data-gallery="our-service-gallery">
                        <img src="<?php echo esc_url( $si['img'] ); ?>" alt="" class="curtain-img" style="transition-delay:300ms,300ms;">
                    </a>
                    <p class="mt-4 text-[12px] leading-relaxed text-[#585058] font-light text-center max-w-[240px]"><?php echo esc_html( $si['desc'] ); ?></p>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>

    <!-- ===== SECTION 4: SERVICES ===== -->
    <div class="section services-section relative w-full px-8 md:px-12 lg:px-16 py-16 lg:py-24 flex items-center justify-center" id="services-section">
        <div class="bg-overlay bg-fade delay-100"></div>
        <div class="bg-gradient bg-fade delay-100"></div>
        <div class="shine-effect" id="services-shine" style="--x: 50%; --y: 50%;"></div>

        <div class="services-content grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6 lg:gap-8 w-full max-w-[1600px] mx-auto">
            <div class="lg:col-span-3 flex flex-col pt-4">
                <span class="reveal-mask block mb-6">
                    <span class="reveal-text text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium">SERVICES</span>
                </span>
                <?php
                $sh1 = get_theme_mod( 'services_heading_line1', 'CORE DESIGN' );
                $sh2 = get_theme_mod( 'services_heading_line2', 'CONCEPTS' );
                ?>
                <h1 class="title-text text-3xl lg:text-[2.8rem] leading-[1.05] tracking-tight mb-6 text-white">
                    <span class="reveal-mask block pb-1"><span class="reveal-text delay-100"><?php echo esc_html( $sh1 ); ?></span></span>
                    <span class="reveal-mask block"><span class="reveal-text delay-200"><span class="fancy-r"><?php echo esc_html( mb_substr( $sh2, 0, 1 ) ); ?></span><?php echo esc_html( mb_substr( $sh2, 1 ) ); ?></span></span>
                </h1>
                <p class="text-[13px] leading-relaxed text-gray-300 font-light reveal-mask block mb-8">
                    <span class="reveal-text delay-300"><?php echo esc_html( get_theme_mod( 'services_intro', 'Our integrated design services are applied across a diverse range of sectors, ensuring that every concept is executed with unrivaled precision and global standards.' ) ); ?></span>
                </p>
                <div class="reveal-mask block">
                    <div class="reveal-text delay-400">
                        <a href="#contact" class="link-wrapper-white mt-2" onclick="event.preventDefault(); fullpage_api.moveTo(6)">
                            <span class="text-[11px] uppercase tracking-widest font-semibold"><?php echo esc_html( get_theme_mod( 'services_cta_label', 'GET IN TOUCH' ) ); ?></span>
                            <svg width="16" height="10" viewBox="0 0 16 10" fill="none" stroke="currentColor" stroke-width="1.2">
                                <path d="M11 1L15 5M15 5L11 9M15 5H0" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <?php
            $svc_ids = array_filter( [
                (int) get_option( 'dayanarc_service_architecture_id' ),
                (int) get_option( 'dayanarc_service_interior_design_id' ),
                (int) get_option( 'dayanarc_service_3d_viz_id' ),
                (int) get_option( 'dayanarc_service_project_mgmt_id' ),
                (int) get_option( 'dayanarc_service_5_id' ),
                (int) get_option( 'dayanarc_service_6_id' ),
            ] );
            $card_delays  = [ 'delay-200', 'delay-300', 'delay-400', 'delay-200', 'delay-300', 'delay-400' ];
            $img_delays   = [ 'delay-300', 'delay-400', 'delay-500', 'delay-300', 'delay-400', 'delay-500' ];
            $fallback_img = 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=600&q=80';

            if ( $svc_ids ) :
                $svc_q = new WP_Query( [
                    'post_type'      => 'page',
                    'post__in'       => array_values( $svc_ids ),
                    'orderby'        => 'post__in',
                    'posts_per_page' => count( $svc_ids ),
                    'no_found_rows'  => true,
                ] );
                $svc_i = 0;
                while ( $svc_q->have_posts() ) :
                    $svc_q->the_post();
                    $sid       = get_the_ID();
                    $svc_num   = get_post_meta( $sid, '_service_number', true ) ?: str_pad( $svc_i + 1, 2, '0', STR_PAD_LEFT );
                    $svc_desc  = get_post_meta( $sid, '_service_card_description', true ) ?: get_the_excerpt();
                    $svc_label = get_post_meta( $sid, '_service_card_tagline', true );
                    $svc_thumb = get_the_post_thumbnail_url( $sid, 'large' ) ?: $fallback_img;
                    $card_d    = $card_delays[ $svc_i ] ?? 'delay-200';
                    $img_d     = $img_delays[ $svc_i ] ?? 'delay-300';
                    ?>
            <a href="<?php echo esc_url( get_permalink() ); ?>" class="lg:col-span-3 card-wrapper <?php echo esc_attr( $card_d ); ?><?php echo $svc_i >= 3 ? ' card-row2' : ''; ?>" style="text-decoration:none; color:inherit; display:block;">
                <div class="service-card group bg-white text-[#231f20] p-5 lg:p-6 relative cursor-pointer shadow-2xl">
                    <div class="flex justify-between items-start w-full">
                        <span class="text-[11px] text-[#585058] tracking-widest"><?php echo esc_html( $svc_num ); ?></span>
                        <div class="img-container curtain-container">
                            <img src="<?php echo esc_url( $svc_thumb ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="curtain-img <?php echo esc_attr( $img_d ); ?> object-cover w-full h-full">
                        </div>
                    </div>
                    <div class="mt-auto w-full pt-6 bg-white relative z-10">
                        <h3 class="title-text text-2xl lg:text-3xl text-[#231f20] tracking-tight"><?php echo esc_html( strtoupper( get_the_title() ) ); ?></h3>
                        <div class="card-content-grid">
                            <div class="card-inner-content">
                                <div class="pt-4 flex flex-col gap-5">
                                    <p class="text-[13px] leading-relaxed text-[#585058] font-light"><?php echo esc_html( $svc_desc ); ?></p>
                                    <div class="flex justify-between items-center text-[12px] text-[#585058]">
                                        <?php if ( $svc_label ) : ?><span><?php echo esc_html( $svc_label ); ?></span><?php endif; ?>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#231f20" stroke-width="1.5" class="arrow-hover">
                                            <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
                    <?php
                    $svc_i++;
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
            <div class="lg:col-span-3 hidden lg:block"></div>

        </div>
    </div>

    <!-- ===== SECTION 5: PORTFOLIO ===== -->
    <div class="section blog-section">
        <div class="relative w-full max-w-[1600px] mx-auto px-6 md:px-12 lg:px-16 py-16 lg:py-20 flex flex-col justify-center">
            <div class="text-center mb-12 lg:mb-16">
                <span class="reveal-mask block mb-4">
                    <span class="reveal-text text-[10px] tracking-[0.2em] text-[#585058] uppercase font-medium">PROJECTS</span>
                </span>
                <?php $ph5 = get_theme_mod( 'portfolio_heading', 'OUR WORKS' ); ?>
                <h1 class="title-text text-3xl md:text-4xl lg:text-5xl leading-tight text-[#231f20]">
                    <span class="reveal-mask"><span class="reveal-text delay-100 uppercase tracking-tight"><?php echo esc_html( $ph5 ); ?></span></span>
                </h1>
                <div class="reveal-mask mt-5">
                    <a href="<?php echo esc_url( dayanarc_portfolio_url() ); ?>" class="reveal-text delay-200 inline-flex items-center gap-2 text-[11px] tracking-widest uppercase font-medium text-[#585058] hover:text-[#231f20] transition-colors">
                        View all projects
                        <svg width="14" height="9" viewBox="0 0 16 10" fill="none">
                            <path d="M11 1L15 5M15 5L11 9M15 5H0" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="relative pt-4">
                <div id="portfolio-grid" class="grid grid-cols-1 md:grid-cols-5 gap-6 lg:gap-8 items-stretch transition-opacity duration-500 opacity-100"></div>

                <div class="relative md:absolute md:bottom-12 md:right-0 flex flex-col items-end gap-3 z-20 mt-6 md:mt-0">
                    <div class="flex gap-3">
                        <button onclick="changePage(-1)" class="w-10 h-10 border border-[#e5e5e5] rounded-full flex items-center justify-center hover:border-[#231f20] transition-all bg-white shadow-sm">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M19 12H5M5 12L12 19M5 12L12 5"/>
                            </svg>
                        </button>
                        <button onclick="changePage(1)" class="w-10 h-10 border border-[#e5e5e5] rounded-full flex items-center justify-center hover:border-[#231f20] transition-all bg-white shadow-sm">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M5 12H19M19 12L12 5M19 12L12 19"/>
                            </svg>
                        </button>
                    </div>
                    <div id="pagination-numbers" class="flex items-center gap-4 text-[11px] tracking-widest font-medium pr-1"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SECTION 6: CONTACT ===== -->
    <div class="section" id="contact">
        <div class="relative w-full max-w-[1440px] mx-auto px-6 md:px-12 lg:px-20 h-full flex flex-col justify-center py-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-24 items-center">
                <div class="flex flex-col">
                    <span class="reveal-mask block mb-6">
                        <span class="reveal-text delay-100 text-[10px] tracking-[0.15em] text-[#585058] uppercase font-medium">CONTACT US</span>
                    </span>
                    <?php
                    $fch1 = get_theme_mod( 'fp_contact_heading_line1', "LET'S BEGIN A" );
                    $fch2 = get_theme_mod( 'fp_contact_heading_line2', 'CONVERSATION' );
                    ?>
                    <h1 class="title-text text-4xl md:text-5xl lg:text-[4rem] leading-[1.1] tracking-tight mb-6 text-[#231f20] break-words">
                        <span class="reveal-mask block pb-1"><span class="reveal-text delay-200"><?php echo esc_html( $fch1 ); ?></span></span>
                        <span class="reveal-mask block w-full"><span class="reveal-text delay-300"><span class="fancy-c"><?php echo esc_html( mb_substr( $fch2, 0, 1 ) ); ?></span><?php echo esc_html( mb_substr( $fch2, 1 ) ); ?></span></span>
                    </h1>
                    <p class="text-[14px] leading-relaxed text-[#585058] font-light max-w-[420px] reveal-mask block mb-8">
                        <span class="reveal-text delay-400"><?php echo esc_html( get_theme_mod( 'fp_contact_description', "Tell us more about your space, your ideas, and your aspirations. We'll guide you through the next steps with care and intention." ) ); ?></span>
                    </p>
                    <span class="reveal-mask block">
                    <a href="<?php echo esc_url( dayanarc_contact_page_url() ); ?>" class="link-wrapper reveal-text delay-600" style="width:auto; display:inline-flex;">
                        <span class="link-text" style="font-size:11px;">VIEW OUR LOCATIONS</span>
                        <div class="arrow-graphic">
                            <svg width="14" height="9" viewBox="0 0 16 10" fill="none">
                                <path d="M11 1L15 5M15 5L11 9M15 5H0" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </a>
                    </span>
                </div>

                <div class="flex flex-col w-full reveal-mask">
                    <div class="reveal-text delay-500 w-full fp-contact-form">
                        <?php
                        $cf7_id = dayanarc_get_contact_form_id();
                        if ( $cf7_id ) :
                            echo do_shortcode( '[contact-form-7 id="' . esc_attr( $cf7_id ) . '" html_class="flex flex-col gap-8 w-full"]' );
                        else : ?>
                            <p style="font-size:13px; color:#585058; line-height:1.8; padding:1rem 0;">
                                Contact form not set up yet. Run the
                                <a href="<?php echo esc_url( admin_url( 'themes.php?page=dayanarc-demo-import' ) ); ?>"
                                   style="color:#231f20; text-decoration:underline;">demo importer</a> to activate it.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- end section 6 -->

    <!-- ===== SECTION 7: JOURNAL + FOOTER ===== -->
    <div class="section fp-auto-height" id="journal-section">

        <!-- Journal/Blog carousel -->
        <div class="w-full max-w-[1440px] mx-auto px-6 md:px-12 lg:px-20 pt-10 pb-14">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 md:gap-0 mb-8">
                <div>
                    <span class="text-[10px] tracking-[0.2em] text-[#585058] uppercase font-medium block mb-2">PROJECTS</span>
                    <?php $jh7 = get_theme_mod( 'journal_heading', 'DESIGN INSIGHTS' ); ?>
                    <h2 class="title-text text-3xl md:text-4xl text-[#231f20] leading-tight uppercase tracking-tight">
                        <?php echo esc_html( $jh7 ); ?>
                    </h2>
                </div>
                <a href="<?php echo esc_url( dayanarc_journal_url() ); ?>" class="link-wrapper" style="opacity:1; transform:none; width:auto; display:inline-flex; gap:0.75rem; flex-shrink:0;">
                    <span class="link-text" style="font-size:11px;">SEE ALL</span>
                    <div class="arrow-graphic">
                        <svg width="14" height="9" viewBox="0 0 16 10" fill="none">
                            <path d="M11 1L15 5M15 5L11 9M15 5H0" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </a>
            </div>

            <div id="journal-container"></div>
        </div>

        <!-- Footer -->
        <footer class="fp-footer pt-16 pb-6 w-full flex flex-col relative">
            <div class="w-full max-w-[1440px] mx-auto px-6 md:px-12 lg:px-20 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 lg:gap-10 mb-12">

                <!-- Col 1: Logo + tagline + social icons -->
                <div class="flex flex-col">
                    <div class="mb-6">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="display:inline-block; text-decoration:none;">
                            <img src="<?php echo esc_url( wp_get_attachment_image_url( get_theme_mod( 'footer_logo_id', 87 ), 'full' ) ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="footer-logo-img" style="height:150px; width:auto;">
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
                        [ 'text' => get_theme_mod( 'office_germany', 'Berlin, Germany' ),           'flag' => '🇩🇪' ],
                        [ 'text' => get_theme_mod( 'office_georgia', 'Tbilisi, Georgia' ),           'flag' => '🇬🇪' ],
                        [ 'text' => get_theme_mod( 'office_dubai',   'Business Bay, Dubai, UAE' ),   'flag' => '🇦🇪' ],
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

            <div class="flex flex-col items-center">
                <div class="w-1/5 h-[1px] mb-4" style="background:rgba(246,240,218,0.2);"></div>
                <div class="text-center text-[10px] tracking-widest uppercase font-medium footer-muted">
                    COPYRIGHT <?php echo esc_html( date( 'Y' ) ); ?> &copy; DESIGNED BY <a href="https://valasolution.com/" target="_blank" rel="noopener noreferrer" class="footer-link">VALASOLUTION</a>
                </div>
            </div>
        </footer>

    </div><!-- end section 7 -->

</div><!-- end #fullpage -->

<div id="mobileMenu" class="mobile-menu">
    <a href="#about"      class="nav-link text-xl" onclick="fullpage_api.moveTo(2)">About us</a>
    <a href="#ourservice" class="nav-link text-xl" onclick="fullpage_api.moveTo(3)">What we do</a>
    <a href="#services"   class="nav-link text-xl" onclick="fullpage_api.moveTo(4)">Services</a>
    <a href="#journal"    class="nav-link text-xl" onclick="fullpage_api.moveTo(5)">Projects</a>
    <a href="#contact"    class="nav-link text-xl" onclick="fullpage_api.moveTo(6)">Contact</a>
</div>
<div id="menuOverlay" class="menu-overlay"></div>

<?php get_footer(); ?>

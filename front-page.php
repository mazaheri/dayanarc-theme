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
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hero-title text-xl md:text-2xl tracking-widest uppercase text-white no-underline" style="text-decoration:none; color:inherit;">DAYAN ARC</a>
                </div>
                <nav class="hidden md:flex space-x-12">
                    <a href="#about"     class="nav-link" onclick="event.preventDefault(); fullpage_api.moveTo(2)">About Us</a>
                    <a href="#portfolio" class="nav-link" onclick="event.preventDefault(); fullpage_api.moveTo(3)">Portfolio</a>
                    <a href="#services"  class="nav-link" onclick="event.preventDefault(); fullpage_api.moveTo(4)">Services</a>
                    <a href="#journal"   class="nav-link" onclick="event.preventDefault(); fullpage_api.moveTo(5)">Journal</a>
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

            <div id="mobileMenu" class="mobile-menu">
                <a href="#about"     class="nav-link text-xl" onclick="fullpage_api.moveTo(2)">About Us</a>
                <a href="#portfolio" class="nav-link text-xl" onclick="fullpage_api.moveTo(3)">Portfolio</a>
                <a href="#services"  class="nav-link text-xl" onclick="fullpage_api.moveTo(4)">Services</a>
                <a href="#journal"   class="nav-link text-xl" onclick="fullpage_api.moveTo(5)">Journal</a>
                <a href="#contact"   class="nav-link text-xl" onclick="fullpage_api.moveTo(6)">Contact</a>
            </div>
            <div id="menuOverlay" class="menu-overlay"></div>

            <main class="flex-grow flex flex-col justify-center pb-12 md:pb-20 relative">
                <div class="flex flex-col w-full max-w-[1200px] mx-auto relative z-10">
                    <h1 class="main-heading hero-title font-light text-left md:ml-[5%]">
                        VISION.
                    </h1>
                    <h1 class="main-heading hero-title font-light text-right md:mr-[10%] mt-8 md:mt-4 flex items-end justify-end">
                        <span class="italic-m mr-1 md:mr-2">D</span><span style="padding-bottom: 0.05em;">ESIGN.</span>
                    </h1>
                    <h1 class="main-heading hero-title font-light text-right md:mr-[20%] mt-8 md:mt-4">
                        REALITY.
                    </h1>
                </div>

                <div class="mt-12 md:mt-16 flex flex-col md:flex-row md:items-end md:justify-between w-full max-w-[1200px] mx-auto px-4 md:px-0">
                    <div class="md:ml-auto md:mr-[15%]">
                        <p class="sub-text mb-8 md:mb-12 text-sm md:text-base">
                            At Dayan Arc, we blend creativity and expertise to craft exceptional architectural and interior design experiences. From concept to completion, we bring spaces to life with innovation, precision, and a passion for design excellence.
                        </p>
                        <a href="#contact" class="cta-link group" onclick="event.preventDefault(); fullpage_api.moveTo(6)">
                            Get in touch
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
    <div class="section about-section relative w-full max-w-[1440px] mx-auto px-6 md:px-16 lg:px-20 pt-32 pb-20 flex flex-col justify-center">
        <div class="absolute top-10 md:top-20 left-6 md:left-16 lg:left-20 text-[10px] tracking-[0.15em] text-[#a9a39f] uppercase font-medium">
            <span class="reveal-mask block"><span class="reveal-text delay-100">ABOUT US</span></span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20 items-center mt-12 lg:mt-16">
            <div class="col-span-1 lg:col-span-5 flex flex-col pt-8 lg:pt-0">
                <h1 class="title-text text-4xl md:text-5xl lg:text-[4rem] leading-[1.05] tracking-tight mb-8">
                    <span class="reveal-mask block pb-1"><span class="reveal-text delay-200"><span class="fancy-d">D</span>ESIGN WITH</span></span>
                    <span class="reveal-mask block"><span class="reveal-text delay-300">PASSION</span></span>
                </h1>

                <div class="mb-12">
                    <p class="text-[14px] md:text-[15px] leading-relaxed text-[#68635f] font-light max-w-[420px] reveal-mask block">
                        <span class="reveal-text delay-400">At Dayan Arc, our team brings together the best talent from around the world, combining creativity, expertise, and passion. Together, we strive to deliver exceptional design solutions that exceed expectations and create spaces that inspire and delight.</span>
                    </p>
                </div>

                <a href="#contact" class="link-wrapper mt-4" onclick="event.preventDefault(); fullpage_api.moveTo(6)">
                    <span class="link-text">LEARN MORE</span>
                    <div class="arrow-graphic">
                        <svg width="16" height="10" viewBox="0 0 16 10" fill="none">
                            <path d="M11 1L15 5M15 5L11 9M15 5H0" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </a>
            </div>

            <div class="col-span-1 lg:col-span-7 flex items-start gap-4 lg:gap-8 justify-end h-[500px] md:h-[600px] lg:h-[700px] relative lg:-mt-16">
                <div class="w-full lg:w-[62%] h-[95%] curtain-container">
                    <img src="https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1200&q=80" alt="Main interior design view" class="curtain-img" style="transition-delay: 400ms, 400ms;">
                </div>
                <div class="hidden lg:block w-[35%] h-[55%] curtain-container mt-24">
                    <img src="https://images.unsplash.com/photo-1631679706909-1844bbd07221?q=80&w=800&auto=format&fit=crop" alt="Interior detail" class="curtain-img" style="transition-delay: 500ms, 500ms;">
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SECTION 3: PORTFOLIO ===== -->
    <div class="section portfolio-section px-4 md:px-6 py-12 flex flex-col items-center justify-center">
        <div class="text-center mb-12">
            <span class="reveal-mask"><span class="reveal-text text-[10px] tracking-[0.2em] text-[#a9a39f] uppercase font-medium mb-4 block">PORTFOLIO</span></span>
            <h1 class="title-text text-3xl md:text-4xl lg:text-5xl leading-tight text-[#2c221a]">
                <span class="reveal-mask"><span class="reveal-text delay-100">OUR WORK<span class="fancy-e">S</span></span></span>
            </h1>
        </div>
        <div id="portfolio-container" class="portfolio-slide w-full max-w-[1400px]"></div>
    </div>

    <!-- ===== SECTION 4: SERVICES ===== -->
    <div class="section services-section relative w-full px-8 md:px-12 lg:px-16 py-16 lg:py-24 flex items-center justify-center" id="services-section">
        <div class="bg-overlay bg-fade delay-100"></div>
        <div class="bg-gradient bg-fade delay-100"></div>
        <div class="shine-effect" id="services-shine" style="--x: 50%; --y: 50%;"></div>

        <div class="services-content grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6 lg:gap-8 w-full max-w-[1600px] mx-auto">
            <div class="lg:col-span-6 flex flex-col pt-4">
                <span class="reveal-mask block mb-6">
                    <span class="reveal-text text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium">SERVICES</span>
                </span>
                <h1 class="title-text text-4xl lg:text-[4.2rem] leading-[1.05] tracking-tight mb-6 text-white max-w-[90%]">
                    <span class="reveal-mask block pb-1"><span class="reveal-text delay-100">COMPREHENSIVE</span></span>
                    <span class="reveal-mask block"><span class="reveal-text delay-200"><span class="fancy-r">S</span>OLUTIONS</span></span>
                </h1>
                <p class="text-[14px] md:text-[15px] leading-relaxed text-gray-300 font-light max-w-[340px] reveal-mask block mb-8">
                    <span class="reveal-text delay-300">At Dayan Arc, we offer comprehensive architectural and interior design services, from concept development to project management.</span>
                </p>
                <div class="reveal-mask block">
                    <div class="reveal-text delay-400">
                        <a href="#contact" class="link-wrapper-white mt-2" onclick="event.preventDefault(); fullpage_api.moveTo(6)">
                            <span class="text-[11px] uppercase tracking-widest font-semibold">GET IN TOUCH</span>
                            <svg width="16" height="10" viewBox="0 0 16 10" fill="none" stroke="currentColor" stroke-width="1.2">
                                <path d="M11 1L15 5M15 5L11 9M15 5H0" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <a href="<?php echo esc_url( dayanarc_service_url( 'architecture' ) ); ?>" class="lg:col-span-3 card-wrapper delay-200" style="text-decoration:none; color:inherit; display:block;">
                <div class="service-card group bg-white text-[#2c221a] p-5 lg:p-6 relative cursor-pointer shadow-2xl">
                    <div class="flex justify-between items-start w-full">
                        <span class="text-[11px] text-[#68635f] tracking-widest">01</span>
                        <div class="img-container curtain-container">
                            <img src="https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=600&q=80" alt="Architecture" class="curtain-img delay-300 object-cover w-full h-full">
                        </div>
                    </div>
                    <div class="mt-auto w-full pt-6 bg-white relative z-10">
                        <h3 class="title-text text-2xl lg:text-3xl text-[#2c221a] tracking-tight">ARCHITECTURE</h3>
                        <div class="card-content-grid">
                            <div class="card-inner-content">
                                <div class="pt-4 flex flex-col gap-5">
                                    <p class="text-[13px] leading-relaxed text-[#68635f] font-light">Complete architectural design from concept to execution, tailored to your unique vision and functional needs.</p>
                                    <div class="flex justify-between items-center text-[12px] text-[#a9a39f]">
                                        <span>Consultation</span>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2c221a" stroke-width="1.5" class="arrow-hover">
                                            <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <a href="<?php echo esc_url( dayanarc_service_url( 'interior-design' ) ); ?>" class="lg:col-span-3 card-wrapper delay-300" style="text-decoration:none; color:inherit; display:block;">
                <div class="service-card group bg-white text-[#2c221a] p-5 lg:p-6 relative cursor-pointer shadow-2xl">
                    <div class="flex justify-between items-start w-full">
                        <span class="text-[11px] text-[#68635f] tracking-widest">02</span>
                        <div class="img-container curtain-container">
                            <img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=600&q=80" alt="Interior Design" class="curtain-img delay-400 object-cover w-full h-full">
                        </div>
                    </div>
                    <div class="mt-auto w-full pt-6 bg-white relative z-10">
                        <h3 class="title-text text-2xl lg:text-3xl text-[#2c221a] tracking-tight">INTERIOR DESIGN</h3>
                        <div class="card-content-grid">
                            <div class="card-inner-content">
                                <div class="pt-4 flex flex-col gap-5">
                                    <p class="text-[13px] leading-relaxed text-[#68635f] font-light">Comprehensive interior design services from space planning to material selection and 3D visualization.</p>
                                    <div class="flex justify-between items-center text-[12px] text-[#a9a39f]">
                                        <span>Full Service</span>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2c221a" stroke-width="1.5" class="arrow-hover">
                                            <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <div class="lg:col-span-3 hidden lg:block"></div>

            <a href="<?php echo esc_url( dayanarc_service_url( '3d-visualization' ) ); ?>" class="lg:col-span-3 card-wrapper delay-400" style="text-decoration:none; color:inherit; display:block;">
                <div class="service-card group bg-white text-[#2c221a] p-5 lg:p-6 relative cursor-pointer shadow-2xl">
                    <div class="flex justify-between items-start w-full">
                        <span class="text-[11px] text-[#68635f] tracking-widest">03</span>
                        <div class="img-container curtain-container">
                            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/project10.png" alt="3D Visualization" class="curtain-img delay-500 object-cover w-full h-full">
                        </div>
                    </div>
                    <div class="mt-auto w-full pt-6 bg-white relative z-10">
                        <h3 class="title-text text-2xl lg:text-3xl text-[#2c221a] tracking-tight">3D VISUALIZATION</h3>
                        <div class="card-content-grid">
                            <div class="card-inner-content">
                                <div class="pt-4 flex flex-col gap-5">
                                    <p class="text-[13px] leading-relaxed text-[#68635f] font-light">High-quality 3D renderings and visualization to help you see your vision before construction begins.</p>
                                    <div class="flex justify-between items-center text-[12px] text-[#a9a39f]">
                                        <span>Rendering</span>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2c221a" stroke-width="1.5" class="arrow-hover">
                                            <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <a href="<?php echo esc_url( dayanarc_service_url( 'project-management' ) ); ?>" class="lg:col-span-3 card-wrapper delay-500" style="text-decoration:none; color:inherit; display:block;">
                <div class="service-card group bg-white text-[#2c221a] p-5 lg:p-6 relative cursor-pointer shadow-2xl">
                    <div class="flex justify-between items-start w-full">
                        <span class="text-[11px] text-[#68635f] tracking-widest">04</span>
                        <div class="img-container curtain-container">
                            <img src="https://images.unsplash.com/photo-1631679706909-1844bbd07221?auto=format&fit=crop&w=600&q=80" alt="Project Management" class="curtain-img delay-600 object-cover w-full h-full">
                        </div>
                    </div>
                    <div class="mt-auto w-full pt-6 bg-white relative z-10">
                        <h3 class="title-text text-2xl lg:text-3xl text-[#2c221a] tracking-tight">PROJECT MANAGEMENT</h3>
                        <div class="card-content-grid">
                            <div class="card-inner-content">
                                <div class="pt-4 flex flex-col gap-5">
                                    <p class="text-[13px] leading-relaxed text-[#68635f] font-light">End-to-end project management ensuring every detail is executed with precision and attention.</p>
                                    <div class="flex justify-between items-center text-[12px] text-[#a9a39f]">
                                        <span>Management</span>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2c221a" stroke-width="1.5" class="arrow-hover">
                                            <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <div class="lg:col-span-3 flex items-end justify-start lg:justify-end pb-8">
                <p class="text-white text-[14px] font-light max-w-[200px] leading-relaxed text-justify lg:text-right reveal-mask">
                    <span class="reveal-text delay-600">Transforming ideas into inspiring, functional spaces.</span>
                </p>
            </div>
        </div>
    </div>

    <!-- ===== SECTION 5: JOURNAL ===== -->
    <div class="section blog-section">
        <div class="relative w-full max-w-[1600px] mx-auto px-6 md:px-12 lg:px-16 py-16 lg:py-20 flex flex-col justify-center">
            <div class="text-center mb-12 lg:mb-16">
                <span class="reveal-mask block mb-4">
                    <span class="reveal-text text-[10px] tracking-[0.2em] text-[#a9a39f] uppercase font-medium">JOURNAL</span>
                </span>
                <h1 class="title-text text-3xl md:text-4xl lg:text-5xl leading-tight text-[#2c221a]">
                    <span class="reveal-mask"><span class="reveal-text delay-100 uppercase tracking-tight">DESIGN IN<span class="fancy-s">S</span>IGHTS</span></span>
                </h1>
            </div>

            <div class="relative min-h-[600px] pt-4">
                <div id="journal-grid" class="grid grid-cols-1 md:grid-cols-5 gap-6 lg:gap-8 items-stretch transition-opacity duration-500 opacity-100"></div>

                <div class="absolute bottom-8 lg:bottom-12 right-0 flex flex-col items-end gap-3 z-20">
                    <div class="flex gap-3">
                        <button onclick="changePage(-1)" class="w-10 h-10 border border-[#e5e5e5] rounded-full flex items-center justify-center hover:border-[#2c221a] transition-all bg-white shadow-sm">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M19 12H5M5 12L12 19M5 12L12 5"/>
                            </svg>
                        </button>
                        <button onclick="changePage(1)" class="w-10 h-10 border border-[#e5e5e5] rounded-full flex items-center justify-center hover:border-[#2c221a] transition-all bg-white shadow-sm">
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

    <!-- ===== SECTION 6: CONTACT + FOOTER ===== -->
    <div class="section fp-auto-height">

        <!-- Contact -->
        <section class="contact-section relative w-full max-w-[1440px] mx-auto px-6 md:px-12 lg:px-20 pt-24 pb-12 lg:pt-32 lg:pb-16 flex flex-col justify-center min-h-[70vh]">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-start">
                <div class="flex flex-col">
                    <span class="reveal-mask block mb-6">
                        <span class="reveal-text delay-100 text-[10px] tracking-[0.15em] text-[#8c8783] uppercase font-medium">CONTACT US</span>
                    </span>
                    <h1 class="title-text text-4xl md:text-5xl lg:text-[4rem] leading-[1.1] tracking-tight mb-8 text-[#2c221a] break-words">
                        <span class="reveal-mask block pb-1"><span class="reveal-text delay-200">LET'S BEGIN A</span></span>
                        <span class="reveal-mask block w-full"><span class="reveal-text delay-300"><span class="fancy-c">C</span>ONVERSATION</span></span>
                    </h1>
                    <p class="text-[14px] md:text-[15px] leading-relaxed text-[#68635f] font-light max-w-[420px] reveal-mask block text-justify">
                        <span class="reveal-text delay-400">Tell us more about your space, your ideas, and your aspirations. We'll guide you through the next steps with care and intention.</span>
                    </p>
                </div>

                <div class="flex flex-col w-full pt-4 lg:pt-16 reveal-mask">
                    <div class="reveal-text delay-500 w-full fp-contact-form">
                        <?php
                        $cf7_id = dayanarc_get_contact_form_id();
                        if ( $cf7_id ) :
                            echo do_shortcode( '[contact-form-7 id="' . esc_attr( $cf7_id ) . '" html_class="flex flex-col gap-10 w-full"]' );
                        else : ?>
                            <p style="font-size:13px; color:#8c8783; line-height:1.8; padding:1rem 0;">
                                Contact form not set up yet. Run the
                                <a href="<?php echo esc_url( admin_url( 'themes.php?page=dayanarc-demo-import' ) ); ?>"
                                   style="color:#2c221a; text-decoration:underline;">demo importer</a> to activate it.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-[#f5f2ee] pt-16 pb-6 w-full flex flex-col relative">
            <div class="w-full max-w-[1440px] mx-auto px-6 md:px-12 lg:px-20 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8 mb-12">
                <div class="flex flex-col">
                    <div class="title-text text-2xl tracking-widest text-[#2c221a] mb-6 font-medium">DAYAN ARC</div>
                    <p class="text-[12px] leading-relaxed text-[#68635f] font-light max-w-[220px]">Bringing together creativity, expertise, and passion to deliver exceptional design solutions.</p>
                </div>

                <div class="grid grid-cols-[auto_1fr] gap-4 lg:gap-8">
                    <div><span class="text-[10px] uppercase tracking-[0.15em] text-[#8c8783] font-medium">MENU</span></div>
                    <div class="flex flex-col gap-4 text-[11px] font-semibold tracking-widest uppercase text-[#2c221a]">
                        <a href="#" onclick="fullpage_api.moveTo(2); return false;" class="footer-link">ABOUT US</a>
                        <a href="#" onclick="fullpage_api.moveTo(3); return false;" class="footer-link">PORTFOLIO</a>
                        <a href="#" onclick="fullpage_api.moveTo(4); return false;" class="footer-link">SERVICES</a>
                        <a href="#" onclick="fullpage_api.moveTo(5); return false;" class="footer-link">JOURNAL</a>
                    </div>
                </div>

                <div class="grid grid-cols-[auto_1fr] gap-4 lg:gap-8">
                    <div><span class="text-[10px] uppercase tracking-[0.15em] text-[#8c8783] font-medium">FOLLOW US</span></div>
                    <div class="flex flex-col gap-4 text-[11px] font-semibold tracking-widest uppercase text-[#2c221a]">
                        <a href="#" class="footer-link">INSTAGRAM</a>
                        <a href="#" class="footer-link">PINTEREST</a>
                        <a href="#" class="footer-link">BEHANCE</a>
                        <a href="#" class="footer-link">LINKEDIN</a>
                    </div>
                </div>

                <div class="grid grid-cols-[auto_1fr] gap-4 lg:gap-8">
                    <div><span class="text-[10px] uppercase tracking-[0.15em] text-[#8c8783] font-medium">CONTACT</span></div>
                    <div class="flex flex-col gap-4 text-[11px] font-semibold tracking-widest uppercase text-[#2c221a] leading-relaxed">
                        <p>RIYADH, SAUDI ARABIA</p>
                        <a href="mailto:<?php echo antispambot( 'dayanarc.co@gmail.com' ); ?>" class="footer-link lowercase"><?php echo antispambot( 'dayanarc.co@gmail.com' ); ?></a>
                        <a href="<?php echo esc_url( 'https://www.dayanarc.com' ); ?>" class="footer-link lowercase">www.dayanarc.com</a>
                    </div>
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
                <div class="w-1/5 h-[1px] bg-[#d1ccc8] mb-4"></div>
                <div class="text-center text-[10px] tracking-widest uppercase text-[#8c8783] font-medium">
                    COPYRIGHT <?php echo esc_html( date( 'Y' ) ); ?> &copy; DESIGNED BY <a href="https://valasolution.com/" target="_blank" rel="noopener noreferrer" class="vala-link">VALASOLUTION</a>
                </div>
            </div>
        </footer>

    </div><!-- end section 6 -->

</div><!-- end #fullpage -->

<?php get_footer(); ?>

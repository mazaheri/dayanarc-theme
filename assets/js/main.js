(function () {
    'use strict';

    // ── Hero spotlight ──────────────────────────────────────────────────────────
    const spotlight = document.getElementById('spotlight');
    const hero = document.querySelector('.hero-section');

    if (hero && spotlight) {
        hero.addEventListener('mousemove', function (e) {
            const rect = hero.getBoundingClientRect();
            spotlight.style.setProperty('--x', (e.clientX - rect.left) + 'px');
            spotlight.style.setProperty('--y', (e.clientY - rect.top) + 'px');
        });
        hero.addEventListener('mouseleave', function () {
            spotlight.style.setProperty('--x', '50%');
            spotlight.style.setProperty('--y', '50%');
        });
    }

    // ── Services shine ──────────────────────────────────────────────────────────
    const servicesSection = document.getElementById('services-section');
    const servicesShine   = document.getElementById('services-shine');

    if (servicesSection && servicesShine) {
        servicesSection.addEventListener('mousemove', function (e) {
            const rect = servicesSection.getBoundingClientRect();
            servicesShine.style.setProperty('--x', (e.clientX - rect.left) + 'px');
            servicesShine.style.setProperty('--y', (e.clientY - rect.top) + 'px');
        });
        servicesSection.addEventListener('mouseleave', function () {
            servicesShine.style.setProperty('--x', '50%');
            servicesShine.style.setProperty('--y', '50%');
        });
    }

    // ── Mobile menu ─────────────────────────────────────────────────────────────
    const menuBtn     = document.getElementById('menuBtn');
    const mobileMenu  = document.getElementById('mobileMenu');
    const menuOverlay = document.getElementById('menuOverlay');
    const menuIcon    = document.getElementById('menuIcon');

    const closeMobileMenu = function () {
        mobileMenu.classList.remove('active');
        menuOverlay.classList.remove('active');
        menuIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
    };

    if (menuBtn) {
        menuBtn.addEventListener('click', function () {
            mobileMenu.classList.toggle('active');
            menuOverlay.classList.toggle('active');
            const isOpen = mobileMenu.classList.contains('active');
            menuIcon.innerHTML = isOpen
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
        });

        if (menuOverlay) menuOverlay.addEventListener('click', closeMobileMenu);

        document.querySelectorAll('#mobileMenu a').forEach(function (link) {
            link.addEventListener('click', closeMobileMenu);
        });
    }

    // ── Loader / intro curtain ──────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const loader = document.getElementById('loader');
        setTimeout(function () { if (loader) loader.classList.add('loader-hide'); }, 300);
        setTimeout(function () { triggerSectionAnimations(1); }, 800);
    });

    // ── Section animation helpers ───────────────────────────────────────────────
    function triggerSectionAnimations(sectionIndex) {
        const section = document.querySelectorAll('.section')[sectionIndex - 1];
        if (!section) return;
        section.querySelectorAll('.reveal-text').forEach(function (el) { el.classList.add('active'); });
        section.querySelectorAll('.curtain-img').forEach(function (el) { el.classList.add('active'); });
        section.querySelectorAll('.curtain-img-portfolio').forEach(function (el) { el.classList.add('active'); });
        section.querySelectorAll('.link-wrapper').forEach(function (el) { el.classList.add('active'); });
        section.querySelectorAll('.card-wrapper').forEach(function (el) { el.classList.add('active'); });
        section.querySelectorAll('.bg-fade').forEach(function (el) { el.classList.add('active'); });
        section.querySelectorAll('.curtain-mask').forEach(function (el) { el.classList.add('active'); });
    }

    function removeSectionAnimations(sectionIndex) {
        const section = document.querySelectorAll('.section')[sectionIndex - 1];
        if (!section) return;
        section.querySelectorAll('.reveal-text').forEach(function (el) { el.classList.remove('active'); });
        section.querySelectorAll('.curtain-img').forEach(function (el) { el.classList.remove('active'); });
        section.querySelectorAll('.curtain-img-portfolio').forEach(function (el) { el.classList.remove('active'); });
        section.querySelectorAll('.link-wrapper').forEach(function (el) { el.classList.remove('active'); });
        section.querySelectorAll('.card-wrapper').forEach(function (el) { el.classList.remove('active'); });
        section.querySelectorAll('.bg-fade').forEach(function (el) { el.classList.remove('active'); });
        section.querySelectorAll('.curtain-mask').forEach(function (el) { el.classList.remove('active'); });
    }

    // ── Scroll-to-top + fullPage.js ─────────────────────────────────────────────
    const scrollToTopBtn = document.getElementById('scrollToTop');

    if (scrollToTopBtn) {
        const progressCircle = scrollToTopBtn.querySelector('.progress-ring-circle');
        const circumference  = 2 * Math.PI * 28;

        progressCircle.style.strokeDasharray  = circumference + ' ' + circumference;
        progressCircle.style.strokeDashoffset = circumference;

        function updateScrollProgress(currentSection, totalSections) {
            currentSection > 0
                ? scrollToTopBtn.classList.add('visible')
                : scrollToTopBtn.classList.remove('visible');
            const offset = circumference - ((currentSection / (totalSections - 1)) * circumference);
            progressCircle.style.strokeDashoffset = offset;
        }

        scrollToTopBtn.addEventListener('click', function () { fullpage_api.moveTo(1); });

        new fullpage('#fullpage', {
            autoScrolling:      true,
            scrollHorizontally: true,
            navigation:         true,
            navigationPosition: 'right',
            scrollingSpeed:     700,
            easingcss3:         'cubic-bezier(0.77, 0, 0.175, 1)',
            fitToSection:       true,
            fitToSectionDelay:  600,
            scrollBar:          false,
            css3:               true,
            responsiveWidth:    768,
            onLeave: function (origin, destination) {
                removeSectionAnimations(origin.index + 1);
                updateScrollProgress(destination.index, document.querySelectorAll('.section').length);
            },
            afterLoad: function (origin, destination) {
                triggerSectionAnimations(destination.index + 1);
                updateScrollProgress(destination.index, document.querySelectorAll('.section').length);
            }
        });
    }

    // ── Portfolio carousel ──────────────────────────────────────────────────────
    const portfolios          = dayanarcData.portfolioData;
    let currentPortfolioIndex = 0;

    function renderPortfolioSlide(index) {
        const container = document.getElementById('portfolio-container');
        if (!container) return;
        const data = portfolios[index];

        container.innerHTML =
            '<div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-8 items-start">' +
                '<div class="order-2 lg:order-1">' +
                    '<div class="curtain-container aspect-square w-full">' +
                        '<img src="' + escHtml(data.imgLarge) + '" class="curtain-img-portfolio active" alt="' + escHtml(data.title) + '">' +
                    '</div>' +
                '</div>' +
                '<div class="order-1 lg:order-2 flex flex-col h-full lg:min-h-[550px]">' +
                    '<div class="flex justify-between items-center mb-12 lg:mb-16">' +
                        '<span class="text-[12px] text-[#68635f] font-light">' + escHtml(data.location) + '</span>' +
                        '<span class="text-[12px] text-[#68635f] font-light tracking-widest">' + escHtml(data.id) + '</span>' +
                    '</div>' +
                    '<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-auto">' +
                        '<div class="flex flex-col">' +
                            '<h2 class="title-text text-3xl lg:text-4xl mb-6 text-[#2c221a] font-medium tracking-wide uppercase">' + escHtml(data.title) + '</h2>' +
                            '<p class="text-[14px] leading-relaxed text-[#68635f] font-light">' + escHtml(data.description) + '</p>' +
                        '</div>' +
                        '<div class="flex flex-col">' +
                            '<div class="curtain-container aspect-square w-full max-w-[280px] ml-auto">' +
                                '<img src="' + escHtml(data.imgSmall) + '" class="curtain-img-portfolio active" alt="Detail">' +
                            '</div>' +
                            '<p class="text-[11px] leading-relaxed text-[#68635f] font-light mt-4 text-right max-w-[280px] ml-auto">' + escHtml(data.palette) + '</p>' +
                            (data.permalink ? '<a href="' + escHtml(data.permalink) + '" class="link-wrapper" style="opacity:1;transform:none;width:auto;gap:0.75rem;min-width:100px;margin-top:1rem;margin-left:auto;"><span class="link-text" style="font-size:11px;">LEARN MORE</span><div class="arrow-graphic"><svg width="14" height="9" viewBox="0 0 16 10" fill="none"><path d="M11 1L15 5M15 5L11 9M15 5H0" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div></a>' : '') +
                        '</div>' +
                    '</div>' +
                    '<div class="mt-12 lg:mt-auto pt-8 flex flex-col md:flex-row justify-between items-end gap-8">' +
                        '<div class="text-[12px] text-[#68635f] font-light leading-relaxed"><p>' + escHtml(data.concept) + '</p></div>' +
                        '<div class="flex gap-4">' +
                            '<button onclick="prevPortfolioSlide()" class="nav-btn">' +
                                '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2c221a" stroke-width="1.2"><path d="M19 12H5M5 12L12 19M5 12L12 5"/></svg>' +
                            '</button>' +
                            '<button onclick="nextPortfolioSlide()" class="nav-btn">' +
                                '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2c221a" stroke-width="1.2"><path d="M5 12H19M19 12L12 5M19 12L12 19"/></svg>' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
    }

    window.nextPortfolioSlide = function () {
        const container = document.getElementById('portfolio-container');
        container.classList.add('slide-exit');
        setTimeout(function () {
            currentPortfolioIndex = (currentPortfolioIndex + 1) % portfolios.length;
            renderPortfolioSlide(currentPortfolioIndex);
            container.classList.remove('slide-exit');
            container.classList.add('slide-enter');
            setTimeout(function () { container.classList.remove('slide-enter'); }, 50);
        }, 600);
    };

    window.prevPortfolioSlide = function () {
        const container = document.getElementById('portfolio-container');
        container.classList.add('slide-exit');
        setTimeout(function () {
            currentPortfolioIndex = (currentPortfolioIndex - 1 + portfolios.length) % portfolios.length;
            renderPortfolioSlide(currentPortfolioIndex);
            container.classList.remove('slide-exit');
            container.classList.add('slide-enter');
            setTimeout(function () { container.classList.remove('slide-enter'); }, 50);
        }, 600);
    };

    // ── Journal / Blog grid ─────────────────────────────────────────────────────
    const journalData       = dayanarcData.journalPages;
    let currentJournalPage  = 0;

    const slotConfigs = [
        { colSpan: 'md:col-span-2', align: 'justify-start', height: 'h-[700px]', titleSize: 'text-lg',  delay: 'delay-200' },
        { colSpan: 'md:col-span-1', align: 'justify-start', height: 'h-[380px]', titleSize: 'text-sm',  delay: 'delay-300' },
        { colSpan: 'md:col-span-1', align: 'justify-end',   height: 'h-[380px]', titleSize: 'text-sm',  delay: 'delay-400' },
        { colSpan: 'md:col-span-1', align: 'justify-start', height: 'h-[380px]', titleSize: 'text-sm',  delay: 'delay-500' }
    ];

    function renderJournalPage(pageIndex) {
        const grid = document.getElementById('journal-grid');
        const nums = document.getElementById('pagination-numbers');
        if (!grid || !nums) return;

        nums.innerHTML = journalData.map(function (_, i) {
            const active = i === pageIndex
                ? 'text-[#2c221a] border-b border-[#2c221a] pb-1'
                : 'text-[#a9a39f] hover:text-[#2c221a]';
            return '<span class="cursor-pointer transition-colors ' + active + '" onclick="goToJournalPage(' + i + ')">0' + (i + 1) + '</span>';
        }).join('');

        grid.innerHTML = journalData[pageIndex].map(function (post, i) {
            const conf  = slotConfigs[i];
            const hSize = i === 0 ? 'text-2xl lg:text-3xl' : 'text-xl';
            return (
                '<div class="' + conf.colSpan + ' flex flex-col ' + conf.align + (i > 0 ? ' h-[700px]' : '') + '">' +
                    '<div class="journal-card group relative ' + conf.height + ' w-full cursor-pointer">' +
                        '<div class="curtain-container h-full">' +
                            '<div class="curtain-mask active ' + conf.delay + ' h-full">' +
                                '<img src="' + escHtml(post.img) + '" alt="' + escHtml(post.title) + '" class="curtain-img-blog">' +
                            '</div>' +
                        '</div>' +
                        '<div class="hover-overlay shadow-sm">' +
                            '<h3 class="title-text ' + hSize + ' text-[#2c221a] uppercase mb-3 leading-tight">' + escHtml(post.title) + '</h3>' +
                            '<p class="text-[12px] md:text-[13px] leading-relaxed text-[#68635f] font-light px-2 mb-4">' + escHtml(post.desc) + '</p>' +
                            '<a href="' + escHtml(post.url) + '" class="read-more-btn text-[10px] tracking-widest font-semibold flex items-center gap-2 text-[#2c221a]">' +
                                'READ MORE ' +
                                '<svg width="14" height="8" viewBox="0 0 16 10" fill="none" stroke="currentColor"><path d="M11 1L15 5M15 5L11 9M15 5H0" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
                            '</a>' +
                        '</div>' +
                    '</div>' +
                    '<div class="w-full mt-3">' +
                        '<span class="title-text ' + conf.titleSize + ' uppercase tracking-wider text-[#2c221a] block">' + escHtml(post.title) + '</span>' +
                    '</div>' +
                '</div>'
            );
        }).join('');
    }

    window.changePage = function (dir) {
        goToJournalPage((currentJournalPage + dir + journalData.length) % journalData.length);
    };

    window.goToJournalPage = function (index) {
        if (index === currentJournalPage) return;
        const grid = document.getElementById('journal-grid');
        grid.style.opacity = '0';
        setTimeout(function () {
            currentJournalPage = index;
            renderJournalPage(currentJournalPage);
            grid.style.opacity = '1';
        }, 500);
    };

    // Contact form is now handled by Contact Form 7.

    // ── Utility ─────────────────────────────────────────────────────────────────
    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // ── Initialise ───────────────────────────────────────────────────────────────
    renderPortfolioSlide(0);
    renderJournalPage(0);

}());

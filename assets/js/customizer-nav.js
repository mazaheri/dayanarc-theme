( function ( wp ) {
    'use strict';

    wp.customize.bind( 'ready', function () {
        var urls     = window.dayanarcCustomizerUrls || {};
        var previewer = wp.customize.previewer;

        /*
         * Panel-level navigation.
         * Expanding a panel navigates the preview to the right page,
         * so the sections inside become visible immediately.
         */
        var panelUrlMap = {
            'dayanarc_homepage': urls.home,
            'dayanarc_pages':    null,   // sections within handle their own URLs
            'dayanarc_general':  null,   // sitewide — no navigation needed
        };

        Object.keys( panelUrlMap ).forEach( function ( panelId ) {
            var targetUrl = panelUrlMap[ panelId ];
            if ( ! targetUrl ) return;

            wp.customize.panel( panelId, function ( panel ) {
                panel.expanded.bind( function ( isExpanded ) {
                    if ( isExpanded ) {
                        previewer.previewUrl.set( targetUrl );
                    }
                } );
            } );
        } );

        /*
         * Section-level navigation for sections in the Other Pages panel.
         * Homepage panel sections (portfolio, journal) are covered by the
         * panel-level navigation above — they must NOT override it here,
         * because those sections are embedded in the front page, not their
         * respective archive pages.
         */
        var sectionUrlMap = {
            'dayanarc_contact_page': urls.contact,
        };

        Object.keys( sectionUrlMap ).forEach( function ( sectionId ) {
            var targetUrl = sectionUrlMap[ sectionId ];
            if ( ! targetUrl ) return;

            wp.customize.section( sectionId, function ( section ) {
                section.expanded.bind( function ( isExpanded ) {
                    if ( isExpanded ) {
                        previewer.previewUrl.set( targetUrl );
                    }
                } );
            } );
        } );
    } );

} )( wp );

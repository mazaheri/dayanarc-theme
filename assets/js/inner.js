(function () {
    'use strict';

    // Scroll-to-top for inner pages
    var btn = document.getElementById('innerScrollTop');
    if (btn) {
        window.addEventListener('scroll', function () {
            btn.classList.toggle('visible', window.scrollY > 300);
        });
        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Curtain animation trigger for archive pages (replaces fullPage.js section trigger)
    var curtains = document.querySelectorAll('.archive-curtain');
    if (curtains.length > 0 && 'IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        curtains.forEach(function (el) { observer.observe(el); });
    } else {
        // Fallback: show all immediately if IntersectionObserver is unavailable
        curtains.forEach(function (el) { el.classList.add('active'); });
    }


    // Journal Load More
    var loadMoreBtn = document.getElementById('journal-load-more');
    var mosaicGrid  = document.getElementById('journal-mosaic-grid');

    if (loadMoreBtn && mosaicGrid && typeof dayanarcInner !== 'undefined') {
        loadMoreBtn.addEventListener('click', function () {
            var offset = parseInt(loadMoreBtn.dataset.offset, 10);

            loadMoreBtn.disabled = true;
            loadMoreBtn.textContent = 'LOADING…';

            var formData = new FormData();
            formData.append('action', 'dayanarc_load_more_journal');
            formData.append('nonce',  dayanarcInner.nonce);
            formData.append('offset', offset);

            fetch(dayanarcInner.ajaxUrl, { method: 'POST', body: formData })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.html) {
                        mosaicGrid.insertAdjacentHTML('beforeend', data.html);
                    }
                    loadMoreBtn.dataset.offset = offset + 4;
                    if (!data.has_more) {
                        loadMoreBtn.style.display = 'none';
                    } else {
                        loadMoreBtn.disabled = false;
                        loadMoreBtn.innerHTML = 'LOAD MORE <svg width="14" height="8" viewBox="0 0 16 10" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M8 1L8 9M8 9L4 5M8 9L12 5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                    }
                })
                .catch(function () {
                    loadMoreBtn.disabled = false;
                    loadMoreBtn.innerHTML = 'LOAD MORE <svg width="14" height="8" viewBox="0 0 16 10" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M8 1L8 9M8 9L4 5M8 9L12 5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                });
        });
    }

}());

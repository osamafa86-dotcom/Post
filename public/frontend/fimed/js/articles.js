/**
 * FIMED - Articles Page Scripts
 * Handles filter toggle, view toggle, and filter tags
 */

document.addEventListener('DOMContentLoaded', function () {

    // Advanced Filter Toggle
    const advancedToggle = document.getElementById('advancedToggle');
    const advancedFilter = document.getElementById('advancedFilter');

    if (advancedToggle && advancedFilter) {
        advancedToggle.addEventListener('click', function () {
            this.classList.toggle('active');
            advancedFilter.classList.toggle('show');
        });
    }

    // View Toggle
    const viewBtns = document.querySelectorAll('.filter-view-btn');
    const articlesGrid = document.getElementById('articlesGrid');

    if (viewBtns.length && articlesGrid) {
        viewBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                viewBtns.forEach(function (b) {
                    b.classList.remove('active');
                });
                this.classList.add('active');

                if (this.querySelector('.fa-list')) {
                    articlesGrid.classList.add('articles-grid--list');
                } else {
                    articlesGrid.classList.remove('articles-grid--list');
                }
            });
        });
    }

    // Filter Tags
    const filterTags = document.querySelectorAll('.filter-tag');

    filterTags.forEach(function (tag) {
        tag.addEventListener('click', function () {
            filterTags.forEach(function (t) {
                t.classList.remove('active');
            });
            this.classList.add('active');
        });
    });

    // Pagination
    const paginationBtns = document.querySelectorAll('.pagination__btn:not(.pagination__btn--arrow)');

    paginationBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            paginationBtns.forEach(function (b) {
                b.classList.remove('active');
            });
            this.classList.add('active');
        });
    });

});

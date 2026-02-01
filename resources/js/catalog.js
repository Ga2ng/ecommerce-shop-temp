/**
 * Catalog Page JavaScript
 * jQuery is available globally as $ and jQuery
 */

// Wait for DOM to be ready
$(document).ready(function() {
    // Mobile menu toggle
    $('#mobile-menu-toggle').on('click', function() {
        $('#mobile-menu').toggleClass('hidden');
    });

    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(e) {
        const target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 1000);
        }
    });

    // Product card hover effects
    $('.product-card').on('mouseenter', function() {
        $(this).find('.product-image').addClass('scale-105');
    }).on('mouseleave', function() {
        $(this).find('.product-image').removeClass('scale-105');
    });

    // Filter form auto-submit on change (with debounce)
    let filterTimeout;
    $('.filter-input').on('change', function() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(function() {
            $('.filter-form').submit();
        }, 300);
    });

    // Clear filters button
    $('.clear-filters').on('click', function(e) {
        e.preventDefault();
        window.location.href = $(this).attr('href');
    });
});


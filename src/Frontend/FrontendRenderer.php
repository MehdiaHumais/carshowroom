<?php
namespace CarShowroom\Frontend;

class FrontendRenderer {
    public static function init() {
        add_filter('the_content', [self::class, 'render_booking_form']);
    }

    public static function render_booking_form($content) {
        if (get_post_type() === 'car' && is_singular('car')) {
            $content .= '<button class="book-this-car">Book This Car</button>';
        }
        return $content;
    }
}

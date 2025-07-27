<?php
namespace CarShowroom\Frontend;

class BookingHandler {
    public static function register() {
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_scripts']);
        add_action('wp_ajax_book_car', [self::class, 'handle_booking']);
        add_action('wp_ajax_nopriv_book_car', [self::class, 'handle_booking']);
    }

    public static function enqueue_scripts() {
        wp_enqueue_script('car-booking', plugin_dir_url(__FILE__) . '../../assets/js/booking.js', ['jquery'], null, true);
        wp_localize_script('car-booking', 'carBooking', [
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);
    }

    public static function handle_booking() {
        wp_send_json_success(['message' => 'Car booked successfully!']);
    }
}

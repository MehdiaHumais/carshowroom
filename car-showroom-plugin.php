<?php
/**
 * Plugin Name: Car Showroom Plugin
 * Description: Manage and display cars with REST API and WooCommerce integration.
 * Version: 1.1
 * Author: Mehdia Humais
 */

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/vendor/autoload.php';

use CarShowroom\Init;

add_action('after_setup_theme', function () {
    add_theme_support('post-thumbnails');
});

if (class_exists('CarShowroom\Init')) {
    Init::register();
}

// WooCommerce support
function carshowroom_add_car_booking_to_cart() {
    if (isset($_GET['book_car_id'])) {
        $car_id = intval($_GET['book_car_id']);

        // Create a dummy WooCommerce product if not exists
        $product_id = get_option('car_booking_virtual_product_id');
        if (!$product_id || get_post_type($product_id) !== 'product') {
            $product_id = wp_insert_post([
                'post_title' => 'Car Booking',
                'post_type' => 'product',
                'post_status' => 'publish'
            ]);
            update_post_meta($product_id, '_price', 0);
            update_post_meta($product_id, '_virtual', 'yes');
            update_post_meta($product_id, '_sold_individually', 'yes');
            update_option('car_booking_virtual_product_id', $product_id);
        }

        WC()->cart->add_to_cart($product_id, 1, 0, [], ['car_id' => $car_id]);
        wp_redirect(wc_get_cart_url());
        exit;
    }
}
add_action('template_redirect', 'carshowroom_add_car_booking_to_cart');

// Show car ID in cart and checkout
function carshowroom_display_car_meta($item_data, $cart_item) {
    if (isset($cart_item['car_id'])) {
        $car_id = $cart_item['car_id'];
        $item_data[] = [
            'name' => 'Car Booked',
            'value' => get_the_title($car_id)
        ];
    }
    return $item_data;
}
add_filter('woocommerce_get_item_data', 'carshowroom_display_car_meta', 10, 2);

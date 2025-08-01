<?php

namespace CarShowroom\Frontend;

if (!defined('ABSPATH')) {
    exit;
}

class BookingHandler {
    public function register() {
        add_action('wp_ajax_book_this_car', [$this, 'handle_ajax_booking']);
        add_action('wp_ajax_nopriv_book_this_car', [$this, 'handle_ajax_booking']);
    }

    public function handle_ajax_booking() {
        // Ensure WooCommerce is available
        if (!function_exists('WC') || !WC()) {
            wp_send_json_error('Booking failed: WooCommerce not active.');
        }

        // Nonce check (optional if you localize one)
        if (isset($_POST['_ajax_nonce']) && !wp_verify_nonce($_POST['_ajax_nonce'], 'book_car_nonce')) {
            wp_send_json_error('Invalid security token.');
        }

        if (!isset($_POST['car_id'])) {
            wp_send_json_error('Missing car ID.');
        }

        $car_id = intval($_POST['car_id']);
        if (!$car_id || get_post_type($car_id) !== 'car') {
            wp_send_json_error('Invalid car.');
        }

        // Fetch car meta
        $title        = get_the_title($car_id);
        $raw_price    = get_post_meta($car_id, 'price', true);
        $price        = $raw_price ? floatval(preg_replace('/[^0-9\.]/', '', $raw_price)) : 0;
        $mileage      = get_post_meta($car_id, 'mileage', true);
        $condition    = get_post_meta($car_id, 'condition', true);
        $demand_price = get_post_meta($car_id, 'demand_price', true);
        $owner_name   = get_post_meta($car_id, 'owner_name', true);
        $owner_phone  = get_post_meta($car_id, 'owner_phone', true);
        $image_url    = get_the_post_thumbnail_url($car_id, 'thumbnail');

        // Create or reuse hidden product for this car
        $product_id = $this->get_or_create_hidden_product($car_id, $title, $price, $image_url);

        if (!$product_id) {
            wp_send_json_error('Failed to create product for car.');
        }

        // Create a new order
        $order = wc_create_order([
            'status' => 'pending', // or 'processing' as you wish
        ]);

        if (is_wp_error($order)) {
            wp_send_json_error('Could not create order.');
        }

        // Add product to order
        $item = $order->add_product(wc_get_product($product_id), 1, [
            'subtotal' => $price,
            'total'    => $price,
        ]);

        // Attach car-specific metadata to order line item
        if ($item) {
            $item->add_meta_data('Car ID', $car_id, true);
            $item->add_meta_data('Car Name', $title, true);
            $item->add_meta_data('Price', wc_price($price), true);
            if ($mileage) {
                $item->add_meta_data('Mileage', $mileage, true);
            }
            if ($condition) {
                $item->add_meta_data('Condition', $condition, true);
            }
            if ($demand_price) {
                $item->add_meta_data('Demand Price', $demand_price, true);
            }
            if ($owner_name) {
                $item->add_meta_data('Owner Name', $owner_name, true);
            }
            if ($owner_phone) {
                $item->add_meta_data('Owner Phone', $owner_phone, true);
            }
            if ($image_url) {
                $item->add_meta_data('Car Image', $image_url, true);
            }
            $item->save();
        }

        // Set customer if logged in
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $order->set_customer_id($user->ID);
        }

        // Finalize order totals and save
        $order->calculate_totals();
        $order->save();

        // Optional: you can email admin or customer here.

        // Return the admin order edit URL so they can immediately be taken there
        $order_edit_url = admin_url('post.php?post=' . $order->get_id() . '&action=edit');

        wp_send_json_success([
            'message' => 'Car booked and order created.',
            'order_id' => $order->get_id(),
            'order_admin_url' => $order_edit_url,
        ]);
    }

    private function get_or_create_hidden_product($car_id, $title, $price, $image_url) {
        $linked = get_post_meta($car_id, '_linked_product_id', true);
        if ($linked && get_post_status($linked) === 'publish') {
            return $linked;
        }

        $product = new \WC_Product_Simple();
        $product->set_name($title);
        $product->set_price($price);
        $product->set_regular_price($price);
        $product->set_virtual(true);
        $product->set_catalog_visibility('hidden');
        $product->set_sold_individually(true);
        $product_id = $product->save();

        if ($image_url) {
            $attachment_id = attachment_url_to_postid($image_url);
            if ($attachment_id) {
                update_post_meta($product_id, '_thumbnail_id', $attachment_id);
            }
        }

        update_post_meta($car_id, '_linked_product_id', $product_id);
        return $product_id;
    }
}

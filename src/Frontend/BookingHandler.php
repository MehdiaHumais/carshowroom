<?php
namespace CarShowroom\Frontend;

if (!defined('ABSPATH')) exit;

class BookingHandler {
    public function register() {
        // AJAX booking
        add_action('wp_ajax_book_this_car', [$this, 'handle_ajax_booking']);
        add_action('wp_ajax_nopriv_book_this_car', [$this, 'handle_ajax_booking']);

        // Persist custom cart item data
        add_filter('woocommerce_add_cart_item_data', [$this, 'attach_car_meta_to_cart'], 10, 3);
        add_filter('woocommerce_get_cart_item_from_session', [$this, 'restore_cart_item_from_session'], 10, 2);

        // Display in cart
        add_filter('woocommerce_cart_item_name', [$this, 'show_car_image_and_title'], 10, 3);
        add_filter('woocommerce_get_item_data', [$this, 'display_car_meta_in_cart'], 10, 2);

        // Add meta during normal checkout
        add_action('woocommerce_checkout_create_order_line_item', [$this, 'add_car_meta_to_order'], 10, 4);
    }

    public function handle_ajax_booking() {
        if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], 'book_car_nonce')) {
            wp_send_json_error('Security check failed.');
        }

        if (empty($_POST['car_id'])) {
            wp_send_json_error('Missing car ID.');
        }

        $car_id = intval($_POST['car_id']);
        if (!$car_id || get_post_type($car_id) !== 'car') {
            wp_send_json_error('Invalid car.');
        }

        if (!class_exists('\WooCommerce')) {
            wp_send_json_error('WooCommerce not active.');
        }

        // Prevent duplicate same car in cart
        if ($this->is_car_already_in_cart($car_id)) {
            wp_send_json_success([
                'message'  => 'Car already in cart.',
                'cart_url' => wc_get_cart_url(),
            ]);
        }

        // Gather data
        $title = get_the_title($car_id);
        $raw_price = get_post_meta($car_id, 'price', true);
        $price = $raw_price ? floatval(preg_replace('/[^0-9\.]/', '', $raw_price)) : 0;
        $mileage = get_post_meta($car_id, 'mileage', true);
        $condition = get_post_meta($car_id, 'condition', true);
        $demand_price = get_post_meta($car_id, 'demand_price', true);
        $owner_name = get_post_meta($car_id, 'owner_name', true);
        $owner_phone = get_post_meta($car_id, 'owner_phone', true);
        $image_url = get_the_post_thumbnail_url($car_id, 'thumbnail');

        // Ensure cart is loaded
        if (!WC()->cart) {
            wc_load_cart();
        }

        // Prepare product
        $product_id = $this->get_or_create_hidden_product($car_id, $title, $price, $image_url);
        if (!$product_id) {
            wp_send_json_error('Failed to create product.');
        }

        // Add to cart
        WC()->cart->add_to_cart($product_id, 1, 0, [], [
            'car_id'        => $car_id,
            'car_title'     => $title,
            'car_price'     => $price,
            'car_image'     => $image_url,
            'mileage'       => $mileage,
            'condition'     => $condition,
            'demand_price'  => $demand_price,
            'owner_name'    => $owner_name,
            'owner_phone'   => $owner_phone,
            'unique_key'    => uniqid('car_', true),
        ]);

        // Create an order immediately with full meta
        $order = $this->create_order_for_car($car_id, $product_id, [
            'title'         => $title,
            'price'         => $price,
            'mileage'       => $mileage,
            'condition'     => $condition,
            'demand_price'  => $demand_price,
            'owner_name'    => $owner_name,
            'owner_phone'   => $owner_phone,
        ]);

        $response = [
            'message'  => 'Car booked successfully.',
            'cart_url' => wc_get_cart_url(),
        ];
        if ($order) {
            $response['order_id'] = $order->get_id();
        }

        wp_send_json_success($response);
    }

    private function is_car_already_in_cart($car_id) {
        if (!WC()->cart) return false;
        foreach (WC()->cart->get_cart() as $cart_item) {
            if (isset($cart_item['car_id']) && intval($cart_item['car_id']) === $car_id) {
                return true;
            }
        }
        return false;
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

    private function create_order_for_car($car_id, $product_id, $car_meta) {
        if (!function_exists('wc_create_order')) {
            return null;
        }

        $order = wc_create_order();

        if (is_user_logged_in()) {
            $order->set_customer_id(get_current_user_id());
        }

        $product = wc_get_product($product_id);
        if (!$product) {
            return null;
        }

        // Add product line with price
        $order_item = new \WC_Order_Item_Product();
        $order_item->set_product($product);
        $order_item->set_quantity(1);
        $order_item->set_subtotal($car_meta['price']);
        $order_item->set_total($car_meta['price']);

        // Add car meta to that line item
        $order_item->add_meta_data('Car ID', $car_id, true);
        $order_item->add_meta_data('Car Name', $car_meta['title'], true);
        $order_item->add_meta_data('Mileage', $car_meta['mileage'], true);
        $order_item->add_meta_data('Condition', $car_meta['condition'], true);
        $order_item->add_meta_data('Price', $car_meta['price'], true);
        $order_item->add_meta_data('Demanded Price', $car_meta['demand_price'], true);
        $order_item->add_meta_data('Owner Name', $car_meta['owner_name'], true);
        $order_item->add_meta_data('Owner Phone', $car_meta['owner_phone'], true);

        $order->add_item($order_item);
        $order->calculate_totals();
        $order->update_status('processing', 'Auto-created from car booking');

        return $order;
    }

    public function attach_car_meta_to_cart($cart_item_data, $product_id, $variation_id) {
        if (!empty($_POST['car_id'])) {
            $car_id = intval($_POST['car_id']);
            $cart_item_data['car_id']       = $car_id;
            $cart_item_data['car_title']    = get_the_title($car_id);
            $raw_price = get_post_meta($car_id, 'price', true);
            $cart_item_data['car_price']    = $raw_price ? floatval(preg_replace('/[^0-9\.]/', '', $raw_price)) : 0;
            $cart_item_data['car_image']    = get_the_post_thumbnail_url($car_id, 'thumbnail');
            $cart_item_data['mileage']      = get_post_meta($car_id, 'mileage', true);
            $cart_item_data['condition']    = get_post_meta($car_id, 'condition', true);
            $cart_item_data['demand_price'] = get_post_meta($car_id, 'demand_price', true);
            $cart_item_data['owner_name']   = get_post_meta($car_id, 'owner_name', true);
            $cart_item_data['owner_phone']  = get_post_meta($car_id, 'owner_phone', true);
            $cart_item_data['unique_key']   = uniqid('car_', true);
        }
        return $cart_item_data;
    }

    public function restore_cart_item_from_session($cart_item, $values) {
        if (isset($values['car_id'])) {
            $cart_item['car_id']       = $values['car_id'];
            $cart_item['car_title']    = $values['car_title'] ?? '';
            $cart_item['car_price']    = $values['car_price'] ?? 0;
            $cart_item['car_image']    = $values['car_image'] ?? '';
            $cart_item['mileage']      = $values['mileage'] ?? '';
            $cart_item['condition']    = $values['condition'] ?? '';
            $cart_item['demand_price'] = $values['demand_price'] ?? '';
            $cart_item['owner_name']   = $values['owner_name'] ?? '';
            $cart_item['owner_phone']  = $values['owner_phone'] ?? '';
            $cart_item['unique_key']   = $values['unique_key'] ?? uniqid('car_', true);
        }
        return $cart_item;
    }

    public function show_car_image_and_title($name, $cart_item, $cart_item_key) {
        if (isset($cart_item['car_id'])) {
            $img = isset($cart_item['car_image']) && $cart_item['car_image']
                ? '<img src="' . esc_url($cart_item['car_image']) . '" style="width:50px; height:auto; margin-right:8px; vertical-align:middle; border-radius:4px;" alt="Car"/>'
                : '';
            $title = isset($cart_item['car_title']) ? esc_html($cart_item['car_title']) : $name;
            return '<div style="display:flex; align-items:center; gap:6px;">' . $img . '<div>' . $title . '</div></div>';
        }
        return $name;
    }

    public function display_car_meta_in_cart($item_data, $cart_item) {
        if (!isset($cart_item['car_id'])) {
            return $item_data;
        }

        $meta_map = [
            'mileage'      => 'Mileage',
            'condition'    => 'Condition',
            'car_price'    => 'Price',
            'demand_price' => 'Demanded Price',
            'owner_name'   => 'Owner Name',
            'owner_phone'  => 'Owner Phone',
        ];

        foreach ($meta_map as $key => $label) {
            if (!empty($cart_item[$key])) {
                $value = $cart_item[$key];
                if ($key === 'car_price') {
                    $value = preg_replace('/\s*Rs\.?\s*/i', '', $value);
                }
                $item_data[] = [
                    'key'   => $label,
                    'value' => esc_html($value),
                ];
            }
        }

        return $item_data;
    }

    public function add_car_meta_to_order($item, $cart_item_key, $values, $order) {
        if (!isset($values['car_id'])) {
            return;
        }

        $car_id = $values['car_id'];
        $item->add_meta_data('Car ID', $car_id, true);
        $item->add_meta_data('Car Name', get_the_title($car_id), true);
        $item->add_meta_data('Mileage', get_post_meta($car_id, 'mileage', true), true);
        $item->add_meta_data('Condition', get_post_meta($car_id, 'condition', true), true);
        $item->add_meta_data('Price', get_post_meta($car_id, 'price', true), true);
        $item->add_meta_data('Demanded Price', get_post_meta($car_id, 'demand_price', true), true);
        $item->add_meta_data('Owner Name', get_post_meta($car_id, 'owner_name', true), true);
        $item->add_meta_data('Owner Phone', get_post_meta($car_id, 'owner_phone', true), true);

        // Save item so admin view reflects immediately
        $item->save();
    }
}

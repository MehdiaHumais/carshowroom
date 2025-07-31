<?php
namespace CarShowroom\Frontend;

class BookingHandler {
    public function register() {
        add_action('wp_ajax_book_this_car', [$this, 'handle_ajax_booking']);
        add_action('wp_ajax_nopriv_book_this_car', [$this, 'handle_ajax_booking']);

        add_filter('woocommerce_add_cart_item_data', [$this, 'attach_car_meta_to_cart'], 10, 3);
        add_filter('woocommerce_get_cart_item_from_session', [$this, 'restore_cart_item_from_session'], 10, 2);
        add_filter('woocommerce_cart_item_name', [$this, 'show_car_image_and_title'], 10, 3);
        add_filter('woocommerce_get_item_data', [$this, 'display_car_meta_in_cart'], 10, 2);
        add_action('woocommerce_checkout_create_order_line_item', [$this, 'add_car_meta_to_order'], 10, 4);
    }

    public function handle_ajax_booking() {
        if (!isset($_POST['car_id'])) {
            wp_send_json_error('Missing car ID');
        }

        $car_id = intval($_POST['car_id']);
        if (!$car_id || get_post_type($car_id) !== 'car') {
            wp_send_json_error('Invalid car');
        }

        // Get the car price and title and image
        $price = get_post_meta($car_id, 'price', true);
        $price = $price ? floatval(preg_replace('/[^0-9\.]/', '', $price)) : 0;
        $title = get_the_title($car_id);
        $image_url = get_the_post_thumbnail_url($car_id, 'thumbnail');

        // Create or reuse hidden product for this car
        $product_id = $this->get_or_create_hidden_product($car_id, $title, $price, $image_url);

        // Add to cart with car-specific meta
        WC()->cart->add_to_cart($product_id, 1, 0, [], [
            'car_id'     => $car_id,
            'car_title'  => $title,
            'car_price'  => $price,
            'car_image'  => $image_url,
            'unique_key' => uniqid('car_', true),
        ]);

        wp_send_json_success(['cart_url' => wc_get_cart_url()]);
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

        // Optionally set image if attachment exists
        if ($image_url) {
            $attachment_id = attachment_url_to_postid($image_url);
            if ($attachment_id) {
                update_post_meta($product_id, '_thumbnail_id', $attachment_id);
            }
        }

        update_post_meta($car_id, '_linked_product_id', $product_id);
        return $product_id;
    }

    public function attach_car_meta_to_cart($cart_item_data, $product_id, $variation_id) {
        if (isset($_POST['car_id'])) {
            $cart_item_data['car_id']    = intval($_POST['car_id']);
            $cart_item_data['car_title'] = get_the_title(intval($_POST['car_id']));
            $price_raw = get_post_meta(intval($_POST['car_id']), 'price', true);
            $cart_item_data['car_price'] = $price_raw ? floatval(preg_replace('/[^0-9\.]/', '', $price_raw)) : 0;
            $cart_item_data['car_image'] = get_the_post_thumbnail_url(intval($_POST['car_id']), 'thumbnail');
            $cart_item_data['unique_key'] = uniqid('car_', true);
        }
        return $cart_item_data;
    }

    public function restore_cart_item_from_session($cart_item, $values) {
        if (isset($values['car_id'])) {
            $cart_item['car_id']    = $values['car_id'];
            $cart_item['car_title'] = $values['car_title'] ?? '';
            $cart_item['car_price'] = $values['car_price'] ?? 0;
            $cart_item['car_image'] = $values['car_image'] ?? '';
            $cart_item['unique_key'] = $values['unique_key'] ?? uniqid('car_', true);
        }
        return $cart_item;
    }

    public function show_car_image_and_title($name, $cart_item, $cart_item_key) {
        if (isset($cart_item['car_id'])) {
            $img = isset($cart_item['car_image']) ? '<img src="' . esc_url($cart_item['car_image']) . '" style="width:60px; height:auto; border-radius:6px; margin-right:8px; vertical-align:middle;" />' : '';
            $title = isset($cart_item['car_title']) ? esc_html($cart_item['car_title']) : $name;
            return '<div style="display:flex; align-items:center; gap:8px;">' . $img . '<div style="font-weight:600;">' . $title . '</div></div>';
        }
        return $name;
    }

    public function display_car_meta_in_cart($item_data, $cart_item) {
        if (!isset($cart_item['car_id'])) return $item_data;

        $fields = [
            'mileage'       => 'Mileage',
            'condition'     => 'Condition',
            'price'         => 'Price',
            'demand_price'  => 'Demand Price',
            'owner_name'    => 'Owner',
            'owner_phone'   => 'Owner Phone',
        ];

        foreach ($fields as $meta_key => $label) {
            $value = get_post_meta($cart_item['car_id'], $meta_key, true);
            if ($value) {
                // strip Rs if present
                $clean = preg_replace('/\\s*Rs\\.?\\s*/i', '', $value);
                $item_data[] = [
                    'key'   => $label,
                    'value' => esc_html($clean),
                ];
            }
        }

        return $item_data;
    }

    public function add_car_meta_to_order($item, $cart_item_key, $values, $order) {
        if (isset($values['car_id'])) {
            $car_id = $values['car_id'];
            $item->add_meta_data('Car ID', $car_id);
            $item->add_meta_data('Car Name', get_the_title($car_id));
            $item->add_meta_data('Price', get_post_meta($car_id, 'price', true));
            $item->add_meta_data('Condition', get_post_meta($car_id, 'condition', true));
            $item->add_meta_data('Owner', get_post_meta($car_id, 'owner_name', true));
        }
    }
}

<?php

namespace CarShowroom\Frontend;

use WC_Cart;

class FrontendRenderer {
    public function __construct() {
        add_filter('the_content', [$this, 'appendBookingButtonToCar']);
        add_action('init', [$this, 'handleBookingSubmission']);
        add_action('woocommerce_before_calculate_totals', [$this, 'customizeCartItemName']);
        add_filter('woocommerce_get_item_data', [$this, 'displayCarDetailsInCart'], 10, 2);
    }

    public function appendBookingButtonToCar($content) {
        if (!is_singular('car') || !in_the_loop()) {
            return $content;
        }

        global $post;

        $car_id = $post->ID;
        $car_title = get_the_title($car_id);
        $car_price = get_post_meta($car_id, 'price', true);
        $booking_url = esc_url( $_SERVER['REQUEST_URI'] );

        ob_start();
        ?>
        <style>
            .book-car-button {
                background-color: #0073aa;
                color: white;
                padding: 12px 24px;
                font-size: 16px;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                transition: background-color 0.3s ease;
                margin-top: 20px;
                display: inline-block;
            }
            .book-car-button:hover {
                background-color: #005177;
            }
        </style>

        <form method="post" action="<?php echo $booking_url; ?>" onsubmit="return confirmBooking();">
            <input type="hidden" name="book_this_car" value="1">
            <input type="hidden" name="car_id" value="<?php echo esc_attr($car_id); ?>">
            <button type="submit" class="book-car-button">🚗 Book This Car</button>
        </form>

        <script>
            function confirmBooking() {
                return confirm("Do you want to book this car?");
            }
        </script>
        <?php

        return $content . ob_get_clean();
    }

    public function handleBookingSubmission() {
        if (
            isset($_POST['book_this_car']) &&
            isset($_POST['car_id']) &&
            is_numeric($_POST['car_id'])
        ) {
            $car_id = intval($_POST['car_id']);
            $car_title = get_the_title($car_id);
            $car_price = get_post_meta($car_id, 'price', true);

            if (!WC()->cart) {
                wc_load_cart();
            }

            WC()->cart->add_to_cart(
                0, // Product ID = 0 for custom/virtual item
                1,
                0,
                [],
                [
                    'car_id'    => $car_id,
                    'car_title' => $car_title,
                    'car_price' => $car_price,
                    'unique_key' => md5($car_id . time()) // To avoid duplicates
                ]
            );

            wp_safe_redirect(wc_get_cart_url());
            exit;
        }
    }

    public function customizeCartItemName($cart) {
        if (is_admin() && !defined('DOING_AJAX')) return;

        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            if (isset($cart_item['car_id'])) {
                $car_title = get_the_title($cart_item['car_id']);
                $car_price = get_post_meta($cart_item['car_id'], 'price', true);
                $cart_item['data']->set_name("🚗 Booking: " . $car_title);
                $cart_item['data']->set_price($car_price); // Set dynamic price
            }
        }
    }

    public function displayCarDetailsInCart($item_data, $cart_item) {
        if (isset($cart_item['car_id'])) {
            $car_id = $cart_item['car_id'];
            $mileage = get_post_meta($car_id, 'mileage', true);
            $condition = get_post_meta($car_id, 'condition', true);
            $owner_name = get_post_meta($car_id, 'owner_name', true);
            $owner_phone = get_post_meta($car_id, 'owner_phone', true);

            $item_data[] = ['key' => 'Mileage', 'value' => $mileage];
            $item_data[] = ['key' => 'Condition', 'value' => $condition];
            $item_data[] = ['key' => 'Owner', 'value' => $owner_name];
            $item_data[] = ['key' => 'Phone', 'value' => $owner_phone];
        }

        return $item_data;
    }
}

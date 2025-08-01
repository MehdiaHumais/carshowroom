<?php
namespace CarShowroom\Frontend;

class FrontendRenderer {
    public function register() {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_filter('the_content', [$this, 'renderCarBookingButton']);
    }

    public function enqueueScripts() {
        $script_path = plugin_dir_path(__FILE__) . 'book-car.js';
        $script_url = plugin_dir_url(__FILE__) . 'book-car.js';

        if (file_exists($script_path)) {
            wp_enqueue_script(
                'car-booking-script',
                $script_url,
                ['jquery'],
                time(),
                true
            );

            wp_localize_script('car-booking-script', 'carBookingData', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('book_car_nonce'),
            ]);



            // Minimal cart styling (can be moved to a CSS file)
            add_action('wp_head', function () {
                if (!is_cart() && !is_checkout()) return;
                echo '<style>
                    .woocommerce-cart .cart_item { font-size:14px; }
                    .woocommerce-cart .product-name img { width:70px; height:auto; border-radius:6px; margin-right:10px; vertical-align:middle; }
                    .woocommerce-cart .product-name { display:flex; align-items:center; gap:8px; }
                    .woocommerce-cart .woocommerce-cart-form__cart-item { padding:10px 0; border-bottom:1px solid #e5e5e5; }
                    .woocommerce-cart .item-meta { margin-top:6px; font-size:12px; color:#555; }
                </style>';
            });
        } else {
            error_log('book-car.js not found at: ' . $script_path);
        }
    }

    public function renderCarBookingButton($content) {
        if (!is_singular('car')) return $content;

        ob_start();
        ?>
        <button
            class="book-this-car-btn"
            data-car-id="<?php echo esc_attr(get_the_ID()); ?>"
            style="padding:10px 18px; background:#1d4ed8; color:#fff; border:none; border-radius:6px; cursor:pointer; margin-top:16px;"
        >
            🚗 Book This Car
        </button>
        <?php
        return $content . ob_get_clean();
    }
}

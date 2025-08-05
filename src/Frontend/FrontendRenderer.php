<?php

namespace CarShowroom\Frontend;

if (!defined('ABSPATH')) {
    exit;
}

class FrontendRenderer {
    public function register() {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_filter('the_content', [$this, 'renderCarBookingButton']);
    }

    public function enqueueScripts() {
        $handle = 'car-booking-script';
        $script_path = plugin_dir_path(__FILE__) . 'book-car.js';
        $script_url = plugin_dir_url(__FILE__) . 'book-car.js';

        if (file_exists($script_path)) {
            wp_enqueue_script($handle, $script_url, ['jquery'], null, true);
           wp_localize_script('car-booking-script', 'carBookingData', [
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('book_car_nonce'),
]);
        } else {
            error_log('[CarShowroom] Missing book-car.js at ' . $script_path);
        }

        // Simple inline styles for the button
        wp_add_inline_style('wp-block-library', '
            .book-this-car-btn {
                background: #1f6feb;
                color: #fff;
                padding: 10px 18px;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                transition: background .2s ease;
            }
            .book-this-car-btn:hover {
                background: #155bb5;
            }
        ');
    }

    public function renderCarBookingButton($content) {
        if (!is_singular('car')) {
            return $content;
        }

        $car_id = get_the_ID();
        ob_start();
        ?>
        <div style="margin:20px 0;">
            <button class="book-this-car-btn" data-car-id="<?php echo esc_attr($car_id); ?>">
                🚗 Book This Car
            </button>
        </div>
        <?php
        return $content . ob_get_clean();
    }
}

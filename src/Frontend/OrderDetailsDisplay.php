<?php

namespace CarShowroom\Frontend;

if (!defined('ABSPATH')) {
    exit;
}

class OrderDetailsDisplay {
    public function register() {
        // Admin: add a meta box on order edit screen
        add_action('add_meta_boxes', [$this, 'add_admin_metabox']);

        // Frontend: show under order details (My Account / Thank you)
        add_action('woocommerce_order_details_after_order_table', [$this, 'output_frontend_order_details'], 10, 1);
    }

    public function add_admin_metabox() {
        add_meta_box(
            'car_order_details_meta_box',
            __('Car Booking Details', 'car-showroom'),
            [$this, 'render_admin_metabox'],
            'shop_order',
            'side',
            'default'
        );
    }

    /**
     * Render meta box on admin order edit screen
     */
    public function render_admin_metabox($post) {
        $order = wc_get_order($post->ID);
        if (!$order) {
            echo '<p>' . esc_html__('Order not found', 'car-showroom') . '</p>';
            return;
        }

        echo '<div style="font-size:13px;">';
        $this->render_car_items($order);
        echo '</div>';
    }

    /**
     * Hook for frontend order view
     */
    public function output_frontend_order_details($order) {
        if (!is_a($order, 'WC_Order')) {
            return;
        }

        echo '<h2 style="margin-top:1.5em;">' . esc_html__('Car Booking Details', 'car-showroom') . '</h2>';
        echo '<div class="car-order-details-wrapper" style="font-size:14px; border:1px solid #e5e5e5; padding:12px; border-radius:6px;">';
        $this->render_car_items($order);
        echo '</div>';
    }

    /**
     * Shared rendering logic for an order's car items
     */
    private function render_car_items($order) {
        $items = $order->get_items();
        $found = false;

        foreach ($items as $item_id => $item) {
            // Look for the car-related metadata
            $car_id = $item->get_meta('Car ID');
            $car_name = $item->get_meta('Car Name') ?: $item->get_name();
            $price = $item->get_meta('Price') ?: wc_price($item->get_total());
            $condition = $item->get_meta('Condition');
            $owner = $item->get_meta('Owner') ?: $item->get_meta('Owner Name');
            $mileage = $item->get_meta('Mileage');
            $demand_price = $item->get_meta('Demand Price');
            $car_image = $item->get_meta('Car Image');

            if (!$car_id && !$car_name) {
                continue; // not a car line
            }

            $found = true;

            echo '<div class="single-car-booking" style="margin-bottom:16px; display:flex; gap:12px; border-bottom:1px solid #ddd; padding-bottom:12px;">';

            // Thumbnail
            if ($car_image) {
                echo '<div class="car-thumb" style="flex:0 0 80px;">';
                echo '<img src="' . esc_url($car_image) . '" width="80" style="border-radius:4px; display:block;" alt="' . esc_attr($car_name) . '">';
                echo '</div>';
            }

            echo '<div class="car-info" style="flex:1;">';
            echo '<div style="font-weight:600; font-size:16px; margin-bottom:4px;">' . esc_html($car_name) . '</div>';
            echo '<div style="margin-bottom:3px;"><strong>' . esc_html__('Price:', 'car-showroom') . '</strong> ' . esc_html($price) . '</div>';
            if ($mileage) {
                echo '<div style="margin-bottom:3px;"><strong>' . esc_html__('Mileage:', 'car-showroom') . '</strong> ' . esc_html($mileage) . '</div>';
            }
            if ($condition) {
                echo '<div style="margin-bottom:3px;"><strong>' . esc_html__('Condition:', 'car-showroom') . '</strong> ' . esc_html($condition) . '</div>';
            }
            if ($demand_price) {
                echo '<div style="margin-bottom:3px;"><strong>' . esc_html__('Demand Price:', 'car-showroom') . '</strong> ' . esc_html($demand_price) . '</div>';
            }
            if ($owner) {
                echo '<div style="margin-bottom:3px;"><strong>' . esc_html__('Owner:', 'car-showroom') . '</strong> ' . esc_html($owner) . '</div>';
            }
            if ($car_id) {
                echo '<div style="margin-bottom:3px;"><strong>' . esc_html__('Car ID:', 'car-showroom') . '</strong> ' . esc_html($car_id) . '</div>';
            }
            echo '</div>'; // .car-info

            echo '</div>'; // .single-car-booking
        }

        if (!$found) {
            echo '<div>' . esc_html__('No car booking data found for this order.', 'car-showroom') . '</div>';
        }
    }
}

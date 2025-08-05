<?php

namespace CarShowroom\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class MetaBox {
    public function register() {
        add_action('add_meta_boxes', [$this, 'add_car_meta_box']);
        add_action('save_post', [$this, 'save_meta']);
    }

    public function add_car_meta_box() {
        add_meta_box(
            'car_details',
            'Car Details',
            [$this, 'render_meta_box'],
            'car',
            'normal',
            'high'
        );
    }

    public function render_meta_box($post) {
        // Security nonce
        wp_nonce_field('car_details_nonce_action', 'car_details_nonce');

        $fields = [
            'mileage'       => 'Mileage',
            'condition'     => 'Condition',
            'price'         => 'Price',
            'demand_price'  => 'Demand Price',
            'owner_name'    => 'Owner Name',
            'owner_phone'   => 'Owner Phone',
        ];

        echo '<div style="display:flex; flex-direction:column; gap:10px;">';
        foreach ($fields as $key => $label) {
            $value = get_post_meta($post->ID, $key, true);
            echo '<p>';
            echo '<label for="' . esc_attr($key) . '"><strong>' . esc_html($label) . ':</strong></label><br />';
            echo '<input type="text" style="width:100%; padding:6px;" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
            echo '</p>';
        }
        echo '</div>';
    }

    public function save_meta($post_id) {
        // Autosave, revisions, permission checks
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!isset($_POST['car_details_nonce'])) return;
        if (!wp_verify_nonce($_POST['car_details_nonce'], 'car_details_nonce_action')) return;
        if (!current_user_can('edit_post', $post_id)) return;

        $fields = [
            'mileage',
            'condition',
            'price',
            'demand_price',
            'owner_name',
            'owner_phone',
        ];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $sanitized = sanitize_text_field(wp_unslash($_POST[$field]));
                update_post_meta($post_id, $field, $sanitized);
            }
        }
    }
}

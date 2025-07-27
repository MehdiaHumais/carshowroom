<?php

namespace CarShowroom\Admin;

class MetaBox {
    public function register() {
        add_action('add_meta_boxes', function () {
            add_meta_box('car_details', 'Car Details', [$this, 'render_meta_box'], 'car');
        });

        add_action('save_post', [$this, 'save_meta']);
    }

    public function render_meta_box($post) {
        $fields = ['mileage', 'condition', 'price', 'demand_price', 'owner_name', 'owner_phone'];
        foreach ($fields as $field) {
            $value = get_post_meta($post->ID, $field, true);
            echo "<p><label>" . ucfirst(str_replace('_', ' ', $field)) . ": </label>";
            echo "<input name='$field' value='$value' style='width:100%'></p>";
        }
    }

    public function save_meta($post_id) {
        foreach (['mileage', 'condition', 'price', 'demand_price', 'owner_name', 'owner_phone'] as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
}

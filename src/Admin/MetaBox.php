<?php

namespace CarShowroom\Admin;

class MetaBox {
<<<<<<< HEAD
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_custom_metabox']);
        add_action('save_post', [$this, 'save_custom_fields']);
    }

    public function add_custom_metabox() {
        add_meta_box(
            'car_details',
            'Car Details',
            [$this, 'render_metabox'],
            'car',
            'normal',
            'default'
        );
    }

    public function render_metabox($post) {
        $fields = [
            'mileage' => 'Mileage',
            'condition' => 'Condition',
            'price' => 'Price',
            'demand_price' => 'Demand Price',
            'owner_name' => 'Owner Name',
            'owner_phone' => 'Owner Phone',
        ];

        foreach ($fields as $key => $label) {
            $value = get_post_meta($post->ID, $key, true);
            echo "<p><label for='{$key}'>{$label}</label><br />";
            echo "<input type='text' id='{$key}' name='{$key}' value='" . esc_attr($value) . "' style='width:100%;' /></p>";
        }
    }

    public function save_custom_fields($post_id) {
        $keys = ['mileage', 'condition', 'price', 'demand_price', 'owner_name', 'owner_phone'];

        foreach ($keys as $key) {
            if (isset($_POST[$key])) {
                update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
=======
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
>>>>>>> 8a47fa6 (push of car showroom error)
            }
        }
    }
}

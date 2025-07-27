<?php
class Car_Showroom_Meta {
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_car_meta_boxes']);
        add_action('save_post', [$this, 'save_car_meta']);
    }

    public function add_car_meta_boxes() {
        add_meta_box('car_details', 'Car Details', [$this, 'render_car_fields'], 'car', 'normal', 'default');
    }

    public function render_car_fields($post) {
        $fields = ['mileage', 'condition', 'price', 'demand_price', 'owner_name', 'owner_phone'];
        foreach ($fields as $field) {
            $$field = esc_attr(get_post_meta($post->ID, $field, true));
        }

        wp_nonce_field('save_car_meta', 'car_meta_nonce');

        echo '<p><label>Current will get: <input type="text" name="mileage" value="' . $mileage . '" class="widefat"/></label></p>';
        echo '<p><label>Condition: <input type="text" name="condition" value="' . $condition . '" class="widefat"/></label></p>';
        echo '<p><label>Price: <input type="text" name="price" value="' . $price . '" class="widefat"/></label></p>';
        echo '<p><label>Demand Price: <input type="text" name="demand_price" value="' . $demand_price . '" class="widefat"/></label></p>';
        echo '<p><label>Owner Name: <input type="text" name="owner_name" value="' . $owner_name . '" class="widefat"/></label></p>';
        echo '<p><label>Owner Phone: <input type="text" name="owner_phone" value="' . $owner_phone . '" class="widefat"/></label></p>';
    }

    public function save_car_meta($post_id) {
        if (!isset($_POST['car_meta_nonce']) || !wp_verify_nonce($_POST['car_meta_nonce'], 'save_car_meta')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        $fields = ['mileage', 'condition', 'price', 'demand_price', 'owner_name', 'owner_phone'];
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
}

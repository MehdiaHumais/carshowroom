<?php
namespace CarShowroom\CPT;

class CarPostType {
    public static function register() {
        add_action('init', [self::class, 'register_post_type']);
    }

    public static function register_post_type() {
        register_post_type('car', [
            'label' => 'Cars',
            'public' => true,
            'show_in_rest' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'menu_icon' => 'dashicons-car',
        ]);
    }
}

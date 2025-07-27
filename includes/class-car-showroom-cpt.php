<?php
class Register_Car_CPT {
    public function __construct() {
        add_action('init', [$this, 'register_car_cpt']);
    }

    public function register_car_cpt() {
        register_post_type('car', [
            'labels' => [
                'name' => 'Cars',
                'singular_name' => 'Car',
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'cars'],
            'supports' => ['title', 'editor', 'thumbnail'],
            'show_in_rest' => true,
        ]);
    }
}

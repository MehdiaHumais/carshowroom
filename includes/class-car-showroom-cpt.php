<?php
<<<<<<< HEAD

namespace CarShowroom\Admin;

class CPT {
    public function __construct() {
        add_action('init', [$this, 'register_car_post_type']);
    }

    public function register_post_type('car', [
    'label' => 'Cars',
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_rest' => true,
    'supports' => ['title', 'editor', 'thumbnail'],
    'has_archive' => true,
    'rewrite' => ['slug' => 'car'],
    'menu_icon' => 'dashicons-car',
    'menu_position' => 5,
    ]);


        $args = [
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'cars'],
            'menu_icon' => 'dashicons-car',
            'supports' => ['title', 'editor', 'thumbnail'],
            'show_in_rest' => true,
        ];

        register_post_type('car', $args);
=======
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
>>>>>>> 8a47fa6 (push of car showroom error)
    }
}

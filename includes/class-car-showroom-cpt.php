<?php

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
    }
}

<?php

namespace CarShowroom\Admin;

if (!class_exists('CarShowroom\Admin\CPT')) {
    class CPT {
        public function __construct() {
            add_action('init', [$this, 'register_car_post_type']);
        }

        public function register_car_post_type() {
            $labels = [
                'name' => 'Cars',
                'singular_name' => 'Car',
                'add_new' => 'Add New Car',
                'add_new_item' => 'Add New Car',
                'edit_item' => 'Edit Car',
                'new_item' => 'New Car',
                'view_item' => 'View Car',
                'search_items' => 'Search Cars',
                'not_found' => 'No Cars found',
                'not_found_in_trash' => 'No Cars found in Trash',
                'menu_name' => 'Cars',
            ];

            $args = [
                'labels' => $labels,
                'public' => true,
                'has_archive' => true,
                'rewrite' => ['slug' => 'cars'],
                'show_in_rest' => true,
                'supports' => ['title', 'editor', 'thumbnail'],
                'menu_icon' => 'dashicons-car',
            ];

            register_post_type('car', $args);
        }
    }
}

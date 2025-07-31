<?php

namespace CarShowroom\CPT;

class CarPostType {
    public function register() {
        add_action('init', [$this, 'register_car_post_type']);
    }

    public function register_car_post_type() {
        register_post_type('car', [
            'labels' => [
                'name' => 'Cars',
                'singular_name' => 'Car',
                'add_new' => 'Add New Car',
                'add_new_item' => 'Add New Car',
                'edit_item' => 'Edit Car',
                'new_item' => 'New Car',
                'view_item' => 'View Car',
                'search_items' => 'Search Cars',
                'not_found' => 'No cars found',
                'not_found_in_trash' => 'No cars found in Trash',
                'all_items' => 'All Cars',
                'menu_name' => 'Cars',
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'cars'],
            'menu_icon' => 'dashicons-car',
            'supports' => ['title', 'editor', 'thumbnail'],
            'show_in_rest' => true,
        ]);
    }
}

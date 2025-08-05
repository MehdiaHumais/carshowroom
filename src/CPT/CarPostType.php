<?php
namespace CarShowroom\CPT;

if (!defined('ABSPATH')) {
    exit;
}

class CarPostType {
    public function register() {
        // Delay actual registration to 'init'
        add_action('init', [$this, 'do_register']);
    }

    public function do_register() {
        $labels = [
            'name'                  => __('Cars', 'car-showroom'),
            'singular_name'         => __('Car', 'car-showroom'),
            'menu_name'             => __('Cars', 'car-showroom'),
            'name_admin_bar'        => __('Car', 'car-showroom'),
            'add_new'               => __('Add New', 'car-showroom'),
            'add_new_item'          => __('Add New Car', 'car-showroom'),
            'new_item'              => __('New Car', 'car-showroom'),
            'edit_item'             => __('Edit Car', 'car-showroom'),
            'view_item'             => __('View Car', 'car-showroom'),
            'all_items'             => __('All Cars', 'car-showroom'),
            'search_items'          => __('Search Cars', 'car-showroom'),
            'not_found'             => __('No cars found', 'car-showroom'),
            'not_found_in_trash'    => __('No cars found in Trash', 'car-showroom'),
        ];

        register_post_type('car', [
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => true,
            'show_in_rest'       => true,
            'rewrite'            => ['slug' => 'cars'],
            'supports'           => ['title', 'editor', 'thumbnail'],
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-car',
            'capability_type'    => 'post',
            'show_in_admin_bar'  => true,
            'show_in_nav_menus'  => true,
        ]);
    }
}

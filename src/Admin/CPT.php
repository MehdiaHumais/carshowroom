<?php

namespace CarShowroom\Admin;

class CPT {
    public function register() {
        add_action('init', function () {
            register_post_type('car', [
                'label' => 'Cars',
                'public' => true,
                'menu_icon' => 'dashicons-car',
                'supports' => ['title', 'editor', 'thumbnail'],
                'has_archive' => true,
                'rewrite' => ['slug' => 'cars'],
                'show_in_rest' => true,
            ]);
        });
    }
}

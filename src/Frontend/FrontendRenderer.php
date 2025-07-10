<?php

namespace CarShowroom\Frontend;

class FrontendRenderer {
    public function __construct() {
        add_filter('template_include', [$this, 'load_custom_templates']);
    }

    public function load_custom_templates($template) {
    
        if (is_post_type_archive('car')) {
            $custom_template = plugin_dir_path(__DIR__) . '/../templates/archive-car.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }


        if (is_singular('car')) {
            $custom_template = plugin_dir_path(__DIR__) . '/../templates/single-car.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }

        return $template;
    }
}

<?php
class Car_Showroom_Frontend {
    public function __construct() {
        add_filter('template_include', [$this, 'custom_templates']);
    }

    public function custom_templates($template) {
        if (is_post_type_archive('car')) {
            return plugin_dir_path(__DIR__) . 'templates/archive-car.php';
        }
        if (is_singular('car')) {
            return plugin_dir_path(__DIR__) . 'templates/single-car.php';
        }
        return $template;
    }
}

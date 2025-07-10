<?php
/**
 * Plugin Name: Car Showroom Plugin
 * Description: Manage and display cars with REST API.
 * Version: 1.0
 * Author: Mehdia Humais
 */

if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/vendor/autoload.php';

use CarShowroom\Init;

add_action('after_setup_theme', function () {
    add_theme_support('post-thumbnails');
});

if (class_exists('CarShowroom\Init')) {
    Init::register();
}

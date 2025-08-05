<?php
/**
 * Plugin Name: Car Showroom Plugin
 * Description: Manage and display cars with REST API and WooCommerce integration.
 * Version: 1.2
 * Author: Mehdia Humais
 */

if (!defined('ABSPATH')) {
    exit;
}

// Autoload (composer)
$autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    add_action('admin_notices', function () {
        echo '<div class="notice notice-error"><p>Composer autoloader missing. Run <code>composer install</code>.</p></div>';
    });
}

// Init registration
add_action('plugins_loaded', function () {
    if (class_exists(\CarShowroom\Init::class)) {
        \CarShowroom\Init::register();
    }
});

// Flush rewrite rules on activation / deactivation to avoid 404s
register_activation_hook(__FILE__, function () {
    if (class_exists(\CarShowroom\CPT\CarPostType::class)) {
        // Ensure registration runs so rewrite rules exist
        (new \CarShowroom\CPT\CarPostType())->register();
        // Trigger the init hook manually before flushing (since activation runs before normal init)
        do_action('init');
    }
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules();
});

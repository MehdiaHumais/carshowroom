<?php
/**
 * Plugin Name: Car Showroom Plugin
 * Description: Showroom management with WooCommerce booking.
 * Version: 1.0
 * Author: Mehdia Humais
 */

require_once __DIR__ . '/vendor/autoload.php';

use CarShowroom\Init;

add_action('plugins_loaded', function () {
    Init::register();
});
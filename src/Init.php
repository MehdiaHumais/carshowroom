<?php

namespace CarShowroom;

use CarShowroom\CPT\CarPostType;
use CarShowroom\Admin\MetaBox;
use CarShowroom\Api\CarApi;
use CarShowroom\Frontend\FrontendRenderer;
use CarShowroom\Frontend\BookingHandler;
use CarShowroom\Frontend\OrderDetailsDisplay;

if (!defined('ABSPATH')) {
    exit;
}

class Init {
    public static function register() {
        // Register the custom post type (hooked into init internally)
        (new CarPostType())->register();

        // Register meta box
        (new MetaBox())->register();

        // REST API
        (new CarApi())->register();

        // Frontend rendering (button + script)
        (new FrontendRenderer())->register();

        // Booking / cart / order handling
        (new BookingHandler())->register();

        // Show car details on order view
        (new OrderDetailsDisplay())->register();
    }
}

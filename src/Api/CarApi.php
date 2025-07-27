<?php
namespace CarShowroom\Api;

class CarApi {
    public static function register_routes() {
        add_action('rest_api_init', function () {
            register_rest_route('wprobo/v1', '/get-cars', [
                'methods' => 'GET',
                'callback' => [self::class, 'get_cars'],
                'permission_callback' => '__return_true',
            ]);
        });
    }

    public static function get_cars($data) {
        return ['cars' => []];
    }
}

<?php
<<<<<<< HEAD

namespace CarShowroom\Api;

if (!class_exists('CarShowroom\Api\CarApi')) {
    class CarApi {
        public function __construct() {
            add_action('rest_api_init', [$this, 'register_routes']);
        }

        public function register_routes() {
            register_rest_route('wprobo/v1', '/get-cars', [
                'methods' => 'GET',
                'callback' => [$this, 'get_cars'],
                'permission_callback' => '__return_true',
            ]);
        }

        public function get_cars() {
            $cars = get_posts([
                'post_type' => 'car',
                'post_status' => 'publish',
                'numberposts' => -1,
            ]);

            $data = [];

            foreach ($cars as $car) {
                $data[] = [
                    'id' => $car->ID,
                    'title' => $car->post_title,
                    'link' => get_permalink($car->ID),
                ];
            }

            return $data;
        }
=======
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
>>>>>>> 8a47fa6 (push of car showroom error)
    }
}

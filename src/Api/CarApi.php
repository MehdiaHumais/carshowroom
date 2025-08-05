<?php

namespace CarShowroom\Api;

if (!defined('ABSPATH')) {
    exit;
}

class CarApi {
    public function register() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        register_rest_route('wprobo/v1', '/get-cars', [
            'methods' => 'GET',
            'callback' => [$this, 'get_cars'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('wprobo/v1', '/add-car', [
            'methods' => 'POST',
            'callback' => [$this, 'add_car'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },
            'args' => $this->get_car_args_definition(),
        ]);

        register_rest_route('wprobo/v1', '/update-car', [
            'methods' => 'POST',
            'callback' => [$this, 'update_car'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },
            'args' => $this->get_car_args_definition(true),
        ]);

        register_rest_route('wprobo/v1', '/delete-car', [
            'methods' => 'POST',
            'callback' => [$this, 'delete_car'],
            'permission_callback' => function () {
                return current_user_can('delete_posts');
            },
            'args' => [
                'car_id' => [
                    'required' => true,
                    'sanitize_callback' => 'absint',
                ],
            ],
        ]);

        register_rest_route('wprobo/v1', '/my-cars', [
            'methods' => 'GET',
            'callback' => [$this, 'my_cars'],
            'permission_callback' => '__return_true',
            'args' => [
                'user' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'email' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_email',
                ],
            ],
        ]);
    }

    private function get_car_args_definition($is_update = false) {
        $base = [
            'title' => [
                'required' => !$is_update,
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'content' => [
                'required' => false,
                'sanitize_callback' => 'sanitize_textarea_field',
            ],
            'mileage' => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'condition' => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'price' => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'demand_price' => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'owner_name' => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'owner_phone' => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ],
        ];

        if ($is_update) {
            $base['car_id'] = [
                'required' => true,
                'sanitize_callback' => 'absint',
            ];
        }

        return $base;
    }

    public function get_cars($request) {
        $posts = get_posts([
            'post_type' => 'car',
            'post_status' => 'publish',
            'numberposts' => -1,
        ]);

        $data = array_map([$this, 'prepare_car_response'], $posts);
        return rest_ensure_response($data);
    }

    public function add_car($request) {
        $params = $request->get_params();

        $current_user = wp_get_current_user();
        $post_arr = [
            'post_title'   => $params['title'] ?? '',
            'post_content' => $params['content'] ?? '',
            'post_type'    => 'car',
            'post_status'  => 'publish',
            'post_author'  => $current_user->ID ?: 1,
        ];

        $car_id = wp_insert_post($post_arr, true);
        if (is_wp_error($car_id)) {
            return rest_build_response(['error' => $car_id->get_error_message()], 500);
        }

        $this->update_car_meta($car_id, $params);
        return rest_ensure_response($this->prepare_car_response(get_post($car_id)));
    }

    public function update_car($request) {
        $params = $request->get_params();
        $car_id = intval($params['car_id']);
        $post = get_post($car_id);
        if (!$post || $post->post_type !== 'car') {
            return rest_build_response(['error' => 'Car not found'], 404);
        }

        $update = [
            'ID'           => $car_id,
            'post_title'   => $params['title'] ?? $post->post_title,
            'post_content' => $params['content'] ?? $post->post_content,
        ];
        wp_update_post($update);

        $this->update_car_meta($car_id, $params);
        return rest_ensure_response($this->prepare_car_response(get_post($car_id)));
    }

    public function delete_car($request) {
        $car_id = intval($request->get_param('car_id'));
        $post = get_post($car_id);
        if (!$post || $post->post_type !== 'car') {
            return rest_build_response(['error' => 'Car not found'], 404);
        }

        wp_trash_post($car_id);
        return rest_ensure_response(['deleted' => true, 'car_id' => $car_id]);
    }

    public function my_cars($request) {
        $user_arg = $request->get_param('user');
        $email_arg = $request->get_param('email');

        $user = null;
        if ($user_arg) {
            $user = get_user_by('login', $user_arg);
        }
        if (!$user && $email_arg) {
            $user = get_user_by('email', $email_arg);
        }
        if (!$user) {
            return rest_build_response(['error' => 'User not found'], 404);
        }

        $posts = get_posts([
            'post_type' => 'car',
            'post_status' => 'publish',
            'author' => $user->ID,
            'numberposts' => -1,
        ]);

        $data = array_map([$this, 'prepare_car_response'], $posts);
        return rest_ensure_response($data);
    }

    private function update_car_meta($car_id, $params) {
        $meta_fields = ['mileage', 'condition', 'price', 'demand_price', 'owner_name', 'owner_phone'];
        foreach ($meta_fields as $field) {
            if (isset($params[$field])) {
                update_post_meta($car_id, $field, sanitize_text_field($params[$field]));
            }
        }
    }

    private function prepare_car_response($post) {
        $car_id = $post->ID;
        $created = get_post_time('U', false, $post);
        $human_time = human_time_diff($created, current_time('timestamp')) . ' ago';

        return [
            'id'            => $car_id,
            'title'         => get_the_title($car_id),
            'content'       => get_the_content(null, false, $car_id),
            'link'          => get_permalink($car_id),
            'mileage'       => get_post_meta($car_id, 'mileage', true),
            'condition'     => get_post_meta($car_id, 'condition', true),
            'price'         => get_post_meta($car_id, 'price', true),
            'demand_price'  => get_post_meta($car_id, 'demand_price', true),
            'owner_name'    => get_post_meta($car_id, 'owner_name', true),
            'owner_phone'   => get_post_meta($car_id, 'owner_phone', true),
            'featured_image'=> get_the_post_thumbnail_url($car_id, 'medium'),
            'time_since'    => $human_time,
        ];
    }
}

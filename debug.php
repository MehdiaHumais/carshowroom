<?php

require_once __DIR__ . '/vendor/autoload.php';

use CarShowroom\Api\CarApi;

$api = new CarApi();
$api->register_routes();

print_r($api);

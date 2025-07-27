<?php
namespace CarShowroom;

use CarShowroom\Admin\MetaBox;
use CarShowroom\Admin\CarPostType;
use CarShowroom\Api\CarApi;
use CarShowroom\Frontend\FrontendRenderer;
use CarShowroom\Frontend\BookingHandler;

class Init {
    public static function register() {
       
        CarPostType::register();
        MetaBox::register();
        CarApi::register_routes();
        FrontendRenderer::init();
        BookingHandler::register();
    }
}

<?php
<<<<<<< HEAD

namespace CarShowroom;

use CarShowroom\Admin\CPT;
use CarShowroom\Admin\MetaBox;
use CarShowroom\Api\CarApi;
use CarShowroom\Frontend\FrontendRenderer;

class Init {
    public static function register() {
        new CPT();
        new MetaBox();
        new CarApi();
        new FrontendRenderer();
=======
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
>>>>>>> 8a47fa6 (push of car showroom error)
    }
}

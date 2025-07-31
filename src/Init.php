<?php
namespace CarShowroom;

use CarShowroom\Admin\MetaBox;
use CarShowroom\CPT\CarPostType;
use CarShowroom\Frontend\FrontendRenderer;
use CarShowroom\Frontend\BookingHandler;

class Init {
    public static function register() {
        (new CarPostType())->register();
        (new MetaBox())->register();
        (new FrontendRenderer())->register();
        (new BookingHandler())->register();
    }
}

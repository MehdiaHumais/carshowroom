<?php

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
    }
}

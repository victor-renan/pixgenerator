<?php

require_once 'vendor/autoload.php';

use VictorRenan\PixGenerator\PixGenerator;

$test = new PixGenerator(
    "alvesrenan990@gmail.com",
    100.00,
);

echo $test->code();
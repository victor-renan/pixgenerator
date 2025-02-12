<?php

require_once 'vendor/autoload.php';

use VictorRenan\Pixgen\Pixgen;

$test = new Pixgen(
    "alvesrenan990@gmail.com",
    100.00,
);

echo $test->code();
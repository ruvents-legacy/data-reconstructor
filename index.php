<?php

use Ruvents\DataReconstructor\DataReconstructor;
use Ruvents\DataReconstructor\Fixtures\TestClass;

error_reporting(E_ALL);
ini_set('display_errors', true);

require_once __DIR__.'/vendor/autoload.php';

$reconstructor = new DataReconstructor();
$result = $reconstructor->reconstruct(
    ['class2' => ['int' => 1], 'class1' => [['class2' => ['int' => 2]],['class2' => ['int' => 2]]]],
    TestClass::class
);

var_dump($result);

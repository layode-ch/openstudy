<?php

use Openstudy\Schemas\BaseSchema;
require_once __DIR__."/vendor/autoload.php";

error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 1);

$schema = new BaseSchema([
	"Name" => 1.3
]);

var_dump($schema);
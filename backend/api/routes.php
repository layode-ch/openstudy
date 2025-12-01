<?php
use OpenStudy\Controllers\OpenApiController;
use OpenStudy\Controllers\UserController;

use Slim\Routing\RouteCollectorProxy;

$app->get("/swagger", [OpenApiController::class, "swagger"]);

$app->group("/user" , function (RouteCollectorProxy $group) {
	$group->post("/login", [UserController::class, "login"]);
	$group->post("/sign-up", [UserController::class, "signUp"]);
});
<?php
use OpenStudy\Controllers AS OC;
use OpenStudy\Controllers\UserController;
use Slim\Routing\RouteCollectorProxy;

$app->get("/swagger", [OC\OpenApiController::class, "swagger"]);

$app->group("/user" , function (RouteCollectorProxy $group) {
	$group->get("/login", [UserController::class, "login"]);
	$group->get("/sign-up", [UserController::class, "signUp"]);
});
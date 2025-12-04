<?php
use OpenStudy\Controllers\OpenApiController;
use OpenStudy\Controllers\SetController;
use OpenStudy\Controllers\UserController;
use OpenStudy\Middlewares\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

$app->get("/swagger", [OpenApiController::class, "swagger"]);

$app->group("/user" , function (RouteCollectorProxy $group) {
	$group->post("/login", [UserController::class, "login"]);
	$group->post("/sign-up", [UserController::class, "signUp"]);
});

$app->group("/set" , function (RouteCollectorProxy $group) {
	$group->put("/create", [SetController::class, "create"])->add(new AuthMiddleware());
	$group->put("/{id}/add", [SetController::class, "add"])->add(new AuthMiddleware());
});
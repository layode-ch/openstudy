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
	$group->get("/auth", [UserController::class, "auth"])->add(new AuthMiddleware());
	$group->get("/{id}", [UserController::class, "getById"]);
});

$app->group("/set" , function (RouteCollectorProxy $group) {
	$group->put("/create", [SetController::class, "create"])->add(new AuthMiddleware());
	$group->put("/{id}/add", [SetController::class, "add"])->add(new AuthMiddleware());
	$group->get("/{id}/terms", [SetController::class, "getTerms"]);
	$group->get("/search", [SetController::class, "search"])->add(new AuthMiddleware());
	$group->post("/{id}", [SetController::class, "update"])->add(new AuthMiddleware());
	$group->get("/{id}", [SetController::class, "getById"]);
});

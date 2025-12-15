<?php
namespace OpenStudy\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Attributes as OA;
use OpenStudy\HTTPStatus;
use OpenStudy\Models\Set;
use OpenStudy\Models\User;
use OpenStudy\Schemas\Login;
use OpenStudy\Schemas\Message;
use OpenStudy\Schemas\SchemaException;
use OpenStudy\Schemas\SignUp;

#[OA\Tag("User")]
class UserController extends BaseController {

	/**
	 * Allows to login into a user account
	 */
	#[
		OA\Post("/user/login", tags: ["User"]),
		OA\RequestBody(
			required: true,
			content: new OA\MediaType(
				mediaType: 'application/json',
				schema: new OA\Schema(Login::class)
		)),
		OA\Response(response: 200, content: new OA\MediaType(
			mediaType: "application/json",
			schema: new OA\Schema(
				properties: [
					new OA\Property("token", type:"string")
				]
			)
		))
	]
	public static function login(Request $request, Response $response, array $args): Response {
		$schema = new Login(static::getBody($request));
		$user = User::selectByEmail($schema->email);
		if ($user === false)
			throw new SchemaException(["User not found"], HTTPStatus::NOT_FOUND);
		if (!password_verify($schema->password, $user->password))
			throw new SchemaException(["Wrong password"], HTTPStatus::FORBIDDEN);
		return static::updateResponse($response, ["token" => $user->token]);
	}  

	/**
	 * Allows to create a user account
	 */
	#[
		OA\Post("/user/sign-up", tags: ["User"]),
		OA\RequestBody(
			required: true,
			content: new OA\MediaType(
				mediaType: "application/json",
				schema: new OA\Schema(SignUp::class)
			)
		),
		OA\Response(response: 200, content: new OA\MediaType(
			mediaType: "application/json",
			schema: new OA\Schema(
				properties: [
					new OA\Property("token", type:"string")
				]
			)
		))
	]
	public static function signUp(Request $request, Response $response, array $args): Response {
		$schema = new SignUp(static::getBody($request));
		$user = User::selectByEmail($schema->email);
		if ($user !== false)
			throw new SchemaException(["Email already taken"], HTTPStatus::CONFLICT);
		$user = new User($schema);
		$user->insert();
		return static::updateResponse($response, ["token" => $user->token], HTTPStatus::CREATED);
	}

	/**
	 * Allows to check if the token is valid and getting the user's infos
	 */
	#[
		OA\Get("/user/auth", tags:["User"]),
		OA\Response(response: 200, content: new OA\MediaType(
			mediaType: "application/json",
			schema: new OA\Schema(User::class)
		))
	]
	public static function auth(Request $request, Response $response, array $args): Response {
		$userId = $request->getAttribute("user_id");
		return static::updateResponse($response, User::selectById($userId));
	}

	/**
	 * Allows to get all sets made by a user
	 */
	#[
		OA\Get("/user/sets", tags:["User"]),
		OA\Response(response: 200, content: new OA\MediaType(
			mediaType: "application/json",
			schema: new OA\Schema(
				properties: [
					new OA\Property("sets", type:"array", items: new OA\Items(Set::class))
				]
			)
		))
	]
	public static function terms(Request $request, Response $response, array $args): Response {
		$userId = (int)$request->getAttribute("user_id");
		$sets = Set::selectAllByUserId($userId);
		return static::updateResponse($response, ["sets" => $sets]);
	}

	/**
	 * Allows to get a user by their id
	 */
	#[
		OA\Get("/user/{id}", tags: ["User"], 
			parameters: [
				new OA\Parameter(name:"id", in: "path", required: true)
			]
		),
		OA\Response(response: HTTPStatus::CREATED->value, 
			content: new OA\MediaType("application/json",
				schema: new OA\Schema(User::class)
			) 
		)
	]
	public static function getById(Request $request, Response $response, array $args): Response {
		$id = (int)$args["id"];
		$user = User::selectById($id);
		if ($user === false)
			throw new SchemaException(["User not found"], HTTPStatus::NOT_FOUND);
		return static::updateResponse($response, $user);
	}
}
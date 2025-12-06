<?php
namespace OpenStudy\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Attributes as OA;
use OpenStudy\HTTPStatus;
use OpenStudy\Models\User;
use OpenStudy\Schemas\Login;
use OpenStudy\Schemas\Message;
use OpenStudy\Schemas\SchemaException;
use OpenStudy\Schemas\SignUp;

#[OA\Tag("User")]
class UserController extends BaseController {

	/**
	 * Allows to login into a user account
	 *
	 * @param Login $schema
	 * @return Response
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
			throw new SchemaException(["Wrong password", HTTPStatus::FORBIDDEN]);
		return static::updateResponse($response, ["token" => $user->token]);
	}  

	/**
	 * Allows to create a user account
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param array $args
	 * @return Response
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

	public static function auth(Request $request, Response $response, array $args): Response {
		$message = new Message("The token is valid");
		return static::updateResponse($response, $message);
	}
}
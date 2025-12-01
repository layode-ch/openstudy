<?php
namespace OpenStudy\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Attributes as OA;
use OpenStudy\HTTPStatus;
use OpenStudy\Models\User;
use OpenStudy\Schemas\Login;
use OpenStudy\Schemas\SchemaException;

class UserController extends BaseController {

	/**
	 * Allows to login into a user account
	 *
	 * @param Login $schema
	 * @return void
	 */
	#[
		OA\Get("/user/login"),
		OA\RequestBody(
			required: true,
			content: [new OA\MediaType(
				mediaType: 'application/json',
				schema: new OA\Schema(Login::class)
		)]),
		OA\Response(response: 200, content: new OA\MediaType(
			mediaType: "application/json",
			schema: new OA\Schema(
				properties: [
					new OA\Property("token", type:"string")
				]
			)
		))
	]
	public static function login (Request $request, Response $response, array $args) {
		$schema = new Login(static::getBody($request));
		$user = User::selectByEmail($schema->email);
		if ($user === false)
			throw new SchemaException(["User not found"], HTTPStatus::NOT_FOUND);
		if (!password_verify($schema->password, $user->password))
			throw new SchemaException(["Wrong password", HTTPStatus::FORBIDDEN]);
		return static::updateResponse($response, ["token" => $user->token]);
	}  
}
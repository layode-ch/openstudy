<?php
namespace OpenStudy\Middlewares;

use OpenStudy\HTTPStatus;
use OpenStudy\Models\User;
use OpenStudy\Schemas\SchemaException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware implements MiddlewareInterface {
	public function process(Request $request, RequestHandler $handler): Response {
		$infos = $this->verfifyToken();
		$request = $request->withAttribute("token", $infos["token"]);
		$request = $request->withAttribute("user_id", $infos["user_id"]);
		$response = $handler->handle($request);
        return $response;
	}
	/**
	 * Undocumented function
	 *
	 * @return array{token:string, user_id:int}
	 */
	protected static function verfifyToken(): array {
		$token = self::getToken();
		if (!$token)
			throw new SchemaException(["Invalid or missing Bearer token."], HTTPStatus::UNAUTHORIZED);
		$account = User::selectByToken($token);
		if (!$account)
			throw new SchemaException(["Invalid Bearer token."], HTTPStatus::UNAUTHORIZED);
		return ["token" => $token, "user_id" => $account->id];
	}

	protected static function getToken(): string|false {
		$headers = getallheaders();
		if (!array_key_exists('Authorization', $headers)) { 
			return false;
		}
		$bearer = explode(' ', $headers['Authorization']); 
		if ($bearer[0] === 'Bearer') {
			return $bearer[1];
		}
		return false;
	}
}
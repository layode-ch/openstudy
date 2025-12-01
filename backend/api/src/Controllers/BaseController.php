<?php 
namespace OpenStudy\Controllers;

use OpenStudy\HTTPStatus;
use OpenStudy\Models\Company;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Request;
use OpenStudy\Models\Developer;
use OpenStudy\Schemas\SchemaException;

abstract class BaseController {
	protected static function updateResponse(Response $response, $data, HTTPStatus $status = HTTPStatus::OK): Response {
		$body = $response->getBody();
		$body->write(json_encode($data));
		return $response->withHeader('Content-Type', 'application/json')->withStatus($status->value);
	}

	protected static function getBody(Request $request): array {
		$body = json_decode($request->getBody()->getContents(), true);
		if ($body === null)
			$body = [];
		return $body;
	}

	protected static function verfifyToken(): string {
		$token = self::getToken();
		if (!$token)
			throw new SchemaException(["Invalid or missing Bearer token."], HTTPStatus::UNAUTHORIZED);
		$account = Developer::selectByToken($token);
		if (!$account) {
			$account = Company::selectByToken($token);
			if (!$account)
				throw new SchemaException(["Invalid Bearer token."], HTTPStatus::UNAUTHORIZED);
		}
		return $token;
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
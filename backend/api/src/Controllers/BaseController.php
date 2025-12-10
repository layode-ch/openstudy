<?php 
namespace OpenStudy\Controllers;

use OpenStudy\HTTPStatus;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Request;

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
} 
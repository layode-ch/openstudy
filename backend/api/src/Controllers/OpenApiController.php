<?php
namespace OpenStudy\Controllers;

use OpenApi\Attributes as OA;
use OpenApi\Generator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

#[OA\Info(title: "OpenStudy API", version: "1"), OA\Server("/api")]
class OpenApiController extends BaseController {
	
	/**
	 * The swagger json generated at each request
	 *
	 * @param Request $request
	 * @param Response $response
	 * @return Response
	 */
	#[
		OA\Get("/swagger"),
		OA\Response(response: 200, 
			content: new OA\MediaType (
				"application/json"
			)
		)
	]
	public static function swagger(Request $request, Response $response) {
		$generator = new Generator();
		$openapi = $generator->generate([__DIR__."/.."]);
		return static::updateResponse($response, json_decode($openapi->toJson()));
	} 
}
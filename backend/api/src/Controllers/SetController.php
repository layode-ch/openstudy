<?php
namespace OpenStudy\Controllers;

use OpenApi\Attributes as OA;

use OpenStudy\HTTPStatus;
use OpenStudy\Models\Set;
use OpenStudy\Models\User;
use OpenStudy\Schemas\CreateSet;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Request;

#[OA\Tag("Set")]
class SetController extends BaseController {
	
	#[
		OA\Put("/set/create", tags: ["Set"]),
		OA\RequestBody( 
		required: true,
		content: new OA\MediaType(
			"application/json",
			new OA\Schema(CreateSet::class)
		)),
		OA\Response(response: 200,
			content: new OA\MediaType(
				"application/json",
				new OA\Schema(Set::class)
			)
		)
	]
	public static function create(Request $request, Response $response, array $args): Response {
		$schema = new CreateSet(static::getBody($request));
		$token = $request->getAttribute("token");
		$userId = User::selectByToken($token)->id;
		$set = new Set($schema);
		$set->userId = $userId;
		$id = $set->insert();
		$set = Set::selectById($id);
		return static::updateResponse($response, $set, HTTPStatus::CREATED);
	}
}
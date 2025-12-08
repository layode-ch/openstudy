<?php
namespace OpenStudy\Controllers;

use OpenApi\Attributes as OA;

use OpenStudy\HTTPStatus;
use OpenStudy\Models\Set;
use OpenStudy\Models\Term;
use OpenStudy\Models\User;
use OpenStudy\Schemas\AddTerms;
use OpenStudy\Schemas\CreateSet;
use OpenStudy\Schemas\Message;
use OpenStudy\Schemas\SchemaException;
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
		$userId = $request->getAttribute("user_id");
		$set = new Set($schema);
		$set->userId = $userId;
		$id = $set->insert();
		$set = Set::selectById($id);
		return static::updateResponse($response, $set, HTTPStatus::CREATED);
	}

	#[
		OA\Put("/set/{id}/add", tags: ["Set"], 
			parameters: [
				new OA\Parameter(name:"id", in: "path", required: true)
			]
		),
		OA\RequestBody(required: true, content: new OA\MediaType(
			"application/json",
			schema: new OA\Schema(AddTerms::class)
		)),
		OA\Response(response: HTTPStatus::CREATED->value, 
			content: new OA\MediaType("application/json",
				schema: new OA\Schema(Message::class)
			) 
		)
	]
	public static function add(Request $request, Response $response, array $args): Response {
		$userId = (int)$request->getAttribute("user_id");
		$setId = (int)$args["id"];
		$set = Set::selectById($setId);

		if ($set === false)
			static::notFound();
		if ($set->userId !== $userId)
			static::forbidden();

		$schema = new AddTerms(static::getBody($request));
		foreach ($schema->terms as $term) {
			$term->setId = $setId;
			$term->insert();
		}
		return static::updateResponse($response, new Message("Terms added successfully"), HTTPStatus::CREATED);
	}
	
	#[
		OA\Get("/set/search", tags: ["Set"]),
		OA\Response(response:200, content: new OA\JsonContent(
			type: "array",
			items: new OA\Items(Set::class)
		))
	]
	public static function search(Request $request, Response $response, array $args): Response {
		$sets = Set::searchAll();
		return static::updateResponse($response, $sets);
	}

	private static function notFound() {
		throw new SchemaException(["Set not found"], HTTPStatus::NOT_FOUND);
	}

	private static function forbidden() {
		throw new SchemaException(["You don't have the rights to do that"], HTTPStatus::FORBIDDEN);
	}
}
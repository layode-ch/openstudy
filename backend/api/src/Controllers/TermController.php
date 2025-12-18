<?php
namespace OpenStudy\Controllers;

use OpenApi\Attributes as OA;

use OpenStudy\HTTPStatus;
use OpenStudy\Models\Term;
use OpenStudy\Schemas\CreateTerm;
use OpenStudy\Schemas\Message;
use OpenStudy\Schemas\SchemaException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Request;

#[OA\Tag("Term")]
class TermController extends BaseController {
	/** Allows to delete a term by its id */
	#[
		OA\Delete("/term/{id}", tags: ["Term"],
			parameters: [
				new OA\Parameter(name:"id", in: "path", required: true),
			]),
		OA\Response(response:200, content: new OA\MediaType("application/json",
			schema: new OA\Schema(Message::class)
		))
	]
	public static function delete(Request $request, Response $response, array $args): Response {
		$id = (int)$args["id"];
		Term::delete($id);
		return static::updateResponse($response, new Message("Term deleted"));
	}

	/** Allows to update a term by its id */
	#[
		OA\Post("/term/{id}", tags: ["Term"],
			parameters: [
				new OA\Parameter(name:"id", in: "path", required: true),
			]),
		OA\RequestBody(required: true, content: new OA\MediaType("application/json",
			schema: new OA\Schema(CreateTerm::class)
		)),
		OA\Response(response:200, content: new OA\MediaType("application/json",
			schema: new OA\Schema(Message::class)
		))
	]
	public static function update(Request $request, Response $response, array $args): Response {
		$id = (int)$args["id"];
		$schema = new CreateTerm(static::getBody($request));
		$term = new Term($schema);
		$term->id = $id;
		$term->update();
		return static::updateResponse($response, new Message("Term updated"));
	}
}
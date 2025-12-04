<?php
namespace OpenStudy\Schemas;

use OpenApi\Attributes as OA;
use OpenStudy\Attributes\Property;

#[OA\Schema]
class Message extends BaseSchema {
	#[OA\Property, Property]
	public string $message;

	public function __construct(string $message) {
		return parent::__construct(["message" => $message]);
	}
}
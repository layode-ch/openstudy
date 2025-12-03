<?php
namespace OpenStudy\Schemas;

use OpenStudy\Attributes\Validators as VA;
use OpenStudy\Attributes\Property;
use OpenApi\Attributes as OA;
#[OA\Schema(required:["name","description","user_id"])]
class CreateSet extends BaseSchema {
	#[OA\Property, Property, VA\Filter(FILTER_SANITIZE_FULL_SPECIAL_CHARS)]
	public string $name;
	#[OA\Property, Property, VA\Filter(FILTER_SANITIZE_FULL_SPECIAL_CHARS)]
	public string $description;
	#[OA\Property("user_id"), Property("user_id")]
	public int $userId;
}
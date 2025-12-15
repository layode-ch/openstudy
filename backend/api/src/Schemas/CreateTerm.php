<?php
namespace OpenStudy\Schemas;

use OpenApi\Attributes as OA;
use OpenStudy\Attributes\Validators as VA;
use OpenStudy\Attributes\Property;

#[OA\Schema]
class CreateTerm extends BaseSchema {
	#[OA\Property, Property, VA\Min(2), VA\Filter(FILTER_SANITIZE_FULL_SPECIAL_CHARS)]
	public string $original;
	#[OA\Property, Property, VA\Min(2), VA\Filter(FILTER_SANITIZE_FULL_SPECIAL_CHARS)]
	public string $definition;
}
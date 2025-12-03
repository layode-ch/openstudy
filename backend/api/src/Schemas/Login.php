<?php
namespace OpenStudy\Schemas;

use OpenStudy\Attributes\Validators as VA;
use OpenStudy\Attributes\Property;
use OpenApi\Attributes as OA;

#[OA\Schema(required: ["email", "password"])]
class Login extends BaseSchema {
	#[OA\Property, Property, VA\Max(50), VA\Min(5), VA\Filter(FILTER_VALIDATE_EMAIL)]
	public string $email;
	#[OA\Property, Property, VA\Max(100), VA\Min(5)]
	public string $password;
}
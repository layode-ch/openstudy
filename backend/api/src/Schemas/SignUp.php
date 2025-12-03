<?php
namespace OpenStudy\Schemas;

use OpenStudy\Attributes\Validators as VA;
use OpenStudy\Attributes\Property;
use OpenApi\Attributes as OA;

#[OA\Schema(required: ["email", "username", "password"])]
class SignUp extends BaseSchema {
	#[OA\Property, Property, VA\Filter(FILTER_VALIDATE_EMAIL)]
	public string $email;
	#[OA\Property, Property, VA\Filter(FILTER_SANITIZE_FULL_SPECIAL_CHARS)]
	public string $username;
	#[OA\Property, Property]
	public string $password;
}
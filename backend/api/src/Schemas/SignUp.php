<?php
namespace OpenStudy\Schemas;

use OpenStudy\Attributes\Proprety;
use OpenStudy\Attributes\Validators as VA;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\Property;

#[OA\Schema(required: ["email", "username", "password"])]
class SignUp extends BaseSchema {
	#[OA\Property, Property]
	public string $email;
	#[OA\Property, Property]
	public string $username;
	#[OA\Property, Property]
	public string $password;
}
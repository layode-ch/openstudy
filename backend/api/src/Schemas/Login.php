<?php
namespace OpenStudy\Schemas;

use OpenStudy\Attributes\Validators as VA;
use OpenStudy\Attributes\Proprety;
use OpenApi\Attributes as OA;

#[OA\Schema]
class Login extends BaseSchema {
	#[OA\Property, Proprety, VA\Max(50)]
	public string $email;
	#[OA\Property, Proprety, VA\Max(50)]
	public string $password;
}
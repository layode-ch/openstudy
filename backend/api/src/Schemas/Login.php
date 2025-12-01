<?php
namespace OpenStudy\Schemas;

use OpenStudy\Attributes\Validators as VA;
use OpenStudy\Attributes\Proprety;

class Login extends BaseSchema {
	#[Proprety, VA\Max(50)]
	public string $email;
	#[Proprety, VA\Max(50)]
	public string $password;
}
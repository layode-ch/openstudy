<?php
namespace Openstudy\Attributes\Validators;

abstract class Validator {
	public abstract function validate(string $name, mixed $value): bool;
}
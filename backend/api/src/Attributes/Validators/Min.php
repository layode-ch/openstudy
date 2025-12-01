<?php
namespace OpenStudy\Attributes\Validators;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Min extends Validator {
	private int $value;

	public function __construct(int $value) {
		$this->value = $value;
	}

	public function validate(string $name, mixed $value): bool {
		switch (gettype($value)) {
			case "string":
				if (strlen($value) < $this->value)
					throw new ValidatorException("The field '$name' can't be smaller than {$this->value}");
				break;
			case "array":
				if (count($value) < $this->value)
					throw new ValidatorException("The field '$name' can't have less items than {$this->value}");
				break;
			case "integer":
				if ($value < $this->value)
					throw new ValidatorException("The field '$name' can't be lower than {$this->value}");
				break;
			case "double":
				if ($value < $this->value)
					throw new ValidatorException("The field '$name' can't be lower than {$this->value}");
				break;
		}
		return true;
	}
}

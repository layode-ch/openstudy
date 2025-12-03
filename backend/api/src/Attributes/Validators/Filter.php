<?php
namespace OpenStudy\Attributes\Validators;

use Attribute;
use OpenStudy\Schemas\SchemaException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Filter extends Validator {
	public int $filter;
	public function __construct(int $filter) {
		$this->filter = $filter;
	}

	public function validate(string $name, mixed $value): ?bool {
		$result = filter_var($value, $this->filter);
		$message = "The field '$name' is supposed to be ";
		
		if ($result === false) {
			switch ($this->filter) {
				case FILTER_VALIDATE_EMAIL:
					throw new ValidatorException($message."an email");
				case FILTER_VALIDATE_URL:
					throw new ValidatorException($message."an URL");
				case FILTER_VALIDATE_IP:
					throw new ValidatorException($message."an IP address");
				case FILTER_VALIDATE_BOOL:
					throw new ValidatorException($message."a boolen");
				case FILTER_VALIDATE_BOOLEAN:
					throw new ValidatorException($message."a boolen");
				case FILTER_VALIDATE_DOMAIN:
					throw new ValidatorException($message."a domain name");
				case FILTER_VALIDATE_FLOAT:
					throw new ValidatorException($message."a float");
				case FILTER_VALIDATE_INT:
					throw new ValidatorException($message."an integer");
				case FILTER_VALIDATE_MAC:
					throw new ValidatorException($message."a mac address");
				case FILTER_VALIDATE_REGEXP:
					throw new ValidatorException($message."a regex");
				default:
					return null;
			}
		}
		return true;
	}
}
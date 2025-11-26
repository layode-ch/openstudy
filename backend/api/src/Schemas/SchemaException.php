<?php
namespace Openstudy\Schemas;
use Openstudy\HTTPStatus;

use Exception;
use JsonSerializable;

class SchemaException extends Exception implements JsonSerializable {

	public HTTPStatus $httpStatus { get => HTTPStatus::from($this->code); }
	public function __construct(array $errors, HTTPStatus | int $httpStatus = HTTPStatus::BAD_REQUEST) {
		if ($httpStatus instanceof HTTPStatus)
			$httpStatus = $httpStatus->value;
		parent::__construct(json_encode($errors), $httpStatus);
	}
	
	public function jsonSerialize(): string {
		$msg = $this->getMessage();
		return "$msg";
	}

}
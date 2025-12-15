<?php
namespace OpenStudy\Schemas;
use OpenStudy\HTTPStatus;

use Exception;
use JsonSerializable;

class SchemaException extends Exception implements JsonSerializable {

	public HTTPStatus $httpStatus {
		get {
			return $this->getHttpStatus();
		}
	}

	public array $errors {
		get {
			return json_decode($this->getMessage(), true);
		}
	}

	public function __construct(array $errors, HTTPStatus | int $httpStatus = HTTPStatus::BAD_REQUEST) {
		if ($httpStatus instanceof HTTPStatus)
			$httpStatus = $httpStatus->value;
		parent::__construct(json_encode($errors), $httpStatus);
	}
	
	public function getHttpStatus(): HTTPStatus {
		return HTTPStatus::from($this->code);
	}

	public function jsonSerialize(): string {
		$msg = $this->getMessage();
		return "$msg";
	}

}
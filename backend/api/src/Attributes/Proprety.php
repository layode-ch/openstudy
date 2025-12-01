<?php
namespace OpenStudy\Attributes;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Proprety {
	public ?string $name;
	
	public function __construct(?string $name = null) {
		$this->name = $name;
	}
}
<?php 
namespace OpenStudy\Attributes\DB;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Block {
	const ALL = 0;
	const SELECT = 1;
	const UPDATE = 2;
	const INSERT = 3;

	public array $actions;

	public function __construct(array $actions = [Block::ALL]) {
		$this->actions = $actions;
	}
}
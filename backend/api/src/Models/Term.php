<?php
namespace OpenStudy\Models;

use OpenApi\Attributes as OA;
use OpenStudy\Attributes\DB as DB;
use OpenStudy\Schemas\CreateTerm;

#[OA\Schema]
class Term extends BaseModel {
	#[OA\Property, DB\Block([DB\Block::INSERT, DB\Block::UPDATE])]
	public int $id;
	#[OA\Property]
	public string $original;
	#[OA\Property]
	public string $definition;
	#[OA\Property("set_id"), DB\Column("set_id"), DB\Block([DB\Block::UPDATE])]
	public int $setId;

	public function __construct(?CreateTerm $data = null) {
		if ($data instanceof CreateTerm) {
			$this->original = $data->original;
			$this->definition = $data->definition;
		}
	}

	public function insert(): int {
		$sql = static::insertQuery();
		self::run($sql, [
			$this->original,
			$this->definition,
			$this->setId
		]);
		return self::getDB()->lastInsertId();
	}

	public static function selectById(int $id): Term {
		return static::selectBy("id", $id);
	}

}
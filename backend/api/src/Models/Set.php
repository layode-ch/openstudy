<?php
namespace OpenStudy\Models;

use OpenApi\Attributes as OA;
use OpenStudy\Attributes\DB as DB;
use OpenStudy\Schemas\CreateSet;

#[OA\Schema]
class Set extends BaseModel {
	#[OA\Property, DB\Block([DB\Block::INSERT, DB\Block::UPDATE])]
	public int $id;
	#[OA\Property]
	public string $name;
	#[OA\Property]
	public string $description;
	#[OA\Property("user_id"), DB\Column("user_id"), DB\Block([DB\Block::UPDATE])]
	public int $userId;

	public function __construct(?CreateSet $data = null) {
		if ($data instanceof CreateSet) {
			$this->name = $data->name;
			$this->description = $data->description;
		}
	}

	public function insert(): int {
		$sql = static::insertQuery();
		self::run($sql, [
			$this->name,
			$this->description,
			$this->userId
		]);
		return self::getDB()->lastInsertId();
	}

	public static function selectById(int $id): Set|false {
		return static::selectBy("id", $id);
	}

	/**
	 * Undocumented function
	 *
	 * @param integer $id
	 * @return array<Set>
	 */
	public static function selectAllByUserId(int $id): array {
		return static::search(exactMatchColumns:["user_id" => $id]);
	}

	public static function searchAll(): array {
		return parent::selectAll();
	}
}
<?php
namespace OpenStudy\Models;

use OpenStudy\Attributes\DB as DB;
use OpenStudy\Schemas\CreateSet;

class Set extends BaseModel {
	#[DB\Block([DB\Block::INSERT, DB\Block::UPDATE])]
	public int $id;
	public string $name;
	public string $description;
	#[DB\Column("user_id"), DB\Block([DB\Block::UPDATE])]
	public int $userId;

	public function __construct(?CreateSet $data = null) {
		if ($data instanceof CreateSet) {
			$this->name = $data->name;
			$this->description = $data->description;
			$this->userId = $data->userId;
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

	public function selectById(int $id): Set {
		return static::selectBy("id", $id);
	}

	/**
	 * Undocumented function
	 *
	 * @param integer $id
	 * @return array<User>
	 */
	public function selectAllByUserId(int $id): array {
		return static::search(exactMatchColumns:["user_id" => $id]);
	}
}
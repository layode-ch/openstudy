<?php
namespace OpenStudy\Models;

use OpenStudy\Attributes\DB as DB; 

class User extends BaseModel {
	#[DB\Block([DB\Block::INSERT])]
	public int $id;
	public string $email;
	public string $username;
	#[DB\Sensitive]
	public string $password;
	#[DB\Sensitive]
	public string $token;

	public static function generateToken() {
		$token = uniqid();
		while (static::selectByToken($token) !== false) {
			$token = uniqid();
		}
		return $token;
	}

	public static function selectById(int $id): User|false {
		return static::selectBy("id", $id);
	}

	public static function selectByEmail(string $email): User|false {
		return static::selectBy("email", $email);
	}

	public static function selectByToken(string $token): User|false {
		return static::selectBy("token", $token);
	}

	public function insert(): int {
		$sql = static::insertQuery();
		$db = self::getDB();
		static::run($sql, [
			$this->email,
			$this->username,
			$this->password,
			$this->token
		]);
		return $db->lastInsertId();
	}
}
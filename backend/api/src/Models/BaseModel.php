<?php
namespace OpenStudy\Models;

use OpenStudy\Attributes\DB as DB;
use JsonSerializable;
use PDO;
use ReflectionClass;

abstract class BaseModel implements JsonSerializable {

	const DB_NAME = "OpenStudy";
	const DB_USER = "root";
	const DB_PASSWORD = "Super";
	const DB_HOST = "db";
	const DB_CHARSET = "utf8mb4";

	#[DB\Block]
	private static ?PDO $pdo = null;

	protected static function getTable(): string {
		$ref = new ReflectionClass(static::class);
		$attrs = $ref->getAttributes(DB\Table::class);
		if (!empty($attrs))
			return $attrs[0]->newInstance()->name;
		$path = explode("\\", static::class);
		return array_pop($path);
	}

	protected static function sensitiveColumns(): array { 
		$sensitives = static::getPropertiesWithAttribute(DB\Sensitive::class);
		$columns = [];
		foreach ($sensitives as $sensitive) {
			$attrs = $sensitive->getAttributes(DB\Column::class);
			if (!empty($attrs)) {
				$columns[] = $attrs[0]->newInstance()->name;
			}
			else {
				$columns[] = $sensitive->getName();
			}
		}
		return $columns;
	}


	protected static function getDB() {
		if (self::$pdo === null) {
			$dsn = 'mysql:host=' . BaseModel::DB_HOST . ';dbname=' . BaseModel::DB_NAME . ';charset=' . BaseModel::DB_CHARSET;
			$options = [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => false,
			];
			self::$pdo = new PDO($dsn, BaseModel::DB_USER, BaseModel::DB_PASSWORD, $options);
		}
		return self::$pdo;
	}

	protected static function run(string $sql, array $params = []) {
		$pdo = self::getDB();
		$sttmt = $pdo->prepare($sql);
		$sttmt->execute($params);
		return $sttmt;
	}
	
	protected static function selectAll(): array {
		$sql = static::selectQuery();
		$sttmt = self::run($sql);
		return $sttmt->fetchAll(PDO::FETCH_CLASS, static::class);
	}

	protected static function search(array $likeColumns = [], array $exactMatchColumns = []): array {
		$conditions = static::searchCondition($likeColumns, $exactMatchColumns);
		$sql = static::selectQuery()." WHERE ".$conditions["sql"];
		$sttmt = self::run($sql, $conditions["params"]);
		return $sttmt->fetchAll(PDO::FETCH_CLASS, static::class);
	}

	protected static function selectBy(string $propriety, mixed $value): object | false {
		$sql = static::selectQuery()." WHERE $propriety = ?";
		$sttmt = self::run($sql, [$value]);
		return $sttmt->fetchObject(static::class);
	}

	public static function delete(int $id) {
		$sql = static::deleteQuery()." WHERE id = ?";
		self::run($sql, [$id]);	
	}

	
	/**
	 * Creates an array with the sql query and parameters to search in the database
	 *
	 * @param array $likeColumns
	 * @param array $exactMatchColumns
	 * @return array{
	 *     sql: string,
	 *     params: array<int, mixed>
	 * }
	 */
	protected static function searchCondition(array $likeColumns = [], array $exactMatchColumns = []): array {
		$conditions = [];
		$params = [];

		foreach ($likeColumns as $column => $value) {
			$conditions[] = "`$column` LIKE ?";
			$params[] = "%$value%";
		}

		foreach ($exactMatchColumns as $column => $value) {
			if ($value !== '') {
				$conditions[] = "`$column` = ?";
				$params[] = $value;
			}
		}

		return [
			'sql' => implode(" AND ", $conditions) ?: '1', // Use '1' to prevent invalid SQL if no filters
			'params' => $params
		];
	}

	/**
	 * Array with all table columns
	 *
	 * @return array<string>
	 */
	protected static function getColumns(): array {
		$reflectionClass = new ReflectionClass(static::class);
		$columns = [];
		foreach ($reflectionClass->getProperties() as $property) {
			$attrs = $property->getAttributes(DB\Block::class);
			if (!empty($attrs)) {
				$dbBlock = $attrs[0]->newInstance();
				if (in_array(DB\Block::ALL, $dbBlock->actions)) {
					continue;
				}
			}
			$attrs = $property->getAttributes(DB\Column::class);
			if (empty($attrs)) {
				$columns[] = $property->getName();
				continue;
			}
			$dbColumn = $attrs[0]->newInstance();
			$columns[] = $dbColumn->name;
		}
		return $columns;
	}

	private static function anyColumns(int $block): array {
		$columns = static::getProperties();
		$properties = static::getPropertiesWithAttribute(DB\Block::class);
		foreach ($properties as $property) {
			$name = $property->getName();
			if (!isset($columns[$name])) continue; // skip non-columns
			$attrs = $property->getAttributes(DB\Block::class);
			$dbBlock = $attrs[0]->newInstance();
			if (in_array($block, $dbBlock->actions)) {
				unset($columns[$name]);
			}
		}
		return $columns;
	}

	private static function getPropertiesWithAttribute(string $attribute) {
		$reflectionClass = new ReflectionClass(static::class);
		$properties = [];
		foreach ($reflectionClass->getProperties() as $property) {
			$attrs = $property->getAttributes($attribute);
			if (!empty($attrs)) {
				$properties[] = $property;
			}
		}
		return $properties;
	}

	/**
	 * Associative array with php proprieties as keys and the table columns as values
	 * of all columns that can be selected 
	 *
	 * @return array<string>
	 */
	protected static function selectColumns(): array {
		return static::anyColumns(DB\Block::SELECT);
	}

	/**
	 * Associative array with php proprieties as keys and the table columns as values
	 * of all columns that can be updated 
	 *
	 * @return array<string>
	 */
	protected static function updateColumns(): array {
		return static::anyColumns(DB\Block::UPDATE);
	}

	protected static function insertColumns(): array {
		return static::anyColumns(DB\Block::INSERT);
	}

	protected static function selectQuery(): string {
		$columns = static::selectColumns();
		$sql = "SELECT `".implode('`, `', $columns)."` FROM `".static::getTable()."`";
		return $sql;
	}

	protected static function updateQuery(): string {
		$columns = static::updateColumns();
		$sql = "UPDATE `".static::getTable()."` SET `".implode('`=?, `', $columns)."`=?";
		$sql .= " WHERE id = ?"; // ensure updates are scoped to the specific id
		return $sql;
	}

	protected static function deleteQuery(): string {
		$sql = "DELETE FROM `".static::getTable()."`";
		return $sql;
	}

	protected static function insertQuery(): string {
		$columns = static::insertColumns();
		$table = static::getTable();
		$sql = "INSERT INTO `{$table}` (`".implode('`, `', $columns)."`) ";
		$sql .=  "VALUES (?".str_repeat(", ?", count($columns) - 1).")";
		return $sql;
	}

	/**
	 * Associative array with php proprieties as keys and the table columns as values 
	 *
	 * @return array<string>
	 */
	protected static function getProperties(): array {
		$reflectionClass = new ReflectionClass(static::class);
		$properties = [];
		foreach ($reflectionClass->getProperties() as $property) {
			$attrs = $property->getAttributes(DB\Block::class);
			if (!empty($attrs)) {
				$dbBlock = $attrs[0]->newInstance();
				if (in_array(DB\Block::ALL, $dbBlock->actions)) {
					continue;
				}
			}
			$attrs = $property->getAttributes(DB\Column::class);
			if (empty($attrs)) {
				$properties[$property->getName()] = $property->getName();
				continue;
			}
			$dbColumn = $attrs[0]->newInstance();
			$properties[$property->getName()] = $dbColumn->name;

		}

		return $properties;
	}

	/**
	 * Returns an array with all attributs
	 */
	protected function toArray(): array {
		$array = [];
		$refClass = new ReflectionClass(static::class);
		foreach (static::getProperties() as $key => $value) {
			if ($refClass->hasProperty($key)) {
				$prop = $refClass->getProperty($key);
				$prop->setAccessible(true);
				$array[$value] = $prop->getValue($this);
				continue;
			}
			// fallback for dynamic / inherited public props
			$array[$value] = $this->$key;
		}
		return $array;
	}

	/**
	 * Returns an array without the sensitive proprieties
	 */
	protected function toSafeArray(): array {
		$array = $this->toArray();
		$sensitives = static::sensitiveColumns();
		foreach ($sensitives as $value) {
			unset($array[$value]);
		}
		return $array;
	}

	public function __set($name, $value) {
		if(property_exists(__CLASS__, $name)) {
			$this->$name = $value;
			return;
		}
		
		$properties = static::getProperties();
		$result = array_search($name, $properties);
		if ($result !== false)
			$this->$result = $value;
		
	}

	public function jsonSerialize(): array {
		return $this->toSafeArray();
	}
}


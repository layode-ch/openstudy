<?php
namespace Openstudy\Schemas;

use Exception;
use Openstudy\Attributes\Proprety;
use Openstudy\Attributes\Validators\Min;
use Openstudy\Attributes\Validators\Validator;
use Openstudy\Attributes\Validators\ValidatorException;
use ReflectionClass;
use ReflectionProperty;
use ReflectionUnionType;
use ReflectionType;

class BaseSchema {

	#[Proprety("Name"), Min(2)]
	public mixed $name;

	public function __construct(array $data) {
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}
	}

	public function __set(string $name, mixed $value) {

		$properties = $this->getPropertyNames();
		if (array_key_exists($name, $properties)) {
			$propertyName = $properties[$name];
			$prop = new ReflectionProperty($this, $propertyName);
			$this->validateProprety($name, $prop, $value);
			$this->$propertyName = $value;
			return;
		}
		if(property_exists(__CLASS__, $name)) {
			$this->$name = $value;
			return;
		}
		
	}

	private function validateProprety(string $name, ReflectionProperty $property, mixed $value) {
		$attrs = $this->getAttributesFromProprety($property, Validator::class);
		$errors = [];
		try {
			$this->verifyTypes($name, $property, $value);
		}
		catch (Exception $e) {
			throw new SchemaException([$e->getMessage()]);
		}
		foreach ($attrs as $attr) {
			$validator = $attr->newInstance();
			try {
				if ($validator instanceof Validator) {
					$validator->validate($name, $value);
				}
			}
			catch(Exception $e) {
				$errors[] = $e->getMessage();
			}
		}
		if (count($errors) > 0)
			throw new SchemaException($errors);
	}

	private function verifyTypes(string $name, ReflectionProperty $property, mixed $value) {
		$types = [];
		if ($property->getType() instanceof ReflectionUnionType) {
			foreach ($property->getType()->getTypes() as $t) {
				$types[] = $t->__toString();
			}
		}
		else {
			$types[] = $property->getType()->getName();
		}
		$type = $this->normalizeGetType(gettype($value));
		if (!in_array("mixed", $types, true) && !in_array($type, $types, true)) {
			throw new ValidatorException($this->fieldTypeMessage($name, $types));
		}
	}

	private function getAttributesFromProprety(ReflectionProperty $property, string $attribute) {
		$attrs = $property->getAttributes();
		$result = [];
		foreach ($attrs as $attr) {
			$attrClass = $attr->getName();

			if (is_subclass_of($attrClass, $attribute) || $attrClass == $attribute) {
				$result[] = $attr;
			}
		}
		return $result;
	}

	private function fieldTypeMessage(string $name, array $types): string {
		$message = "The filed '$name' must be of type '";
		$message .= implode("' or '", $types)."'.";
		return $message;
	}

	private function getPropertiesWithAttribute(string $attribute) {
		$reflectionClass = new ReflectionClass(static::class);
		$properties = [];
		foreach ($reflectionClass->getProperties() as $property) {
			$attrs = $property->getAttributes($attribute);
			if (!empty($attrs)) {
				$properties[] = $property;
			}
			else {
				$attrs = $property->getAttributes();
				foreach ($attrs as $attr) {
					$attrClass = $attr->getName();

					if (is_subclass_of($attrClass, $attribute)) {
						$properties[] = $property;
						break;
					}
				}
			}
		}
		return $properties;
	}

	private function getPropertyNames(): array {
		$properties = $this->getPropertiesWithAttribute(Proprety::class);
		$names = [];
		foreach ($properties as $property) {
			$attr = $property->getAttributes(Proprety::class)[0]->newInstance();
			if ($attr->name == null)
				$attr->name = $property->name;
			$names[$attr->name] = $property->name;
		}
		return $names;
	}

	private function normalizeGetType(string $t): string {
		return match ($t) {
			'boolean' => 'bool',
			'integer' => 'int',
			default   => $t
		};
	}
} 
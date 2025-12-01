<?php
namespace OpenStudy\Schemas;

use Exception;
use OpenStudy\Attributes\Proprety;
use OpenStudy\Attributes\Validators\Filter;
use OpenStudy\Attributes\Validators\Min;
use OpenStudy\Attributes\Validators\Validator;
use OpenStudy\Attributes\Validators\ValidatorException;
use ReflectionClass;
use ReflectionEnum;
use ReflectionProperty;
use ReflectionUnionType;
use ReflectionType;

class BaseSchema {

	public function __construct(array $data) {
		foreach ($data as $key => $value) {
			$this->set($key, $value);
		}
		$this->validateAllRequiredPropreties();
	}

	private function set(string $name, mixed $value) {
		$properties = $this->getPropertyNames();
		if (array_key_exists($name, $properties)) {
			$propertyName = $properties[$name];
			$prop = new ReflectionProperty($this, $propertyName);

			// If the property type is an enum class, try to convert the incoming value to the enum instance
			$type = $prop->getType();
			if ($type !== null) {
				$enumClass = null;
				if ($type instanceof ReflectionUnionType) {
					foreach ($type->getTypes() as $t) {
						$tname = $t->__toString();
						if (enum_exists($tname)) { $enumClass = $tname; break; }
					}
				} else {
					$tname = $type->getName();
					if (enum_exists($tname)) $enumClass = $tname;
				}

				if ($enumClass !== null && !($value instanceof $enumClass)) {
					try {
						if (method_exists($enumClass, 'tryFrom')) {
							$enumInstance = $enumClass::tryFrom($value);
							// tryFrom may return null for invalid values
							if ($enumInstance === null) {
								throw new \ValueError("Invalid enum value");
							}
						} else {
							$enumInstance = $enumClass::from($value);
						}
						$value = $enumInstance;
					} catch (\ValueError $e) {
						throw new SchemaException([$this->fieldValueMessage($name, $this->getEnumValues($enumClass))]);
					}
				}
			}

			$this->validateProprety($name, $prop, $value);
			$this->$propertyName = $value;
			return;
		}

		// Allow setting properties declared on the concrete schema class even when they are not marked with #[Proprety]
		if (property_exists(static::class, $name)) {
			$prop = new ReflectionProperty($this, $name);

			// same enum conversion for direct properties
			$type = $prop->getType();
			if ($type !== null) {
				$enumClass = null;
				if ($type instanceof ReflectionUnionType) {
					foreach ($type->getTypes() as $t) {
						$tname = $t->__toString();
						if (enum_exists($tname)) { $enumClass = $tname; break; }
					}
				} else {
					$tname = $type->getName();
					if (enum_exists($tname)) $enumClass = $tname;
				}

				if ($enumClass !== null && !($value instanceof $enumClass)) {
					try {
						if (method_exists($enumClass, 'tryFrom')) {
							$enumInstance = $enumClass::tryFrom($value);
							if ($enumInstance === null) {
								throw new \ValueError("Invalid enum value");
							}
						} else {
							$enumInstance = $enumClass::from($value);
						}
						$value = $enumInstance;
					} catch (\ValueError $e) {
						throw new SchemaException([$this->fieldValueMessage($name, $this->getEnumValues($enumClass))]);
					}
				}
			}

			$this->validateProprety($name, $prop, $value);
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
					$result = $validator->validate($name, $value);
					if ($validator instanceof Filter && $result == null) {
						$this->$name = filter_var($value, $validator->filter);
					}
				}
			}
			catch(Exception $e) {
				$errors[] = $e->getMessage();
			}
		}
		if (count($errors) > 0)
			throw new SchemaException($errors);
    	$property->setValue($this, $value);
	}

	private function validateAllRequiredPropreties() {
		$properties = $this->getPropertiesWithAttribute(Proprety::class);
		$errors = [];

		foreach ($properties as $property) {
			$name = array_search($property->getName(), $this->getPropertyNames());
			if (!$property->isInitialized($this)) {
				$errors[] = "The field '{$name}' is required but missing.";
				continue;
			}

			// Also reject null values if property does not allow null
			$type = $property->getType();
			if ($type && !$type->allowsNull() && $property->getValue($this) === null) {
				$errors[] = "The field '{$property->getName()}' cannot be null.";
			}
		}

		if (!empty($errors)) {
			throw new SchemaException($errors);
		}
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
			if ($property->getType()->allowsNull())
				$types[] = "null";
		}
		$type = $this->normalizeGetType(gettype($value));
		// Check if the property type is an enum
		$isEnum = $this->verifyEnum($types, $name, $property, $value);

		if (!in_array("mixed", $types, true) && !in_array($type, $types, true) && !$isEnum) {
			throw new ValidatorException($this->fieldTypeMessage($name, $types));
		}
	}

	private function verifyEnum(array $types, string $name, ReflectionProperty $property, mixed $value) {
		foreach ($types as $i => $t) {
			if (enum_exists($t)) {
				if ($value instanceof $t) {
					return true; // Value is already correct enum instance
				}

				// Try to convert the value to enum
				try {
					if (method_exists($t, 'tryFrom')) {
						$enumValue = $t::tryFrom($value);
						if ($enumValue !== null) {
							$this->{$property->getName()} = $enumValue;
							return true; // Successfully converted
						}
					} elseif (method_exists($t, 'from')) {
						$enumValue = $t::from($value);
						$this->{$property->getName()} = $enumValue;
						return true; // Successfully converted
					}
				} catch (\ValueError $e) {
					// conversion failed, continue to type error
				}

				throw new ValidatorException($this->fieldValueMessage($name, $this->getEnumValues($t)));
				return true;
			} 	
		}
		return false;
	}

	private function getEnumValues(string $enumClass): array {
		$reflection = new ReflectionEnum($enumClass);
		$cases = $reflection->getCases();
		$values = [];
		foreach ($cases as $case) {
			$values[] = $case->getValue();
		}
		return $values;
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

	private function fieldValueMessage(string $name, array $values): string {
		$message = "The filed '$name' must be of value '";
		$message .= implode("' or '", $values)."'.";
		return $message;
	}

	private function getPropertiesWithAttribute(?string $attribute = null) {
		$reflectionClass = new ReflectionClass(static::class);
		if ($attribute === null) {
			return $reflectionClass->getProperties();
		}
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
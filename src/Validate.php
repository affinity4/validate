<?php
namespace Affinity4\Validate;

use Affinity4\Validate\Exception\AlphaNumericException;
use Affinity4\Validate\Exception\InvalidDataTypeException;
use Affinity4\Validate\Exception\ValidationPropertyParserException;
use Affinity4\Validate\Exception\UnsignedIntException;
use Affinity4\Validate\Validator\TypeValidator;
use \phpDocumentor\Reflection\DocBlockFactory;
use \ReflectionClass;
use Exception;

trait Validate
{
    public $__validate__class;
    public $__validate__property;

    /**
     * Validation rules
     *
     * @var array
     */
    private $validation_rules = [];

    /**
     * Validation errors
     *
     * @var array
     */
    private $validation_errors = [];

    private function __validate__ShouldValidate(\ReflectionProperty $ReflectionProperty)
    {
        $DocBlockFactory = DocBlockFactory::createInstance();
        $DocBlockParser = $DocBlockFactory->create($ReflectionProperty->getDocComment());

        return $DocBlockParser->hasTag('validation');
    }

    private function __validate__getValidationsFromValidationTags($ValidationTags): array
    {
        $validations = [];
        foreach ($ValidationTags as $ValidationTag) {
            $validations[] = (string) $ValidationTag;
        }

        return $validations;
    }

    private function __validate__getValidations(\ReflectionProperty $ReflectionProperty): array
    {
        $DocBlockFactory = DocBlockFactory::createInstance();
        $DocBlockParser = $DocBlockFactory->create($ReflectionProperty->getDocComment());
        $ValidationTags = $DocBlockParser->getTagsByName('validation');
            
        return $this->__validate__getValidationsFromValidationTags($ValidationTags);
    }

    private function __validate__getValidators(\ReflectionProperty $ReflectionProperty): array
    {
        $validations = $this->__validate__getValidations($ReflectionProperty);
            $validators = [];
            foreach ($validations as $validation) {
                preg_match("/^type\((?P<type>int|string)(:(?P<validators>.*))?\)/mi", $validation, $matches);

                if (empty($matches)) {
                    throw new ValidationPropertyParserException("@validation property was not formatted correctly");
                }

                $type = (array_key_exists('type', $matches)) ? $matches['type'] : '';
                if (empty($type)) {
                    throw new ValidationPropertyParserException("@validation error: Valid type not found");
                }

                $validators_list = (array_key_exists('validators', $matches)) ? $matches['validators'] : '';
                $_validators = (trim($validators_list) !== '') ? explode('|', $validators_list) : [];

                $validators[] = ['type' => $type, 'validators' => $_validators, '_' => $ReflectionProperty];
            }

            return $validators;
    }

    /**
     * Execute Before Or After Function
     *
     * @param array $functions
     * @param array $validator
     * @param mixed $value
     * 
     * @return mixed
     */
    private function __validate__executeBeforeOrAfterFunction(array $functions, array $validator, mixed $value): mixed
    {
        if (!empty($functions)) {
            foreach ($functions as $name => $function) {
                if (in_array($name, $validator['validators'])) {
                    $fn = $functions[$name];
                    
                    $value = $fn($validator, $value);
                }
            }
        }

        return $value;
    }

    private function __validate__validateValue(array $validators, mixed $value)
    {
        foreach ($validators as $validator) {
            if (!array_key_exists($validator['type'], $this->validation_rules)) {
                throw new ValidationPropertyParserException("@validations error: Valid type not found in type() declaration");
            }
            
            /* @var string */
            $type = $validator['type'];

            /* @var array */
            $validator_functions = $this->validation_rules[$type];

            /* @var array */
            $before_functions = (array_key_exists('before', $validator_functions))
                ? $validator_functions['before']
                :  [];
            
            // Before
            $value = $this->__validate__executeBeforeOrAfterFunction($before_functions, $validator, $value);

            // Main
            if (!array_key_exists(0, $validator_functions) || empty($validator_functions[0])) {
                throw new ValidationPropertyParserException("@validations error: No validator function set for type $type");
            }

            $main_functions = $validator_functions[0];
            foreach ($main_functions as $function) {
                $value = $function($validator, $value);
            }

            // After
            /* @var array */
            $after_functions = (array_key_exists('after', $validator_functions))
                ? $validator_functions['after']
                :  [];

            $value = $this->__validate__executeBeforeOrAfterFunction($after_functions, $validator, $value);
        }

        return $value;
    }

    /**
     * __validate__addValidationRules
     *
     * @return void
     */
    private function __validate__addValidationRules(): void
    {
        $this->addValidationRule('string', function(array $validator, mixed $value) {
            if (!is_string($value)) {
                $actual_type = gettype($value);
                $this->addValidationError("Property {$validator['_']->class}::{$validator['_']->name} must be of type {$validator['type']}. Type $actual_type detected");
            }

            return $value;
        });

        $this->addValidationRule('before.string:cast', function(array $validator, mixed $value) {
            if (gettype($value) === 'object') {
                if (method_exists($value, '__toString')) {
                    $value = (string) $value;
                } else {
                    if ($value instanceof \Closure || is_callable($value)) {
                        try {
                            $value = (string) $value();
                        } catch(\Exception $e) {
                            // Don't throw any errors
                        }
                    }
                }
            }

            if (is_bool($value)) {
                $value = (string) $value;
            }

            if (is_numeric($value)) {
                $value = (string) $value;
            }

            return $value;
        });

        $this->addValidationRule('after.string:alnum|alphanum|alphanumeric', function(array $validator, string $value) {
            if (!ctype_alnum($value)) {
                $this->addValidationError("Property {$validator['_']->class}::{$validator['_']->name} must be alphanumeric (letters and numbers only). Value was $value");
            }

            return $value;
        });

        $this->addValidationRule('int', function(array $validator, mixed $value) {
            if (is_string($value) || !is_int($value)) {
                $actual_type = gettype($value);
                $this->addValidationError("Property {$validator['_']->class}::{$validator['_']->name} must be of type {$validator['type']}. Type $actual_type detected");
            }

            return $value;
        });

        $this->addValidationRule('before.int:cast', function(array $validator, mixed $value) {
            if (gettype($value) === 'string') {
                $is_numeric = is_numeric($value);
                if ($is_numeric) {
                    $value = (int) $value;
                }
            }

            return $value;
        });

        $this->addValidationRule('after.int:unsigned', function(array $validator, int $value) {
            if (!(abs($value) === $value)) {
                $this->addValidationError("Property {$validator['_']->class}::{$validator['_']->name} must be unsigned (e.g. a positive integer or decimal number). Value was $value");
            }

            return $value;
        });
    }

    /**
     * __set
     * 
     * 1. If property does not exist we throw the standard exception
     * 2. If property is private or protected, we check if it has the @validation tag
     * 3. If it does, we parse the validation rules and throw an exception if the validation fails
     *
     * @param string $property
     * @param mixed $value
     * @return void
     * 
     * @throws \Exception
     */
    public function __set(string $property, mixed $value): void
    {
        $reflection = new ReflectionClass($this);

        $has_property = $reflection->hasProperty($property);
        if (!$has_property) {
            $classname = get_class($this);
            throw new \ErrorException("Property $classname::$property does not exist", 0, E_ERROR);
        }

        $this->__validate__class = get_class($this);
        $this->__validate__property = $property;

        $ReflectionProperty = new \ReflectionProperty($this->__validate__class, $this->__validate__property);

        $should_validate = $this->__validate__ShouldValidate($ReflectionProperty);
        if ($should_validate) {
            $validators = $this->__validate__getValidators($ReflectionProperty);

            $this->__validate__addValidationRules();

            $value = $this->__validate__validateValue($validators, $value);
        }

        $this->$property = $value;
    }

    /**
     * Add Validation Rule
     * 
     * ((before|after).)?type(:validator(|alias1|alias2...)?)?
     *
     * @param string $pattern
     * @param callable $validation
     * 
     * @return void
     */
    public function addValidationRule(string $pattern, callable $validation): void
    {
        $patterns = explode('.', $pattern);
        if (count($patterns) > 1) {
            if (in_array($patterns[0], ['before', 'after'])) {
                $when = $patterns[0];
                $type_and_name = explode(':', $patterns[1]);
                $type = $type_and_name[0];
                if (count($type_and_name) < 2) {
                    throw new \Exception("A name was not provided for validation rule $pattern. Must have a name to identify validator e.g. before.string:password ('password' is the name of the validator)");
                }
    
                $names = explode('|', $type_and_name[1]);
                foreach ($names as $name) {
                    $this->validation_rules[$type][$when][$name] = $validation;           
                }

                $this->validation_rules[$type][$when][$name] = $validation;
                /* This would create something like...
                $pattern = 'after.string:alnum|alphanum';
                $when = 'after';
                $type = 'string';
                $names = ['alnum', 'alphanum'];
                $this->validation_rules = [
                    'string' => [
                        'after' => [
                            'alnum' => function (array $validator, string $value) { ...validate is alnum... },
                            'alphanum => function (array $validator, string $value) { ...validate is alnum... },
                        ]
                    ]
                ];
                */
            } else {
                throw new \Exception("Pattern argument was not formatted correctly. No before or after hook found in pattern string e.g. before.string:validator. Pattern was '$pattern'");
            }
        } else {
            $patterns = explode('.', $pattern);
            $type = $patterns[0];

            $this->validation_rules[$type][0][] = $validation;
        }
    }

    /**
     * Get Validation Rules
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        return $this->validation_rules;
    }

    /**
     * Add Validation Error
     *
     * @param string $error
     * 
     * @return void
     */
    public function addValidationError(string $error): void
    {
        $this->validation_errors[] = $error;
    }

    /**
     * Get Validation Errors
     *
     * @return array
     */
    public function getValidationErrors(): array
    {
        return $this->validation_errors;
    }

    /**
     * Is Valid
     *
     * @return boolean
     */
    public function isValid(): bool
    {
        return (count($this->getValidationErrors()) === 0);
    }
}

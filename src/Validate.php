<?php
namespace Affinity4\Validate;

use Affinity4\Validate\Exception\ValidationPropertyParserException;
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

    private function __validate__getTypeValidatorArray(string $validation, \ReflectionProperty $ReflectionProperty): array
    {
        preg_match("/^type\((?P<type>int|string|float|numeric)(:(?P<validators>.*))?(,\s*(?P<length>\d+))?\)/mi", $validation, $matches);

        if (empty($matches)) {
            throw new ValidationPropertyParserException("@validation property was not formatted correctly");
        }

        $type = (array_key_exists('type', $matches)) ? $matches['type'] : '';
        if (empty($type)) {
            throw new ValidationPropertyParserException("@validation error: Valid type not provided");
        }

        $length = (array_key_exists('length', $matches)) ? (int) $matches['length'] : null;

        $validators_list = (array_key_exists('validators', $matches)) ? $matches['validators'] : '';
        $_validators = (trim($validators_list) !== '') ? explode('|', $validators_list) : [];

        return ['type' => $type, 'validators' => $_validators, 'length' => $length, '_ReflectionProperty' => $ReflectionProperty];
    }

    private function __validate__getMatchValidatorArray(string $validation, \ReflectionProperty $ReflectionProperty): array
    {
        preg_match("/^match\((?P<pattern>.*)\)/", $validation, $matches);

        $pattern = (array_key_exists('pattern', $matches)) ? $matches['pattern'] : '';
        if (empty($pattern)) {
            throw new ValidationPropertyParserException("@validation match() has no regex pattern");
        }

        return [
            'type' => 'match',
            'validators' => [
                'pattern' => $pattern
            ], '_ReflectionProperty' => $ReflectionProperty
        ];
    }

    private function __validate__getReplaceValidatorArray(string $validation, \ReflectionProperty $ReflectionProperty): array
    {
        preg_match("/^replace\((?P<pattern>.*),\s*(?P<replace>.*)\)/", $validation, $matches);

        $pattern = (array_key_exists('pattern', $matches)) ? $matches['pattern'] : '';
        if (empty($pattern)) {
            throw new ValidationPropertyParserException("@validation match() has no regex pattern");
        }

        $replace = (array_key_exists('replace', $matches)) ? $matches['replace'] : '';
        if (empty($replace)) {
            throw new ValidationPropertyParserException("@validation match() has no replace string");
        }

        return [
            'type' => 'replace',
            'validators' => [
                'pattern' => $pattern,
                'replace' => $replace,
            ], '_ReflectionProperty' => $ReflectionProperty
        ];
    }

    private function __validate__getValidators(\ReflectionProperty $ReflectionProperty): array
    {
        $validations = $this->__validate__getValidations($ReflectionProperty);
            $validators = [];
            foreach ($validations as $validation) {
                $valid_validation_types = "type|match|replace|any";
                preg_match("/^(?P<validation_type>$valid_validation_types)\(/", $validation, $matches);
                $validation_type = (array_key_exists('validation_type', $matches)) ? $matches['validation_type'] : null;

                // Here we are simply populating the $validators array which is what gets passed to the validation_rules callbacks as the first argument
                switch($validation_type) {
                    case 'type': 
                        $validators[] = $this->__validate__getTypeValidatorArray($validation, $ReflectionProperty);
                    break;
                    case 'match':
                        $validators[] = $this->__validate__getMatchValidatorArray($validation, $ReflectionProperty);
                    break;
                    case 'replace':
                        $validators[] = $this->__validate__getReplaceValidatorArray($validation, $ReflectionProperty);
                    break;
                    default :
                        throw new ValidationPropertyParserException("@validation property was not formatted correctly. Please ensure main validation function is one of $valid_validation_types");
                    break;
                }
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
                throw new ValidationPropertyParserException("@validations error: Validator for type({$validator['type']}) not found in validation rules array. Please ensure a rule was added for this type using Validate->addValationRule()");
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
     * __validate__addValidationRules
     *
     * @return void
     */
    private function __validate__addValidationRules(): void
    {
        /* ----------------------------------------
         * string
         * ----------------------------------------
         */
        $this->addValidationRule('string', function(array $validator, mixed $value) {
            if (!is_string($value)) {
                $expected_type = $validator['type'];
                $this->addValidationError("Must be of type $expected_type", $value, $validator);
            }

            return $value;
        });

        /* ----------------------------------------
         * before.string:cast
         * ----------------------------------------
         */
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

        /* ----------------------------------------
         * after.string:alnum|alphanum|alphanumeric
         * ----------------------------------------
         */
        $this->addValidationRule('after.string:alnum|alphanum|alphanumeric', function(array $validator, string $value) {
            if (!ctype_alnum($value)) {
                $this->addValidationError("Value must be alphanumeric (letters and numbers only)", $value, $validator);
            }

            return $value;
        });

        /* ----------------------------------------
         * int
         * ----------------------------------------
         */
        $this->addValidationRule('int', function(array $validator, mixed $value) {
            if (is_string($value) || !is_int($value)) {
                $expected_type = $validator['type'];
                $this->addValidationError("Must be of type $expected_type", $value, $validator);
            }

            return $value;
        });

        /* ----------------------------------------
         * before.int:cast
         * ----------------------------------------
         */
        $this->addValidationRule('before.int:cast', function(array $validator, mixed $value) {
            if (gettype($value) === 'string') {
                $is_numeric = is_numeric($value);
                if ($is_numeric) {
                    $value = (int) $value;
                }
            }

            return $value;
        });

        /* ----------------------------------------
         * after.int:unsigned
         * ----------------------------------------
         */
        $this->addValidationRule('after.int:unsigned', function(array $validator, int $value) {
            if (!(abs($value) === $value)) {
                $this->addValidationError("Value must be unsigned (a positive number)", $value, $validator);
            }

            $length = $validator['length'];
            if ($length !== null) {
                if ($value > $length) {
                    $this->addValidationError("Max value is $length", $value, $validator);
                }
            }

            return $value;
        });

        /* ----------------------------------------
         * float
         * ----------------------------------------
         */
        $this->addValidationRule('float', function(array $validators, mixed $value) {
            if (!is_float($value)) {
                $this->addValidationError("Type must be float", $value, $validators);
            } else {
                $length = $validators['length'];
                $decimal_places = 'decimal_places';
                $pattern = "/\d+\.(?P<$decimal_places>\d{$length})$/";
                preg_match($pattern, $value, $matches);
                if (array_key_exists($decimal_places, $matches)) {
                    if ((int) $matches[$decimal_places] !== (int) $length) {
                        $this->addValidationError("Must have exactly $length $decimal_places", $value, $validators);
                    }    
                }
            }

            return $value;
        });

        /* ----------------------------------------
         * numeric
         * ----------------------------------------
         */
        $this->addValidationRule('numeric', function(array $validators, mixed $value) {
            if (!is_numeric($value)) {
                $this->addValidationError("Type must be numeric", $value, $validators);
            }

            return $value;
        });

        /* ----------------------------------------
         * after.string:alpha
         * ----------------------------------------
         */
        $this->addValidationRule('after.string:alpha', function(array $validators, mixed $value) {
            if (!ctype_alpha($value)) {
                $this->addValidationError("Type must be alphabet characters only", $value, $validators);
            }

            return $value;
        });

        /* ----------------------------------------
         * after.string:snakecase
         * ----------------------------------------
         */
        $this->addValidationRule('after.string:snakecase', function(array $validators, mixed $value) {
            preg_match('/^[a-z0-9]+(?:_[a-z0-9]+)+/', $value, $matches);

            if (empty($matches)) {
                $this->addValidationError("Value was not in snakecase (snake_case)", $value, $validators);
            }

            return $value;
        });

        /* ----------------------------------------
         * match($pattern)
         * ----------------------------------------
         */
        $this->addValidationRule('match', function(array $validators, string $value) {
            $pattern = "/{$validators['validators']['pattern']}/";
            preg_match($pattern, $value, $matches);

            if (empty($matches)) {
                $this->addValidationError("Value did not match pattern", $value, $validators);
            }

            return $value;
        });

        /* ----------------------------------------
         * replace($pattern, $replace)
         * ----------------------------------------
         */
        $this->addValidationRule('replace', function(array $validators, string $value) {
            $pattern = "/{$validators['validators']['pattern']}/";
            $replace = $validators['validators']['replace'];
            $value = preg_replace($pattern, $replace, $value);

            return $value;
        });
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
    private function getValidationRules(): array
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
    public function addValidationError(string $error, mixed $value, array $validator): void
    {
        $ReflectionProperty = $validator['_ReflectionProperty'];
        $this->validation_errors[$ReflectionProperty->name][] = [
            'class' => $ReflectionProperty->class,
            'property' => $ReflectionProperty->name,
            'value' => $value,
            'error' => $error
        ];
    }

    /**
     * Get Validation Errors
     *
     * @return \Affinity4\Validate\ValidationErrors|array
     */
    public function getValidationErrors(string $property = null): \Affinity4\Validate\ValidationErrors|array
    {
        if (!is_null($property)) {
            if (!array_key_exists($property, $this->validation_errors)) {
                $class = get_class($this);
                throw new \Exception("No key '$property' in {$class}->getValidationErrors() array");
            }

            $ValidationErrors = new ValidationErrors($this->validation_errors[$property]);

            return $ValidationErrors;
        }

        $ValidationErrors = [];
        foreach ($this->validation_errors as $property => $validation_errors) {
            $ValidationErrors[$property] = new ValidationErrors($validation_errors);
        }

        return $ValidationErrors;
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

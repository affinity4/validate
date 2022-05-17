<?php
namespace Affinity4\Validate;

use ArrayAccess;
use PHPUnit\Util\Xml\Validator;

class ValidationErrors implements ArrayAccess
{
    /**
     * Errors
     *
     * @var array
     */
    private array $errors = [];

    /**
     * Pointer
     *
     * @var array
     */
    private array $pointer = [];

    /**
     * Index
     * 
     * @var int
     */
    private int $index = 0;

    /**
     * Class
     *
     * @var string
     */
    public string $class;

    /**
     * Property
     *
     * @var string
     */
    public string $property;

    /**
     * Value
     *
     * @var mixed
     */
    public mixed $value;

    /**
     * Error
     *
     * @var string
     */
    public string $error;

    /**
     * Constructor
     *
     * @param array $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Offset Set
     *
     * @param mixed $offset
     * @param mixed $value
     * 
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->errors[] = $value;
        } else {
            $this->errors[$offset] = $value;
        }
    }

    /**
     * Offset Get
     *
     * @param mixed $offset
     * 
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return isset($this->errors[$offset]) ? $this->errors[$offset] : null;
    }

    /**
     * Offset Exists
     *
     * @param mixed $offset
     * 
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->errors[$offset]);
    }

    /**
     * Offset Unset
     *
     * @param mixed $offset
     * 
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->errors[$offset]);
    }

    /**
     * First
     * 
     * Moves pointer to the first errors array
     *
     * @return ValidationErrors
     */
    public function first(): ValidationErrors
    {
        $this->index = 0;
        if ($this->offsetExists($this->index)) {
            $this->pointer = $this->offsetGet($this->index);

            $this->class    = $this->pointer['class'];
            $this->property = $this->pointer['property'];
            $this->value    = $this->pointer['value'];
            $this->error    = $this->pointer['error'];
        }

        return $this;
    }

    /**
     * Next
     * 
     * Moves pointer to the next errors array
     *
     * @return ValidationErrors
     */
    public function next(): ValidationErrors
    {
        $index = $this->index + 1;
        if ($this->offsetExists($index)) {
            $this->index = $index;
            $this->pointer = $this->offsetGet($this->index);

            $this->class    = $this->pointer['class'];
            $this->property = $this->pointer['property'];
            $this->value    = $this->pointer['value'];
            $this->error    = $this->pointer['error'];
        }

        return $this;
    }

    /**
     * Prev
     * 
     * Moves pointer to the previous errors array
     *
     * @return ValidationErrors
     */
    public function prev(): ValidationErrors
    {
        $index = $this->index - 1;
        if ($this->offsetExists($index)) {
            $this->index = $index;
            $this->pointer = $this->offsetGet($this->index);

            $this->class    = $this->pointer['class'];
            $this->property = $this->pointer['property'];
            $this->value    = $this->pointer['value'];
            $this->error    = $this->pointer['error'];
        }

        return $this;
    }

    /**
     * Last
     * 
     * Moves pointer to last errors array
     *
     * @return ValidationErrors
     */
    public function last(): ValidationErrors
    {
        $index = count($this->errors) - 1;
        if ($this->offsetExists($index)) {
            $this->index = $index;
            $this->pointer = $this->offsetGet($this->index);

            $this->class    = $this->pointer['class'];
            $this->property = $this->pointer['property'];
            $this->value    = $this->pointer['value'];
            $this->error    = $this->pointer['error'];
        }

        return $this;
    }

    /**
     * Count
     *
     * @return integer
     */
    public function count(): int
    {
        return count($this->errors);
    }

    /**
     * errors
     * 
     * Returns an array with just the error messages
     * 
     * @return array
     */
    public function errors(): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $errors[] = $error['error'];
        }
        
        return $errors;
    }
}
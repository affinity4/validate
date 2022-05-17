<?php
namespace Affinity4\Validate\Exception;

class ValidationPropertyParserException extends \Exception
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
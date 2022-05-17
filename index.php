<?php
require_once __DIR__ . "/vendor/autoload.php";

use Affinity4\Validate\Validate;
use Test\Stub\Stub;

$Model = new Stub;

// $Model->id = -1;
// $Model->addValidationRule('string', function(array $validation, string $value) use ($Model) {
//     $min_length = 8;
//     $length = strlen($value);

//     if ($length < $min_length) {
//         $Model->addValidationError("Password must be at least $min_length characters in length");
//     }

//     preg_match("/[A-Z]+/", $value, $matches);
//     if (count($matches) < 1) {
//         $Model->addValidationError("Password must have at least one capital letter");
//     }

//     preg_match("/\d+/", $value, $matches);
//     if (count($matches) < 1) {
//         $Model->addValidationError("Password must have at least one number");
//     }

//     return $value;
// }, 'password');

$Model->int = '123';
die(print_r($Model->getValidationErrors(), true));


// $TypeValidator = new TypeValidator;
// $TypeValidator->setClassProperty(get_class($Model), '__toString');
// $TypeValidator->setValue($Model);
// $TypeValidator->setType('string');
// $TypeValidator->setLength(255);
// $TypeValidator->setValidators(['cast']);
// die($TypeValidator->validate() ? 'Valid' : 'Invalid');

<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Test\Stub\Stub;

function dd(mixed ...$args)
{
    die(print_r($args, true));
}

/**
 * TypeValidator Tests
 */
final class ValidateTest extends TestCase
{
    /* ---------------------- */
    /*      String Tests      */
    /* ---------------------- */

    /**
     * Data provider for testStringValidatorCapturesValidationErrors
     *
     * @return array
     */
    private function dataProvider__testStringValidatorCapturesValidationErrors(): array
    {
        return [
            [true],
            [false],
            [123],
            [1.23],
            [new stdClass],
            [function() {
                return 'a string';
            }]
        ];
    }

    /**
     * Test string validator throws InvalidDataTypeException
     * 
     * @dataProvider dataProvider__testStringValidatorCapturesValidationErrors
     *
     * @return void
     */
    public function testStringValidatorCapturesValidationErrors(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->string = $value;
        
        $class = get_class($Stub);
        $type = gettype($value);
        $error_message = "Property $class::string must be of type string. Type $type detected";

        $this->assertFalse($Stub->isValid(), "Failed to assert {$Stub}->isValid() was false");
        $this->assertContainsEquals(
            $error_message, 
            $Stub->getValidationErrors(),
            "Error message '$error_message' not in validation errors array"
        );
    }

    /**
     * Data provider for valid strings
     *
     * @return array
     */
    private function dataProvider__validStrings(): array
    {
        $Stub = new Stub;

        function aString() {
            return 'a string';
        };

        return [
            ['a string'],
            [(string) $Stub],
            [aString()],
            [(string) 123],
            [(string) true],
            [(string) false]
        ];
    }

    /**
     * Test String Validator Passes Valid Strings
     * 
     * @dataProvider dataProvider__validStrings
     *
     * @param string $value
     * 
     * @return void
     */
    public function testStringValidatorPassesValidStrings(string $value): void
    {
        $Stub = new Stub;
        $Stub->string = $value;

        $this->assertSame($Stub->getString(), $value);
        $this->assertTrue($Stub->isValid());
    }

    /**
     * Data Provider for testCastValidatorCastsToString
     *
     * @return array
     */
    private function dataProvider__testCastValidatorCastsToString(): array
    {
        return [
            [new Stub],
            [function() {
                return 'a string';
            }],
            [true],
            [false],
            [123]
        ];
    }

    /**
     * Test cast validator casts to string
     * 
     * @dataProvider dataProvider__testCastValidatorCastsToString
     *
     * @param mixed $value
     * 
     * @return void
     */
    public function testCastValidatorCastsToString(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->cast_to_string = $value;

        $this->assertTrue($Stub->isValid(), "Failed to assert ModelWithValidation->isValid() was true");
    }

    /**
     * Data Provider for Non AlphaNumeric Values
     *
     * @return array
     */
    private function dataProvider__nonAlphaNumericValues(): array
    {
        return [
            ['i_am_not_alphanumeric'],
            ['i-am-not-alphanumeric123'],
            ['i.am.not.alphanumeric'],
            ['i/am/not/alphanumeric'],
            ['i\am\not\alphanumeric'],
            ['i am not alphanumeric'],
            ['iAmNotAlphanumeric!']
        ];
    }

    /**
     * Test string validator with alphanumeric validator captures validation errors
     * 
     * @dataProvider dataProvider__nonAlphaNumericValues
     *
     * @param string $value
     * 
     * @return void
     */
    public function testStringValidatorWithAlnumValidatorCapturesValidationErrors(string $value): void
    {
        $Stub = new Stub;
        $Stub->alnum = $value;

        $class = get_class($Stub);
        $property = 'alnum';
        $error_message = "Property $class::$property must be alphanumeric (letters and numbers only). Value was $value";

        $this->assertFalse($Stub->isValid(), "Failed to assert {$Stub}->isValid() was false");
        $this->assertContainsEquals(
            $error_message, 
            $Stub->getValidationErrors(),
            "Error message '$error_message' not in validation errors array"
        );
    }

    /**
     * Test string validator with alphanumeric validator captures validation errors
     * 
     * @dataProvider dataProvider__nonAlphaNumericValues
     *
     * @param string $value
     * 
     * @return void
     */
    public function testStringValidatorWithAlphanumValidatorCapturesValidationErrors(string $value): void
    {
        $Stub = new Stub;
        $Stub->alphanum = $value;

        $class = get_class($Stub);
        $property = 'alphanum';
        $error_message = "Property $class::$property must be alphanumeric (letters and numbers only). Value was $value";

        $this->assertFalse($Stub->isValid(), "Failed to assert {$Stub}->isValid() was false");
        $this->assertContainsEquals(
            $error_message,
            $Stub->getValidationErrors(),
            "Error message '$error_message' not in validation errors array"
        );
    }

    /**
     * Test string validator with alphanumeric validator captures validation errors
     * 
     * @dataProvider dataProvider__nonAlphaNumericValues
     *
     * @param string $value
     * 
     * @return void
     */
    public function testStringValidatorWithAlnumericValidatorCapturesValidationErrors(string $value): void
    {
        $Stub = new Stub;
        $Stub->alphanumeric = $value;

        $class = get_class($Stub);
        $property = 'alphanumeric';
        $error_message = "Property $class::$property must be alphanumeric (letters and numbers only). Value was $value";

        $this->assertFalse($Stub->isValid(), "Failed to assert {$Stub}->isValid() was false");
        $this->assertContainsEquals(
            $error_message, 
            $Stub->getValidationErrors(),
            "Error message '$error_message' not in validation errors array"
        );
    }

    /**
     * dataProvider__alphaNumericValues
     *
     * @return array
     */
    private function dataProvider__alphaNumericValues(): array
    {
        return [
            ['iAmAlphanumeric'],
            ['iAmAlphanumeric123'],
            ['123IAmAlphanumeric'],
            ['123']
        ];
    }

    /**
     * Test string validator with alphanumeric validator returns true for valid alphanumeric values
     *
     * @dataProvider dataProvider__alphaNumericValues
     * 
     * @param mixed $value
     * 
     * @return void
     */
    public function testStringValidatorWithAlphanumericValidatorPassesForValidAlphanumericValues(string $value): void
    {
        $Stub = new Stub;
        $Stub->alnum = $value;

        $this->assertSame($Stub->getAlnum(), $value);
        $this->assertTrue($Stub->isValid());
    }

    /* ------------------- */
    /*      Int Tests      */
    /* ------------------- */

    /**
     * Data provider for non integer values
     *
     * @return array
     */
    private function dataProvider__nonIntValues(): array
    {
        return [
            ['1'],
            ['string'],
            [null],
            [false],
            [true],
            [function() {
                return 123;
            }]
        ];
    }

    /**
     * Test int validatpr captures validation errors
     * 
     * @dataProvider dataProvider__nonIntValues
     * 
     * @return void
     */
    public function testIntValidatorCapturesValidationErrors($value): void
    {
        $Stub = new Stub;
        $Stub->int = $value;

        $class = get_class($Stub);
        $property = 'int';
        $expected_type = 'int';
        $type = gettype($value);
        $error_message = "Property $class::$property must be of type $expected_type. Type $type detected";

        $this->assertFalse($Stub->isValid(), "Failed to assert {$Stub}->isValid() was false");
        $this->assertContainsEquals(
            $error_message, 
            $Stub->getValidationErrors(),
            "Error message '$error_message' not in validation errors array"
        );
    }

    /**
     * Data provider: Cast numeric string to int
     *
     * @return array
     */
    private function dataProvider__castNumericStringToInt(): array
    {
        return [
            ['123', 123],
            ['-123', -123]
        ];
    }

    /**
     * Test cast converts numeric string to int
     * 
     * @dataProvider dataProvider__castNumericStringToInt
     *
     * @return void
     */
    public function testCastConvertsNumericStringToInt(mixed $value, int $expected_value): void
    {
        $Stub = new Stub;
        $Stub->user_id = $value;

        $this->assertSame($Stub->getUserId(), $expected_value);
        $this->assertTrue($Stub->isValid());
    }

    /**
     * Data provider: Uncastable Types
     *
     * @return array
     */
    private function dataProvider__uncastableTypes(): array
    {
        return [
            [null],
            [false],
            [true],
            [function() {
                return '123';
            }]
        ];
    }

    /**
     * Test cast is ignored on uncastable types
     * 
     * @dataProvider dataProvider__uncastableTypes
     *
     * @param mixed $value
     * 
     * @return void
     */
    public function testCastIsIgnoredOnUncastableTypes(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->user_id = $value;

        $class = get_class($Stub);
        $property = 'user_id';
        $expected_type = 'int';
        $type = gettype($value);
        $error_message = "Property $class::$property must be of type $expected_type. Type $type detected";

        $this->assertFalse($Stub->isValid(), "Failed to assert {$Stub}->isValid() was false");
        $this->assertContainsEquals(
            $error_message,
            $Stub->getValidationErrors(),
            "Error message '$error_message' not in validation errors array"
        );
    }

    /**
     * Data provider: Signed values
     *
     * @return array
     */
    private function dataProvider__signedValues(): array
    {
        return [
            [-123],
            [-1]
        ];
    }

    /**
     * Test unsigned validator throws UnsigneedIntException
     * 
     * @dataProvider dataProvider__signedValues
     *
     * @return void
     */
    public function testUnsignedValidatorThrowsUnsignedIntException(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->id = $value;

        $class = get_class($Stub);
        $property = 'id';
        $error_message = "Property $class::$property must be unsigned (e.g. a positive integer or decimal number). Value was $value";

        // ust be unsigned (e.g. a positive integer or decimal number). Value was
        $this->assertFalse($Stub->isValid(), "Failed to assert {$Stub}->isValid() was false");
        $this->assertContainsEquals(
            $error_message,
            $Stub->getValidationErrors(),
            "Error message '$error_message' not in validation errors array"
        );
    }

    /**
     * Data provider: Unsigned values
     *
     * @return array
     */
    private function dataProvider__unsignedValues(): array
    {
        return [
            [1],
            [12],
            [123]
        ];
    }

    /**
     * Test unsigned validator returns true for unsigned values
     * 
     * @dataProvider dataProvider__unsignedValues
     *
     * @param mixed $value
     * 
     * @return void
     */
    public function testUnsignedValidatorReturnsTrueForUnsignedValues(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->id = $value;

        $this->assertTrue( (abs($Stub->getId()) === $Stub->getId()) );
        $this->assertTrue($Stub->isValid());
    }

    public function testAddValidationRule()
    {
        $Stub = new Stub;
        $Stub->addValidationRule('after.string:password', function(array $validation, string $value) use ($Stub) {
            $min_length = 8;
            $length = strlen($value);

            if ($length < $min_length) {
                $Stub->addValidationError("Password must be at least $min_length characters in length");
            }

            preg_match("/[A-Z]+/", $value, $matches);
            if (count($matches) < 1) {
                $Stub->addValidationError("Password must have at least one capital letter");
            }

            preg_match("/\d+/", $value, $matches);
            if (count($matches) < 1) {
                $Stub->addValidationError("Password must have at least one number");
            }

            return $value;
        });
        $Stub->password = '_';

        $validation_errors = $Stub->getValidationErrors();

        $this->assertContainsEquals("Password must be at least 8 characters in length", $validation_errors);
        $this->assertContainsEquals("Password must have at least one capital letter", $validation_errors);
        $this->assertContainsEquals("Password must have at least one number", $validation_errors);
    }
}
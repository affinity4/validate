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
     * Data provider: Non String Values
     *
     * @return array
     */
    private function dataProvider__nonStringValues(): array
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
     * @dataProvider dataProvider__nonStringValues
     *
     * @return void
     */
    public function testStringValidatorCapturesValidationErrors(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->string = $value;
        
        $class = get_class($Stub);
        $type = gettype($value);
        $error_message = "Must be of type string";
        $validation_errors = $Stub->getValidationErrors('string');

        $this->assertFalse($Stub->isValid(), "Failed to assert {$Stub}->isValid() was false");
        $this->assertEquals($error_message, $validation_errors[0]['error'], "Error message '$error_message' not in validation errors array");
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
        $error_message = "Value must be alphanumeric (letters and numbers only)";

        $this->assertFalse($Stub->isValid(), "Failed to assert {$Stub}->isValid() was false");
        $this->assertEquals($error_message, $Stub->getValidationErrors('alnum')->first()->error);
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
        $error_message = "Value must be alphanumeric (letters and numbers only)";

        $this->assertFalse($Stub->isValid(), "Failed to assert {$Stub}->isValid() was false");
        $this->assertEquals($error_message, $Stub->getValidationErrors('alphanum')->first()->error);
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
        $error_message = "Value must be alphanumeric (letters and numbers only)";

        $this->assertFalse($Stub->isValid(), "Failed to assert {$Stub}->isValid() was false");
        $this->assertEquals($error_message, $Stub->getValidationErrors('alphanumeric')->first()->error);
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
        $error_message = "Must be of type int";

        $this->assertFalse($Stub->isValid(), "Failed to assert {$Stub}->isValid() was false");
        $this->assertEquals($error_message, $Stub->getValidationErrors('int')->first()->error);
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

        $error_message = "Must be of type int";

        $this->assertFalse($Stub->isValid(), "Failed to assert {$Stub}->isValid() was false");
        $this->assertEquals($error_message, $Stub->getValidationErrors('user_id')->first()->error);
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

        $error_message = "Value must be unsigned (a positive number)";

        $this->assertFalse($Stub->isValid(), "Failed to assert {$Stub}->isValid() was false");
        $this->assertEquals($error_message, $Stub->getValidationErrors('id')->first()->error);
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
        $password = 'password';
        $Stub = new Stub;
        $Stub->addValidationRule("after.string:$password", function(array $validators, string $value) use ($Stub) {
            $min_length = 8;
            $length = strlen($value);

            if ($length < $min_length) {
                $Stub->addValidationError("Password must be at least $min_length characters in length", $value, $validators);
            }

            preg_match("/[A-Z]+/", $value, $matches);
            if (count($matches) < 1) {
                $Stub->addValidationError("Password must have at least one capital letter", $value, $validators);
            }

            preg_match("/\d+/", $value, $matches);
            if (count($matches) < 1) {
                $Stub->addValidationError("Password must have at least one number", $value, $validators);
            }

            return $value;
        });
        $Stub->$password = '_';

        $PasswordValidationErrors = $Stub->getValidationErrors($password);

        $this->assertSame(3, $PasswordValidationErrors->count());
        $this->assertSame("Password must be at least 8 characters in length", $PasswordValidationErrors->first()->error);
        $this->assertSame("Password must have at least one capital letter", $PasswordValidationErrors->next()->error);
        $this->assertSame("Password must have at least one number", $PasswordValidationErrors->next()->error);
    }

    /**
     * Data provider: Non Float Values
     *
     * @return array
     */
    private function dataProvider__nonFloatValues(): array
    {
        return [
            [true],
            [false],
            [1],
            [0],
            [-1],
            ['1'],
            ['-1'],
            [new stdClass],
            [function() {
                return 1.00;
            }]
        ];
    }

    /**
     * Test Float Validator Captures Validation Errors
     *
     * @dataProvider dataProvider__nonFloatValues
     * 
     * @param mixed $value
     * @return void
     */
    public function testFloatValidatorCapturesValidationErrors(mixed $value): void
    {
        $float = 'float';

        $Stub = new Stub;
        $Stub->$float = $value;

        $error_message = "Type must be $float";
        $FloatValidationErrors = $Stub->getValidationErrors($float);

        $this->assertFalse($Stub->isValid(), "Failed to assert {$Stub}->isValid() was false");
        $this->assertSame($error_message, $FloatValidationErrors->first()->error);
    }
}
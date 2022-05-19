<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Test\Stub\Stub;

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

    /**
     * DataProvider: Irish Mobile Numbers
     *
     * @return array
     */
    private function dataProvider__irishMobileNumbers(): array
    {
        return [
            ["+3538123456789"],
            ["003538123456789"],
            ["+353 81 234 5678"],
            ["00353 81 234 5678"],
            ["+35381 234 5678"],
            ["0035381 234 5678"],
            ["+35381234 5678"],
            ["0035381234 5678"],
            ["08123456789"],
            ["081 234 5678"],
            ["081 234 5678"],
            ["081234 5678"],
        ];
    }

    /**
     * Test Match Validator Passes for valid strings
     *
     * @dataProvider dataProvider__irishMobileNumbers
     * 
     * @param string $value
     * 
     * @return void
     */
    public function testMatchValidatorPassesForValidStrings(string $value): void
    {
        $Stub = new Stub;
        $Stub->mobile = $value;

        $this->assertTrue($Stub->isValid(), "match() validator failed to match correct Irish mobile number");
    }

    /**
     * Data Provider: Invalid Irish Mobile Numbers
     *
     * @return array
     */
    private function dataProvider__invalidIrishMobileNumbers(): array
    {
        return [
            ["+3530858482765"],
            ["123 123 1234"]
        ];
    }

    /**
     * Test Match Validator Captures Validation Errors
     * 
     * @dataProvider dataProvider__invalidIrishMobileNumbers
     *
     * @param string $value
     * @return void
     */
    public function testMatchValidatorCapturesValidationErrors(string $value): void
    {
        $Stub = new Stub;
        $Stub->mobile = $value;

        $error_message = "Value did not match pattern";
        $FloatValidationErrors = $Stub->getValidationErrors('mobile');

        $this->assertFalse($Stub->isValid(), "Failed to assert {$Stub}->isValid() was false");
        $this->assertSame($error_message, $FloatValidationErrors->first()->error);
    }

    /**
     * Test Replace Correctly Replaces Matched String
     *
     * @return void
     */
    public function testReplaceCorrectlyReplacesMatchedString()
    {
        $Stub = new Stub;
        $Stub->cleaned_curse_words = "a curse word";

        $this->assertSame("a c&%!e word", $Stub->getCleanedCurseWords());
    }

    /**
     * Data Provider: Numeric Values
     *
     * @return array
     */
    private function dataProvider__numericValues(): array
    {
        return [
            ["42"],
            [1337],
            [02471],
            ["02471"],
            [9.1],
            [1337e0],
            ["1337e0"],
            [0x539],
            [0b10100111001]           
        ];
    }

    /**
     * Data Provider: Non-numeric Values
     *
     * @return array
     */
    private function dataProvider__nonNumericValues(): array
    {
        return [
            ["0x539"],
            ["not numeric"],
            [array()],
            [[]],
            [null],
            [true],
            [false],
            [new stdClass],
            [''],
            ["0b10100111001"],
            [function() {
                return 123;
            }],
        ];
    }

    /**
     * Test Numeric Validator Captures Valdiation Errors For Non-Numeric Values
     * 
     * @dataProvider dataProvider__nonNumericValues
     *
     * @param mixed $value
     * 
     * @return void
     */
    public function testNumericValidatorCapturesValdiationErrorsForNonNumericValues(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->numeric = $value;
        $type = gettype($value);
        $is_numeric = (is_numeric($value)) ? 'is numeric' : 'is not numeric';

        $this->assertFalse($Stub->isValid(), "Failed to assert \$Stub->isValid() was false for type $type which $is_numeric");
        $this->assertSame(1, $Stub->getValidationErrors('numeric')->count());
        $this->assertSame("Type must be numeric", $Stub->getValidationErrors('numeric')->first()->error);
    }

    /**
     * Test Numeric Validator is valid for numeric values
     * 
     * @dataProvider dataProvider__numericValues
     *
     * @param mixed $value
     * 
     * @return void
     */
    public function testNumericValidatorIsValidForNumericValues(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->numeric = $value;
        $type = gettype($value);
        $is_numeric = (is_numeric($value)) ? 'is numeric' : 'is not numeric';


        $this->assertTrue($Stub->isValid(), "Failed to assert \$Stub->isValid() for type $type which $is_numeric");
        $this->assertCount(0, $Stub->getValidationErrors());
    }

    /**
     * Data Provider: Alphabetical Values
     *
     * @return array
     */
    private function dataProvider__alphaValues(): array
    {
        return [
            ["abcdefghijklmnopqrstuvwxyz"],
            ["ABCDEFGHIJKLMNOPQRSTUVWXYZ"],
            ["abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"]
        ];
    }

    /**
     * Data Provider: Non-alphabetical Values
     *
     * @return array
     */
    private function dataProvider__nonAlphaValues(): array
    {
        return [
            ["1234567890"],
            ["!\"£$%^&*()_+-={}[]:@;'~#<,>.?/|\\`¬"],
            ["0x539"],
            [''],
            ["0b10100111001"]
        ];
    }

    /**
     * Test Alpha Validator Captures Valdiation Errors For Non-Alpha Values
     * 
     * @dataProvider dataProvider__nonAlphaValues
     *
     * @param mixed $value
     * 
     * @return void
     */
    public function testAlphaValidatorCapturesValdiationErrorsForNonAlphaValues(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->alpha = $value;
        $type = gettype($value);
        if ($type === 'string') {
            preg_match("/[a-zA-Z]+/", $value, $matches);
            $is_alpha = (!empty($matches)) ? 'is alpha' : 'is not alpha';
        } else {
            $is_alpha = 'is not alpha';
        }
        

        $this->assertFalse($Stub->isValid(), "Failed to assert \$Stub->isValid() was false for type $type which $is_alpha");
        $this->assertSame(1, $Stub->getValidationErrors('alpha')->count());
        $this->assertSame("Type must be alphabet characters only", $Stub->getValidationErrors('alpha')->first()->error);
    }

    /**
     * Test Alpha Validator is valid for alphabetic values
     * 
     * @dataProvider dataProvider__alphaValues
     *
     * @param mixed $value
     * 
     * @return void
     */
    public function testAlphaValidatorIsValidForAlphaValues(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->alpha = $value;
        $type = gettype($value);
        preg_match("/[a-zA-Z]+/", $value, $matches);
        $is_alpha = (!empty($matches)) ? 'is alpha' : 'is not alpha';


        $this->assertTrue($Stub->isValid(), "Failed to assert \$Stub->isValid() for type $type which $is_alpha");
        $this->assertCount(0, $Stub->getValidationErrors());
    }

    /**
     * Data Provider: Non-snakecase values
     * 
     * @returns array
     */
    private function dataProviderNonSnakeCaseValues(): array
    {
        return [
            ["kebab-case"],
            ["UPPER-KEBAB-CASE"],
            ["COBOL-CASE"],
            ["Camel_Snake_Case"],
            ["UPPER_SNAKE_CASE"],
            ["CONSTANT_CASE"],
            ["Train-Case"],
            ["camelCase"],
            ["UPPERFLATCASE"],
            ["flatcase"],
            ["snake__case_with_too_many_underscores"],
        ];
    }
    
    /**
     * Test: Snake Case Validator Captures Validation Errors
     * 
     * @dataProvider dataProviderNonSnakeCaseValues
     *
     * @param mixed $value
     *
     * @returns void
     */
    public function testSnakeCaseValdidatorCapturesValidationErrors(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->snakecase = $value;

        $this->assertFalse($Stub->isValid(), "Failed to assert \$Stub->isValid() returned false");
        $this->assertSame(1, $Stub->getValidationErrors('snakecase')->count());
        $this->assertSame("Value was not in snakecase (snake_case)", $Stub->getValidationErrors('snakecase')->first()->error);
    }

    /**
     * Data Provider: Snake Case Values
     * 
     * @returns array
     */
    private function dataProviderSnakeCaseValues(): array
    {
        return [
            ["snake_case"],
            ["i_am_a_long_snake_case_string"],
            ["i_am_a_long_snake_case_string_123"]
        ];
    }
    
    /**
     * Test: Snakecase Validator Passes For valid snakecase values
     * 
     * @dataProvider dataProviderSnakeCaseValues
     *
     * @param mixed
     *
     * @returns void
     */
    public function testSnakecaseValidatorPassesForValidSnakecaseValues(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->snakecase = $value;

        $this->assertTrue($Stub->isValid(), "Failed to assert \$Stub->isValid() returned false");
        $this->assertCount(0, $Stub->getValidationErrors());
    }

    /**
     * Data Provider: Non-kebabcase values
     * 
     * @returns array
     */
    private function dataProviderNonKebabCaseValues(): array
    {
        return [
            ["snake_case"],
            ["UPPER-KEBAB-CASE"],
            ["COBOL-CASE"],
            ["Camel_Snake_Case"],
            ["UPPER_SNAKE_CASE"],
            ["CONSTANT_CASE"],
            ["Train-Case"],
            ["camelCase"],
            ["UPPERFLATCASE"],
            ["flatcase"],
            ["snake__case_with_too_many_underscores"],
            ["kebab--case-with-too-many-underscores"],
        ];
    }
    
    /**
     * Test: Snake Case Validator Captures Validation Errors
     * 
     * @dataProvider dataProviderNonKebabCaseValues
     *
     * @param mixed $value
     *
     * @returns void
     */
    public function testKebabCaseValdidatorCapturesValidationErrors(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->kebabcase = $value;

        $this->assertFalse($Stub->isValid(), "Failed to assert \$Stub->isValid() returned false");
        $this->assertSame(1, $Stub->getValidationErrors('kebabcase')->count());
        $this->assertSame("Value was not in kebabcase (kebab-case)", $Stub->getValidationErrors('kebabcase')->first()->error);
    }

    /**
     * Data Provider: Kebab Case Values
     * 
     * @returns array
     */
    private function dataProviderKebabCaseValues(): array
    {
        return [
            ["kebab-case"],
            ["i-am-a-long-kebab-case-string"],
            ["i-am-a-long-snake-case-string-123"]
        ];
    }
    
    /**
     * Test: Kebabcase Validator Passes For valid snakecase values
     * 
     * @dataProvider dataProviderKebabCaseValues
     *
     * @param mixed
     *
     * @returns void
     */
    public function testKebabcaseValidatorPassesForValidSnakecaseValues(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->kebabcase = $value;

        $this->assertTrue($Stub->isValid(), "Failed to assert \$Stub->isValid() returned false");
        $this->assertCount(0, $Stub->getValidationErrors());
    }

    /**
     * Data Provider: Non upper snakecase values
     * 
     * @returns array
     */
    private function dataProviderNonUpperSnakeCaseValues(): array
    {
        return [
            ["kebab-case"],
            ["UPPER-KEBAB-CASE"],
            ["COBOL-CASE"],
            ["Camel_Snake_Case"],
            ["snake_case"],
            ["Train-Case"],
            ["camelCase"],
            ["UPPERFLATCASE"],
            ["flatcase"],
            ["snake__case_with_too_many_underscores"],
        ];
    }
    
    /**
     * Test: Upper Snake Case Validator Captures Validation Errors
     * 
     * @dataProvider dataProviderNonUpperSnakeCaseValues
     *
     * @param mixed $value
     *
     * @returns void
     */
    public function testUpperSnakeCaseValdidatorCapturesValidationErrors(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->uppersnakecase = $value;
        $Stub->macrocase = $value;
        $Stub->constantcase = $value;

        $this->assertFalse($Stub->isValid(), "Failed to assert \$Stub->isValid() returned false");

        $this->assertSame(1, $Stub->getValidationErrors('constantcase')->count());
        $this->assertSame(1, $Stub->getValidationErrors('uppersnakecase')->count());
        $this->assertSame(1, $Stub->getValidationErrors('macrocase')->count());
        
        $this->assertSame("Value was not in constantcase (CONSTANT_CASE)", $Stub->getValidationErrors('constantcase')->first()->error);
        $this->assertSame("Value was not in constantcase (CONSTANT_CASE)", $Stub->getValidationErrors('uppersnakecase')->first()->error);
        $this->assertSame("Value was not in constantcase (CONSTANT_CASE)", $Stub->getValidationErrors('macrocase')->first()->error);
        
    }

    /**
     * Data Provider: Upper Snake Case Values
     * 
     * @returns array
     */
    private function dataProviderUpperSnakeCaseValues(): array
    {
        return [
            ["UPPER_SNAKE_CASE"],
            ["MACRO_CASE"],
            ["CONSTANT_CASE"],
            ["I_AM_A_LONG_UPPER_SNAKE_CASE_STRING"],
            ["I_AM_A_LONG_UPPER_SNAKE_CASE_STRING_123"]
        ];
    }
    
    /**
     * Test: Upper Snakecase Validators Passes For valid upper snakecase values
     * 
     * @dataProvider dataProviderUpperSnakeCaseValues
     *
     * @param mixed
     *
     * @returns void
     */
    public function testUpperSnakecaseValidatorsPassesForValidSnakecaseValues(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->uppersnakecase = $value;
        $Stub->macrocase = $value;
        $Stub->constantcase = $value;

        $this->assertTrue($Stub->isValid(), "Failed to assert \$Stub->isValid() returned false");
        $this->assertCount(0, $Stub->getValidationErrors());
    }

    /**
     * Data Provider: Non Cobol case values
     * 
     * @returns array
     */
    private function dataProviderNonCobolCaseValues(): array
    {
        return [
            ["kebab-case"],
            ["Camel_Snake_Case"],
            ["snake_case"],
            ["Train-Case"],
            ["camelCase"],
            ["UPPERFLATCASE"],
            ["flatcase"],
            ["snake__case_with_too_many_underscores"],
        ];
    }
    
    /**
     * Test: COBOL Case Validator Captures Validation Errors
     * 
     * @dataProvider dataProviderNonCobolCaseValues
     *
     * @param mixed $value
     *
     * @returns void
     */
    public function testCobolCaseValdidatorCapturesValidationErrors(mixed $value): void
    {
        $Stub = new Stub;
        
        $Stub->cobolcase = $value;
        $Stub->upperkebabcase = $value;

        $this->assertFalse($Stub->isValid(), "Failed to assert \$Stub->isValid() returned false");

        $this->assertSame(1, $Stub->getValidationErrors('cobolcase')->count());
        $this->assertSame(1, $Stub->getValidationErrors('upperkebabcase')->count());
        
        
        $this->assertSame("Value was not in cobol case (COBOL-CASE)", $Stub->getValidationErrors('cobolcase')->first()->error);
        $this->assertSame("Value was not in cobol case (COBOL-CASE)", $Stub->getValidationErrors('upperkebabcase')->first()->error);
        
    }

    /**
     * Data Provider: Cobol Case Values
     * 
     * @returns array
     */
    private function dataProviderCobolCaseValues(): array
    {
        return [
            ["UPPER-KEBAB-CASE"],
            ["COBOL-CASE"],
            ["COBOL-CASE-123"]
        ];
    }
    
    /**
     * Test: Cobol Case Validators Passes For valid Cobol case values
     * 
     * @dataProvider dataProviderCobolCaseValues
     *
     * @param mixed
     *
     * @returns void
     */
    public function testCobolCaseValidatorsPassesForValidCobolcaseValues(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->cobolcase = $value;
        $Stub->upperkebabcase = $value;

        $this->assertTrue($Stub->isValid(), "Failed to assert \$Stub->isValid() returned false");
        $this->assertCount(0, $Stub->getValidationErrors());
    }

    /**
     * Data Provider: Non Camel case values
     * 
     * @returns array
     */
    private function dataProviderNonCamelCaseValues(): array
    {
        return [
            ["kebab-case"],
            ["Camel_Snake_Case"],
            ["snake_case"],
            ["Train-Case"],
            ["CamelCaps"],
            ["UPPERFLATCASE"],
            ["flatcase"],
            ["snake__case_with_too_many_underscores"],
        ];
    }
    
    /**
     * Test: Camel Case Validator Captures Validation Errors
     * 
     * @dataProvider dataProviderNonCamelCaseValues
     *
     * @param mixed $value
     *
     * @returns void
     */
    public function testCamelCaseValdidatorCapturesValidationErrors(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->camelcase = $value;

        $this->assertFalse($Stub->isValid(), "Failed to assert \$Stub->isValid() returned false");
        $this->assertSame(1, $Stub->getValidationErrors('camelcase')->count());
        
        
        $this->assertSame("Value is not in camel case (camelCase)", $Stub->getValidationErrors('camelcase')->first()->error);
    }

    /**
     * Data Provider: Camel Case Values
     * 
     * @returns array
     */
    private function dataProviderCamelCaseValues(): array
    {
        return [
            ["camelCase"],
            ["testSomeMethod"],
            ["reallyLongCamelCase"]
        ];
    }
    
    /**
     * Test: Camel Case Validators Passes For valid Camel case values
     * 
     * @dataProvider dataProviderCamelCaseValues
     *
     * @param mixed
     *
     * @returns void
     */
    public function testCamelCaseValidatorsPassesForValidCobolcaseValues(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->camelcase = $value;

        $this->assertTrue($Stub->isValid(), "Failed to assert \$Stub->isValid() returned false");
        $this->assertCount(0, $Stub->getValidationErrors());
    }

    /**
     * Data Provider: Non Pascal case values
     * 
     * @returns array
     */
    private function dataProviderNonPascalCaseValues(): array
    {
        return [
            ["kebab-case"],
            ["Camel_Snake_Case"],
            ["snake_case"],
            ["Train-Case"],
            ["camelCaps"],
            ["UPPERFLATCASE"],
            ["flatcase"],
            ["snake__case_with_too_many_underscores"],
        ];
    }
    
    /**
     * Test: Pascal Case Validator Captures Validation Errors
     * 
     * @dataProvider dataProviderNonPascalCaseValues
     *
     * @param mixed $value
     *
     * @returns void
     */
    public function testPascalCaseValdidatorCapturesValidationErrors(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->pascalcase = $value;
        $Stub->camelcaps = $value;
        $Stub->studlycaps = $value;

        $this->assertFalse($Stub->isValid(), "Failed to assert \$Stub->isValid() returned false");
        $this->assertSame(1, $Stub->getValidationErrors('pascalcase')->count());
        $this->assertSame(1, $Stub->getValidationErrors('camelcaps')->count());
        $this->assertSame(1, $Stub->getValidationErrors('studlycaps')->count());
        
        
        $this->assertSame("Value is not in Pascal case (PascalCase)", $Stub->getValidationErrors('pascalcase')->first()->error);
        $this->assertSame("Value is not in Pascal case (PascalCase)", $Stub->getValidationErrors('camelcaps')->first()->error);
        $this->assertSame("Value is not in Pascal case (PascalCase)", $Stub->getValidationErrors('studlycaps')->first()->error);
    }

    /**
     * Data Provider: Pascal Case Values
     * 
     * @returns array
     */
    private function dataProviderPascalCaseValues(): array
    {
        return [
            ["PascalCase"],
            ["ACatIsATerriblePet"],
            ["ADogIs100TimesBetterThanACat"]
        ];
    }
    
    /**
     * Test: Pascal Case Validators Passes For valid Pascal case values
     * 
     * @dataProvider dataProviderPascalCaseValues
     *
     * @param mixed
     *
     * @returns void
     */
    public function testPascalCaseValidatorsPassesForValidCobolcaseValues(mixed $value): void
    {
        $Stub = new Stub;
        $Stub->pascalcase = $value;
        $Stub->camelcaps = $value;
        $Stub->studlycaps = $value;

        $this->assertTrue($Stub->isValid(), "Failed to assert \$Stub->isValid() returned false");
        $this->assertCount(0, $Stub->getValidationErrors());
    }
}
# Affinity4/Validate

[![Affinity4](https://circleci.com/gh/affinity4/validate.svg?style=svg)](https://circleci.com/gh/affinity4/validate)

Affinity4 Validate is a trait which can be added to any class to enable protected/private property validation.

Once Affinity4 validate is added to a class, any private or protected property can have complex validation assiated by added the @validation docblock tag

```php
use Affinity4\Validate\Validate;

class ClassWithValidation
{
    use Validate;

    /**
     * @validation type(int:unsigned)
     */
    protected $id;

    /**
     * @validation type(string:alphanumeric)
     */
    protected $username;
}

$Validate = new ClassWithValidation;

$Validate->id = null
$Validate->username = 'not_alphanumeric123';

if (!$Validate->isValid()) {
    /*
    The validation errors are grouped by property...
    $validations_errors = [
        'id' => ValidationError([
            [
                "class" => "\ClassWithValidation",
                "property" => "id"
                "value" => null,
                "error" => "Must be of type string"
            ]
        ]),
        'username => ValidationError([
            [
                "class" => "\ClassWithValidation",
                "property" => "username",
                'value' => "not_alphanumeric123",
                'error' => "Must be alphanumeric (letters and numbers only)"
            ]
        ])
    ]
    */
    foreach ($Validate->getValidationErrors() as $ValidationErrors) {
        /*
        NOTE:
        $ValidationErrors is an instance of \Affinity4\Validate\ValidationErrors
        See section ValidationErrors object
        */
    }
}
```

## Why only private and protected properties?

Affinity4 Validate use the __set magic method to validate each property which has a @validation annotation. This means only private/protected properties can be validated.

## Validators

@validation strings have three parts:

type($type:$validator|$validator)

The $type validation will happen first. This will be a straight forward check of the type (e.g. is_int is_string etc)

The $validators are a pipe separated list of validations to happen after the $type validator, which the exception of cast, which is more like a before middleware to attempt to cast the value to the correct type before validation

### String

Validates property value is a string.
NOTE: Integers will not pass this validation. Castable types (e.g. objects with __toString) will not pass this validation unless 'cast' is passed as a validator. See Cast section below

| Value | Status |
| ----- | ------ |
| 123   | Fail   |
| '123' | Pass   |

```php
@validation type(string)
```

## Int

Validates property value is an integer.
NOTE: Numeric strings will not pass this validation. Castable types (e.g. a callable returning an int) will not pass this validation unless 'cast' is passed as a validator. See Cast section below

| Value | Status |
| ----- | ------ |
| '123' | Fail   |
| 123   | Pass   |

```php
@validation type(int)
```

### Numeric

Valdiates a value is numeric

| Value   | Status  |
| ------- | ------- |
| "0x539" | Fail    |
| "123"   | Pass    |

```php
/**
     * Numeric
     *
     * @validation type(numeric)
     *
     * @var numeric
     */
    protected $numeric;
```

### Alpha

**NOTE: Strings only**  
Validates a string is alphabet characters only (no numbers or symbols)

| Value    | Status  |
| -------- | ------- |
| "123"    | Fail    |
| "alpha!" | Fail    |
| "abcDEF" | Pass    |

```php
/**
     * Alpha
     *
     * @validation type(string:alpha)
     *
     * @var string
     */
    protected $alpha;
```

### Alnum/Alphanum/Alphanumeric

**NOTE: Strings only**  
Validates a string contains only alphanumeric characters (numbers and letters only)

| Value            | Status  |
| ---------------- | ------- |
| "i-am-not-alnum" | Fail    |
| "iAmAlnum123"    | Pass    |

```php
@validation type(string:alnum)
// or @validation type(string:alphanum)
// or @validation type(string:alphanumeric)
```

### snakecase

**NOTE: Strings only**  
Validates a string contains is snake_case

| Value        | Status |
| ------------ | ------ |
| "kebab-case" | Fail   |
| "snake_case" | Pass   |

```php
@validation type(string:snakecase)
```

### kebabcase

**NOTE: Strings only**  
Validates a string is kebab-case

| Value        | Status  |
| ------------ | ------- |
| "snake_case" | Fail    |
| "kebab-case" | Pass    |

```php
@validation type(string:kebabcase)
```

### constantcase/uppersnakecase

**NOTE: Strings only**  
Validates a string is CONSTANT_CASE (aka UPPER_SNAKE_CASE)

| Value           | Status  |
| --------------- | ------- |
| "snake_case"    | Fail    |
| "CONSTANT_CASE" | Pass    |

```php
@validation type(string:constantcase)
// or @validation type(string:uppersnakecase)
```

### camelcase

**NOTE: Strings only**  
Validates a string is camelCase

| Value           | Status  |
| --------------- | ------- |
| "snake_case"    | Fail    |
| "PascalCase"    | Fail    |
| "camelCase"     | Pass    |

```php
@validation type(string:camelcase)
```

### pascalcase/camelcaps/studlycaps/capitalcase

**NOTE: Strings only**  
Validates a string is PascalCase (aka CamelCaps, aka StudlyCaps, aka CapitalCase)

| Value           | Status  |
| --------------- | ------- |
| "snake_case"    | Fail    |
| "camelCase"     | Fail    |
| "PascalCase"    | Pass    |

```php
@validation type(string:pascalcase)
// or @validation type(string:camelcaps)
// or @validation type(string:studlycaps)
// or @validation type(string:capitalcase)
```

### traincase

**NOTE: Strings only**  
Validates a string is Train-Case

| Value           | Status  |
| --------------- | ------- |
| "COBOL-CASE"    | Fail    |
| "camelCase"     | Fail    |
| "Train-Case"    | Pass    |

```php
@validation type(string:traincase)
```

### Unsiged

Validates an integer is a positive value, above 0

| Value | Status  |
| ----- | ------- |
| -123  | Fail    |
| 123   | Pass    |

```php
@validation type(int:unsigned)
```

### Cast

**NOTE: Strings and Integers only**  
Will attempt to "cast" an invalid value to the correct type, if possible

This will check if an object has a __toString method, or will attempt to retreive return values of callables as the desired type.

**NOTE: Cast happens before any validation occurs. You can think of it more like a before middleware.**

#### Cast to int

`type(int:cast)`

| From  | To  |
| ----- | --- |
| '123' | 123 |

#### Cast to string

`type(string:cast)`

| From  | To      |
| ----- | ------- |
| false | 'false' |
| 123   | '123'   |

```php
/* 
 * @validation type(int:unsigned|cast)
 */
protected $id;

/* 
 * @validation type(string:alnum|cast)
 */
protected $username;

// ...

class User {
    // ...
    public function __toString()
    {
        return 'user' . $this->user_id;
    }
}

$Class->username = new User; // "user001";
```

### Match

**String only**  
Will match a vlaue based on the regex pattern provided

#### NOTES

1. Do not wrap pattern in quotes
2. Do not use regex delimiters. The default delimiter used internally is /. If you need to change this you should create a custom validation rule using the addValidationRule() method

```php
/**
 * Mobile
 * 
 * Matches an Irish mobile number:
 * +3538123456789
 * 003538123456789
 * +353 81 234 5678
 * 00353 81 234 5678
 * 
 * @validation match(^(?:0|(?:00|\+)353)\s*8\d{1}\s*\d{3}\s*\d{4})
 *
 * @var string
 */
protected $mobile;
```

### Replace

Will attempt to replace a matched pattern with a replacement string.

Notes:

1. Do not qoute pattern or replace strings
1. Do not use regex delimiters. The default delimiter used internally is /. If you need to change this you should create a custom validation rule using the addValidationRule() method
1. Uses preg_replace internally
1. You *CANNOT* pass an array as the replacement value. Only strings are allowed
1. You *CAN* use variable placeholders the same way as in preg_replace e.g. To encrypt a credit card number you would use replace((\d{4})\s*(\d{4})\s*(\d{4}), \*\*\*\* \*\*\*\* ${3}) // returns: \*\*\*\* \*\*\*\* 1234

```php
/**
 * Credit Card Number
 * 
 * Matches an a credit card number (e.g. 1234 1234 1234) and encrypts it (e.g **** **** 1234):
 * 
 * @validation replace((\d{4})\s*(\d{4})\s*(\d{4}), **** **** ${3})
 *
 * @var string
 */
protected $credit_card_number;
```

## ValidationErrors Class

Each group of errors is wrapped in the ValidationErrors class. This is to allow for easier accessing of specific errors and their keys than simply looping over the array of validation errors

For example imaginae a property which has multiple validations, like a password with custom validation added.

1. Validates the length must be greater than 8 characters
1. Validates there is at lease one capital letter
1. Validates there is at least one number

Without the ValidationErrors class the validations would be grouped in an array like so:

```php
[
    "password" => [
        [
            "class"     => "\Acme\ClassWithValidation",
            "property"  => "password",
            "value"     => "password",
            "error"     => "Password length must be greater than 8 characters"
        ],
        [
            "class"     => "\Acme\ClassWithValidation",
            "property"  => "password",
            "value"     => "password",
            "error"     => "Password must have at least one capital letter"
        ],
        [
            "class"     => "\Acme\ClassWithValidation",
            "property"  => "password",
            "value"     => "password",
            "error"     => "Password must have at least one number"
        ]
    ]
]
```

You would then have to do a loop, or check how many errors there are to even see if a loop is necessary, and you would still have to use indexes for all scenarios:

```php
$errors['password'][0]['error'] // Password length must be greater than 8 characters

$errors['password'][3]['error'] // Ooops: There's no index 3, but a very easy and common mistake to make

// Or what if you just wanted all the password errors?
$password_errors = []
foreach ($errors['password'] as $error)
{
    $password_errors[] = $error['error'];
}
```

With the ValidationErrors class containing the array, we have numerous helpful methods available to travers the array and get exactly what data we want without loops or array keys

```php
$password_errors = $Validate->getValidationErrors('password')->errors();
/*
$password_errors = [
    "Password length must be greater than 8 characters",
    "Password must have at least one capital letter",
    "Password must have at least one number"
]
*/
```

You can also easily navigate through the validation errors using the first(), next(), prev() and last() methods. These will expose the array items via properties (in place of the keys)

```php
$Stub->getValidationErrors('password')->count(); // 3
$PasswordValidationErrors = $Stub->getValidationErrors('password');
$PasswordValidationErrors->first()->error; // Password length must be greater than 8 characters
$PasswordValidationErrors->next()->error; // Password must have at least one capital letter
$PasswordValidationErrors->prev()->error; // Password length must be greater than 8 characters
$PasswordValidationErrors->last()->error; // Password must have at least one number

// We also can get class, property and value
$PasswordValidationErrors->first()->class; // \Acme\Validate
$PasswordValidationErrors->first()->property; // 'password'
$PasswordValidationErrors->first()->value; // 'password'
```

**IMPORTANT**: We must store the ValidationError instance in a variable to use first, next, prev and last methods, since getValidationErrors($key) will return a new instance of ValidationErrors each time it is called

## TODO

1. Add type(string:traincase) Train-Case
1. Add type(string:uppercase)
1. Add type(string:lowercase)
1. Add type(string:flatcase)
1. Add type(string:upperflatcase)
1. Add type(string:hex) validator. Validates string is a hexadecimal value. ctype_xdigit($value)
1. Add type(string:no_whitespace) validator. Validates string has no whitespace (e.g. \r\n\t). !ctype_space($value) && ctype_print($value)
1. Add to(snakecase)
1. Add to(kebabcase)
1. Add to(constantcase) aka macrocase/uppersnakecase
1. Add to(macrocase) aka constantcase/uppersnakecase
1. Add to(uppersnakecase) aka constantcase/macrocase
1. Add to(cobolcase) aka upperkebabcase
1. Add to(upperkebabcase) aka cobolcase
1. Add to(camelcase)
1. Add to(camelcaps) aka pascalcase
1. Add to(pascalcase) aka camelcaps
1. Add to(uppercase)
1. Add to(lowercase)
1. Add to(flatcase)
1. Add to(upperflatcase)
1. Allow multiple validations to pass e.g. type(string:any(kebabcase, snakecase)). NOTE: validation "functions" (e.g. regex($pattern)) are not allowed inside any. Custom validators should instead be created using addValidationRule() and the name should be used inside any()
1. Allow any() to be used to allow multiple valid types e.g. type(any(string,int,null)). NOTE: No additioanl validators can be used in this case e.g. type(any(string,null):cast|kebabcase) since allowing multiple types could complex validation scenarios with potentially unexpected results
1. Add chaining/fluent interface  

    ```php
    @validation type(string:cast|kebabcase).to(snakecase) // Fails if not a string formatted as kebabcase. Otherwise converts kebabcase to snakecase (e.g. "i-am-a-kebab" to "i_am_a_kebab")
    // or
    @validation match(\s+).to(lowercase|snakecase) // Fails if not a sentence (no spaces found). Otherwise, converts sentences to lowercase snakecase (e.g. "I Am A Title" to "i_am_a_title")
    // or
    @validation match(\d{4}\s*\s{4}\s*\d{4}).replace((\d{4})\s*(\d{4})\s*(\d{4}), **** **** ${3}) // Fails if not a credit card number. Otherwise, encrypts it (e.g. **** **** 1234)
    ```

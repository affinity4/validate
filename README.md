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

type(<type>:<validator>|<validator>)

The <type> validation will happen first. This will be a straight forward check of the type (e.g. is_int is_string etc)

The <validators> are a pipe separated list of validations to happen after the <type> validator, which the exception of cast, which is more like a before middleware to attempt to cast the value to the correct type before validation

### String

Validates property value is a string. 
NOTE: Integers will not pass this validation. Castable types (e.g. objects with __toString) will not pass this validation unless 'cast' is passed as a validator. See Cast section below

123 = fail
'123' = pass

```php
@validation type(string)
```

## Int

Validates property value is an integer. 
NOTE: Numeric strings will not pass this validation. Castable types (e.g. a callable returning an int) will not pass this validation unless 'cast' is passed as a validator. See Cast section below

'123' = fail
123 = pass

```php
@validation type(int)
```

### Alnum/Alphanum/Alphanumeric

**Strings only**

Validates a string contains only alphanumeric characters (numbers and letters only)

i-am-not-alnum = fail
iAmAlnum123 = pass

```php
@validation type(string:alnum)
// or @validation type(string:alphanum)
// or @validation type(string:alphanumeric)
```

### Unsiged

Validates an integer is a positive value, above 0 

-123 = fail
123 = pass

```php
@validation type(int:unsigned)
```

### Cast

**Strings and Integers**

Will attempt to "cast" an invalid value to the correct type, if possible

This will check if an object has a __toString method, or will attempt to retreive return values of callables as the desired type.

NOTE: Cast happens before any validation occurs. You can think of it more like a before middleware.

type(int:cast)
'123' -> 123

type(string:cast)
false -> 'false'
123 -> '123'

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

## ValidationErrors Class

Each group of errors is wrapped in the ValidationErrors class. This is to allow for easier accessing of specific errors and their keys than simply looping over the array of validation errors

For example imaginae a property which has multiple validations, like a password with custom validation added.
1. Validates the length must be greater than 8 characters
2. Validates there is at lease one capital letter
3. Validates there is at least one number

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

1. Add regex(/<pattern>/) to allow regex patterns to be used for validation
3. Add type(string:snakecase), type(string:kebabcase), type(string:camelcase), type(string:pascalcase), type(string:uppercase), type(string:lowercase)
4. Add to(snakecase), to(kebabcase), to(camelcase), to(pascalcase), to(uppercase), to(lowercase)
5. Add chaining/fluent interface 
    ```php
    @validation type(string:cast|kebabcase)->to(snakecase) // converts "i-am-a-kebab" to "i_am_a_kebab"
    //or
    @validation regex(/\s+/)->to(lowercase|snakecase) // converts "I am a sentence" to "i_am_a_sentence"
    ```
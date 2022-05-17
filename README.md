# Affinity4/Validate

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

if (!$Validate->isValid()) {
    foreach ($Validate->getValidationErrors() as $validation_error) {
        // do something
    }
}
```

## Why only private and protected properties?

Affinity4 Validate use the __set magic method to validate each property with an @validation annotation. This means only private/protected properties can be validated.

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

## TODO

1. Add type(integer). Same behaviour as type(int)
2. Add type(float, 2), type(decimal, 2), type(double, 2) where number is the allowed decimal places
3. Add regex(/<pattern>/) to allow regex patterns to be used for validation
4. Add type(string:snakecase), type(string:kebabcase), type(string:camelcase), type(string:pascalcase), type(string:uppercase), type(string:lowercase)
5. Add to(snakecase), to(kebabcase), to(camelcase), to(pascalcase), to(uppercase), to(lowercase)
6. Add chaining/fluent interface 
```php
@validation type(string:cast|kebabcase)->to(snakecase) // converts "i-am-a-kebab" to "i_am_a_kebab"
//or
@validation regex(/\s+/)->to(lowercase|snakecase) // converts "I am a sentence" to "i_am_a_sentence"
```
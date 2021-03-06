<?php
namespace Test\Stub;

use Affinity4\Validate\Validate;

class Stub
{
    use Validate;

    /**
     * id field
     *
     * @validation type(int:unsigned|cast)
     * 
     * @var int
     */
    protected $id;

    /**
     * int
     * 
     * @validation type(int)
     *
     * @var int
     */
    protected $int;

    /**
     * float
     * 
     * @validation type(float, 2)
     *
     * @var float
     */
    protected $float;

    /**
     * user_id
     * 
     * @validation type(int:cast)
     *
     * @var int
     */
    protected $user_id;

    /**
     * name
     * 
     * @validation type(string:alnum|cast, 255)
     *
     * @var string
     */
    protected $username;

    /**
     * password
     * 
     * @validation type(string:password)
     *
     * @var string
     */
    protected $password;

    /**
     * String
     *
     * @validation type(string)
     * 
     * @var string
     */
    protected $string;

    /**
     * Cast to string
     *
     * @validation type(string:cast)
     * 
     * @var string
     */
    protected $cast_to_string;

    /**
     * alnum
     * 
     * @validation type(string:alnum)
     *
     * @var string
     */
    protected $alnum;

    /**
     * alphanum
     * 
     * @validation type(string:alphanum)
     *
     * @var string
     */
    protected $alphanum;

    /**
     * alphanumeric
     * 
     * @validation type(string:alphanumeric)
     *
     * @var string
     */
    protected $alphanumeric;

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

    /**
     * Replace
     * 
     * Finds any "curse" and replaces the "urs" with "&%!"
     * 
     * @validation replace((c)(?:urs)(e), ${1}&%!${2})
     */
    protected $cleaned_curse_words;

    /**
     * Numeric
     *
     * @validation type(numeric)
     *
     * @var numeric
     */
    protected $numeric;

    /**
     * Alpha
     *
     * @validation type(string:alpha)
     *
     * @var string
     */
    protected $alpha;

    /**
     * Snakecase
     *
     * @validation type(string:snakecase)
     * 
     * @var string
     */
    protected $snakecase;

    /**
     * Constant case
     *
     * @validation type(string:constantcase)
     * 
     * @var string
     */
    protected $constantcase;

    /**
     * Macro case
     *
     * @validation type(string:macrocase)
     * 
     * @var string
     */
    protected $macrocase;

    /**
     * Upper Snakecase
     *
     * @validation type(string:uppersnakecase)
     * 
     * @var string
     */
    protected $uppersnakecase;

    /**
     * Kebabcase
     *
     * @validation type(string:kebabcase)
     * 
     * @var string
     */
    protected $kebabcase;

    /**
     * Cobol Case
     *
     * @validation type(string:cobolcase)
     * 
     * @var string
     */
    protected $cobolcase;

    /**
     * Upper kebab case
     *
     * @validation type(string:upperkebabcase)
     * 
     * @var string
     */
    protected $upperkebabcase;

    /**
     * Camelcase
     *
     * @validation type(string:camelcase)
     * 
     * @var string
     */
    protected $camelcase;

    /**
     * PascalCase
     *
     * @validation type(string:pascalcase)
     * 
     * @var string
     */
    protected $pascalcase;

    /**
     * StudlyCaps
     *
     * @validation type(string:studlycaps)
     * 
     * @var string
     */
    protected $studlycaps;

    /**
     * CamelCaps
     *
     * @validation type(string:camelcaps)
     * 
     * @var string
     */
    protected $camelcaps;

    /**
     * CapitalCase
     *
     * @validation type(string:capitalcase)
     * 
     * @var string
     */
    protected $capitalcase;

    /**
    * Train-Case
    *
    * @validation type(string:traincase)
    * 
    * @var string
    */
   protected $traincase;

    /**
     * Get ID
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get User ID
     *
     * @return integer
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * Get String
     *
     * @return string
     */
    public function getString(): string
    {
        return $this->string;
    }

    /**
     * Get Cast to String
     *
     * @return string
     */
    public function getCastToString(): string
    {
        return $this->cast_to_string;
    }

    /**
     * get alnum
     *
     * @return string
     */
    public function getAlnum(): string
    {
        return $this->alnum;
    }

    /**
     * Get Cleaned Curse Words
     *
     * @return string
     */
    public function getCleanedCurseWords(): string
    {
        return $this->cleaned_curse_words;
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'a string';
    }
}
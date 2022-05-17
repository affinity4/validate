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
     * __toString
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'a string';
    }
}
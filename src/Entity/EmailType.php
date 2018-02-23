<?php
namespace KiwiSuite\CommonTypes\Entity;

use Assert\Assertion;
use KiwiSuite\Entity\Type\Convert\Convert;
use KiwiSuite\Entity\Type\TypeInterface;

final class EmailType implements TypeInterface
{
    /**
     * @var string
     */
    private $value;

    /**
     * EmailType constructor.
     * @param string $value
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(string $value)
    {
        Assertion::email($value);
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function convertToInternalType($value)
    {
        return Convert::convertString($value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return (string)$this;
    }
}

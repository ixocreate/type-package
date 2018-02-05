<?php
namespace KiwiSuite\CommonTypes\Entity;

use Assert\Assertion;
use KiwiSuite\Entity\Type\Convert\Convert;
use KiwiSuite\Entity\Type\TypeInterface;
use Ramsey\Uuid\Uuid;

final class UuidType implements TypeInterface
{
    /**
     * @var Uuid
     */
    private $uuid;

    public function __construct(string $value)
    {
        Assertion::uuid($value);

        $this->uuid = Uuid::fromString($value);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->uuid->toString();
    }

    /**
     * @return string
     */
    public static function getInternalType(): string
    {
        return TypeInterface::TYPE_STRING;
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function convertToInternalType($value)
    {
        return Convert::convertString($value);
    }

    public function __toString()
    {
        return $this->uuid->toString();
    }
}

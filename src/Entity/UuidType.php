<?php
/**
 * kiwi-suite/entity (https://github.com/kiwi-suite/common-types)
 *
 * @package kiwi-suite/common-types
 * @see https://github.com/kiwi-suite/common-types
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
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

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return (string)$this;
    }
}

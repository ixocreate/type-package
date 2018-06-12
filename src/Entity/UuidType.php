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
use Doctrine\DBAL\Types\GuidType;
use KiwiSuite\Contract\Type\DatabaseTypeInterface;
use KiwiSuite\Entity\Type\AbstractType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class UuidType extends AbstractType implements DatabaseTypeInterface
{

    /**
     * @param $value
     * @return mixed|\Ramsey\Uuid\UuidInterface
     * @throws \Assert\AssertionFailedException
     */
    protected function transform($value)
    {
        if ($value instanceof UuidInterface) {
            return $value;
        }

        Assertion::uuid($value);

        return Uuid::fromString($value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value()->toString();
    }

    /**
     * @return string
     */
    public function convertToDatabaseValue()
    {
        return (string) $this;
    }

    /**
     * @return string
     */
    public static function baseDatabaseType(): string
    {
        return GuidType::class;
    }
}

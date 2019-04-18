<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Package\Type\Entity;

use Assert\Assertion;
use Doctrine\DBAL\Types\GuidType;
use Ixocreate\Package\Type\DatabaseTypeInterface;
use Ixocreate\Package\Entity\Type\AbstractType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class UuidType extends AbstractType implements DatabaseTypeInterface
{
    /**
     * @param $value
     * @throws \Assert\AssertionFailedException
     * @return mixed|\Ramsey\Uuid\UuidInterface
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

    public static function serviceName(): string
    {
        return 'uuid';
    }
}

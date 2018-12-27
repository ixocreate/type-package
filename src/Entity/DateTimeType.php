<?php
/**
 * kiwi-suite/common-types (https://github.com/kiwi-suite/common-types)
 *
 * @package kiwi-suite/common-types
 * @link https://github.com/kiwi-suite/common-types
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace Ixocreate\CommonTypes\Entity;

use Ixocreate\Contract\Schema\ElementInterface;
use Ixocreate\Contract\Type\DatabaseTypeInterface;
use Ixocreate\Contract\Type\SchemaElementInterface;
use Ixocreate\Entity\Type\AbstractType;
use Ixocreate\Schema\Elements\DateTimeElement;
use Ixocreate\Schema\ElementSubManager;

final class DateTimeType extends AbstractType implements DatabaseTypeInterface, SchemaElementInterface
{
    /**
     * @param $value
     * @throws \Exception
     * @return \DateTimeImmutable
     */
    protected function transform($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return new \DateTimeImmutable('@' . $value->getTimestamp());
        }

        if (\is_string($value)) {
            $value = \strtotime($value);
        }

        if (\is_int($value)) {
            return new \DateTimeImmutable('@' . $value);
        }

        throw new \Exception("invalid date format");
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return ($this->value() === null) ? null : $this->value()->format('c');
    }

    /**
     * @return string
     */
    public function convertToDatabaseValue()
    {
        return $this->value();
    }

    /**
     * @return string
     */
    public static function baseDatabaseType(): string
    {
        return \Doctrine\DBAL\Types\DateTimeType::class;
    }

    public function schemaElement(ElementSubManager $elementSubManager): ElementInterface
    {
        return $elementSubManager->get(DateTimeElement::class);
    }

    public static function serviceName(): string
    {
        return 'datetime';
    }
}

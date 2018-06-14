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

use KiwiSuite\Contract\Schema\ElementInterface;
use KiwiSuite\Contract\Type\DatabaseTypeInterface;
use KiwiSuite\Contract\Type\SchemaElementInterface;
use KiwiSuite\Entity\Type\AbstractType;
use KiwiSuite\Schema\Elements\DateTimeElement;
use KiwiSuite\Schema\ElementSubManager;

final class DateTimeType extends AbstractType implements DatabaseTypeInterface, SchemaElementInterface
{
    /**
     * @param $value
     * @return \DateTimeImmutable
     * @throws \Exception
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
        return $this->value()->format('c');
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
}

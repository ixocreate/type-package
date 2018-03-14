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

use KiwiSuite\Entity\Type\TypeInterface;

final class DateType implements TypeInterface
{
    /**
     * @var \DateTimeImmutable
     */
    private $dateTime;

    /**
     * DateTimeType constructor.
     * @param \DateTimeInterface $value
     */
    public function __construct(\DateTimeInterface $value)
    {
        $this->dateTime = new \DateTimeImmutable('@' . $value->getTimestamp());
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getValue()
    {
        return $this->dateTime;
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function convertToInternalType($value)
    {
        if (\is_string($value)) {
            $value = \strtotime($value);
        }

        if (\is_int($value)) {
            $value = new \DateTimeImmutable('@' . $value);
        }

        return $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->dateTime->format('Y-m-d');
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return (string)$this;
    }
}

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

use KiwiSuite\Entity\Type\Convert\Convert;
use KiwiSuite\Entity\Type\TypeInterface;

final class ColorType implements TypeInterface
{
    private $color;

    public function __construct(string $value)
    {
        if (\mb_substr($value, 0, 1) !== '#') {
            //TODO Exception
            throw new \Exception("invalid hex color");
        }
        $value = \mb_substr($value, 1);

        if (\mb_strlen($value) === 3) {
            $value = $value . $value;
        }

        if (\mb_strlen($value) !== 6) {
            //TODO Exception
            throw new \Exception("invalid hex color");
        }

        if (!\preg_match('/^[a-f0-9]{6}$/i', $value)) {
            //TODO Exception
            throw new \Exception("invalid hex color");
        }

        $this->color = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return '#' . $this->color;
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
        return (string)$this->getValue();
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->getValue();
    }

    public function toRgb()
    {
        return [
            'r' => \hexdec($this->color[0] . $this->color[1]),
            'g' => \hexdec($this->color[2] . $this->color[3]),
            'b' => \hexdec($this->color[4] . $this->color[5]),
        ];
    }
}

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

use Doctrine\DBAL\Types\StringType;
use KiwiSuite\Contract\Type\DatabaseTypeInterface;
use KiwiSuite\Entity\Type\AbstractType;

final class ColorType extends AbstractType implements DatabaseTypeInterface
{
    /**
     * @param $value
     * @return string
     * @throws \Exception
     */
    protected function transform($value)
    {
        if (!\is_string($value)) {
            throw new \Exception("invalid hex color");
        }

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

        return '#' . $value;
    }

    /**
     * @return array
     */
    public function toRgb()
    {
        return [
            'r' => \hexdec($this->value[1] . $this->value[2]),
            'g' => \hexdec($this->value[3] . $this->value[4]),
            'b' => \hexdec($this->value[5] . $this->value[6]),
        ];
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
        return StringType::class;
    }
}

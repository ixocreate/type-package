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

namespace KiwiSuite\CommonTypes\Entity;

use Doctrine\DBAL\Types\JsonType;
use KiwiSuite\Contract\Schema\ElementInterface;
use KiwiSuite\Contract\Type\DatabaseTypeInterface;
use KiwiSuite\Contract\Type\SchemaElementInterface;
use KiwiSuite\Entity\Type\AbstractType;
use KiwiSuite\Schema\ElementSubManager;

final class PriceType extends AbstractType implements DatabaseTypeInterface, SchemaElementInterface
{
    /**
     * @param $value
     * @return mixed
     */
    protected function transform($value)
    {
        $default = [
            'currency' => null,
            'price' => null,
        ];
        if (!\is_array($value)) {
            return $default;
        }

        if (!\array_key_exists('currency', $value) || !\array_key_exists('price', $value)) {
            return $default;
        }

        $default['currency'] = (string) $value['currency'];
        $default['price'] = (float) $value['price'];

        return $default;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (empty($this->value())) {
            return "";
        }
        return (string) $this->value()['price'];
    }

    public function jsonSerialize()
    {
        return $this->value();
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
        return JsonType::class;
    }

    public static function serviceName(): string
    {
        return 'price';
    }

    public function schemaElement(ElementSubManager $elementSubManager): ElementInterface
    {
        // TODO: Implement schemaElement() method.
    }
}

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

use KiwiSuite\Entity\Type\AbstractType;

final class CollectionType extends AbstractType
{
    /**
     * @param $value
     * @return mixed
     */
    protected function transform($value)
    {
        $newValue = [];

        if (!is_array($value)) {
            return $newValue;
        }
        
        foreach ($value as $item) {
            if (empty($item['_type'])) {
                continue;
            }

            if (!$this->hasSet($item['_type'])) {
                continue;
            }

            $newValue[] = $item;
        }

        return $newValue;
    }

    public function sets(): array
    {
        return [];
    }

    public function hasSet(string $name): bool
    {
        return false;
    }

    public function __toString()
    {
        return "";
    }
}

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
use KiwiSuite\Contract\Schema\SchemaInterface;
use KiwiSuite\Contract\Type\DatabaseTypeInterface;
use KiwiSuite\Entity\Type\AbstractType;
use KiwiSuite\Entity\Type\Type;

final class CollectionType extends AbstractType implements DatabaseTypeInterface, \Iterator
{
    /**
     * @return SchemaInterface|null
     */
    private function getSchema(): ?SchemaInterface
    {
        if (empty($this->options['schema'])) {
            return null;
        }

        return $this->options['schema'];
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function transform($value)
    {
        $result = [];
        if (!\is_array($value) || empty($value)) {
            return $result;
        }

        foreach ($value as $item) {
            if (empty($item['_type'])) {
                continue;
            }

            if (!$this->getSchema()->has($item['_type'])) {
                continue;
            }

            //unset($item['_type']);
            $result[] = [
                '_type' => $item['_type'],
                'value' => Type::create($item, SchemaType::class, ['schema' => $this->getSchema()->get($item['_type'])]),
            ];
        }

        return $result;
    }

    public function __toString()
    {
        return "";
    }

    public static function serviceName(): string
    {
        return 'collection';
    }

    public function jsonSerialize()
    {
        $return = [];
        foreach ($this->value() as $name => $value) {
            $return[$name] = \array_merge(
                ['_type' => $value['_type']],
                $value['value']->value()
            );
        }
        return $return;
    }

    public function convertToDatabaseValue()
    {
        $values = [];

        foreach ($this->value() as $name => $val) {
            if ($val['value'] instanceof DatabaseTypeInterface) {
                $values[$name] = [
                    '_type' => $val['_type'],
                    'value' => $val['value']->convertToDatabaseValue(),
                ];
                continue;
            }

            $values[$name] = $val;
        }

        return $values;
    }

    public function __debugInfo()
    {
        return [
            'value' => $this->value(),
        ];
    }

    public static function baseDatabaseType(): string
    {
        return JsonType::class;
    }

    /**
     * @return mixed
     */
    public function current()
    {
        $value = \current($this->value);

        return $value['value'];
    }

    /**
     *
     */
    public function next()
    {
        \next($this->value);
    }

    /**
     * Return the key of the current element
     * @see http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure
     * @since 5.0.0
     */
    public function key()
    {
        return \key($this->value);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        $key = \key($this->value);
        return $key !== null && $key !== false;
    }

    /**
     *
     */
    public function rewind()
    {
        \reset($this->value);
    }
}

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
use KiwiSuite\Contract\Schema\SubSchemaReceiverInterface;
use KiwiSuite\Contract\Type\DatabaseTypeInterface;
use KiwiSuite\Entity\Type\AbstractType;
use KiwiSuite\Entity\Type\Type;
use KiwiSuite\Schema\Builder;
use KiwiSuite\ServiceManager\ServiceManager;

final class CollectionType extends AbstractType implements DatabaseTypeInterface, \Iterator
{
    /**
     * @var SchemaInterface
     */
    private $schema;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @var Builder
     */
    private $builder;

    /**
     * CollectionType constructor.
     * @param ServiceManager $serviceManager
     * @param Builder $builder
     */
    public function __construct(ServiceManager $serviceManager, Builder $builder)
    {
        $this->serviceManager = $serviceManager;
        $this->builder = $builder;
    }

    /**
     * @return SchemaInterface|null
     */
    private function getSchema(): ?SchemaInterface
    {
        if (!empty($this->schema)) {
            return $this->schema;
        }

        if (!empty($this->options['schema'])) {
            //TODO check instance of
            $this->schema = $this->options['schema'];
            return $this->schema;
        }

        if (!empty($this->options['subSchema'])) {

            //TODO this is a dirty way of receiving the service
            $receiver = null;
            if ($this->serviceManager->has($this->options['subSchema'])) {
                $receiver = $this->serviceManager->get($this->options['subSchema']);
            }

            if (empty($receiver)) {
                foreach (\array_keys($this->serviceManager->getServiceManagerConfig()->getSubManagers()) as $subManager) {
                    if ($this->serviceManager->get($subManager)->has($this->options['subSchema'])) {
                        $receiver = $this->serviceManager->get($subManager)->get($this->options['subSchema']);
                        break;
                    }
                }
            }

            if (! ($receiver instanceof SubSchemaReceiverInterface)) {
                throw new \Exception($this->options['subSchema'] . " must implement " . SubSchemaReceiverInterface::class);
            }

            $this->schema = $receiver->receiveSchema($this->options['subSchemaName'], $this->builder);
            return $this->schema;
        }

        //TODO Exception
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

        if (array_key_exists('__values__', $value) && array_key_exists('__options__', $value)) {
            if (array_key_exists('subSchema', $value['__options__'])) {
                $this->options['subSchema'] = $value['__options__']['subSchema'];
            }
            if (array_key_exists('subSchemaName', $value['__options__'])) {
                $this->options['subSchemaName'] = $value['__options__']['subSchemaName'];
            }

            $value = $value['__values__'];
        }

        foreach ($value as $item) {
            if (empty($item['_type'])) {
                continue;
            }

            if (!$this->getSchema()->has($item['_type'])) {
                continue;
            }

            $type = $item['_type'];

            // HACK
            if (array_key_exists('_type', $item) && array_key_exists('value', $item) && count($item) == 2) {
                $item = $item['value'];
            }

            unset($item['_type']);
            $result[] = [
                '_type' => $type,
                'value' => Type::create($item, SchemaType::class, ['schema' => $this->getSchema()->get($type)]),
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
        $options = [];
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

        if (!empty($this->options['subSchema']) && !empty($this->options['subSchemaName'])) {
            $options['subSchema'] = $this->options['subSchema'];
            $options['subSchemaName'] = $this->options['subSchemaName'];
        }

        return [
            '__options__' => $options,
            '__values__' => $values,
        ];
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

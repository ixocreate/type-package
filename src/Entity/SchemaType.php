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

use Doctrine\DBAL\Types\JsonType;
use KiwiSuite\Contract\Schema\ElementInterface;
use KiwiSuite\Contract\Schema\SchemaInterface;
use KiwiSuite\Contract\Schema\SchemaReceiverInterface;
use KiwiSuite\Contract\Schema\TransformableInterface;
use KiwiSuite\Contract\Type\DatabaseTypeInterface;
use KiwiSuite\Contract\Type\TypeInterface;
use KiwiSuite\Entity\Entity\Definition;
use KiwiSuite\Entity\Entity\DefinitionCollection;
use KiwiSuite\Entity\Type\AbstractType;
use KiwiSuite\Schema\Builder;
use KiwiSuite\ServiceManager\ServiceManager;

final class SchemaType extends AbstractType implements DatabaseTypeInterface
{
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @var array|null
     */
    private $receiver;
    /**
     * @var Builder
     */
    private $builder;

    public function __construct(ServiceManager $serviceManager, Builder $builder)
    {
        $this->serviceManager = $serviceManager;
        $this->builder = $builder;
    }


    public function create($value, array $options = []): TypeInterface
    {
        $receiver = null;

        if (isset($value['__receiver__']) && isset($value['__value__'])) {
            $receiver = $value['__receiver__'];
            $value = $value['__value__'];
        }
        $type = clone $this;
        $type->options = $options;
        $type->receiver = $receiver;

        if (empty($type->getSchema())) {
            $type->receiveSchema();
        }

        if (empty($type->getSchema())) {
            throw new \Exception("Cant initialize without schema");
        }

        $type->value = $type->transform($value);

        return $type;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function transform($value)
    {
        if (!is_array($value) || empty($value)) {
            return [];
        }

        $definitions = [];
        $entityData = [];

        /** @var ElementInterface $element */
        foreach ($this->getSchema()->elements() as $element) {
            $definitions[] = new Definition($element->name(), $element->type(), true, true);
            $entityData[$element->name()] = null;
            if (is_array($value) && \array_key_exists($element->name(), $value)) {
                $entityData[$element->name()] = $value[$element->name()];
            }

            if ($element instanceof TransformableInterface) {
                $entityData[$element->name()] = $element->transform($entityData[$element->name()]);
            }
        }

        return (new \KiwiSuite\Schema\Entity\Schema($entityData, new DefinitionCollection($definitions)))->toArray();
    }

    private function getSchema(): ?SchemaInterface
    {
        if (empty($this->options['schema'])) {
            return null;
        }

        return $this->options['schema'];
    }

    /**
     * @throws \Exception
     */
    private function receiveSchema(): void
    {
        if (empty($this->receiver) || empty($this->receiver['receiver']) || empty($this->receiver['options'])) {
            return;
        }

        $receiver = $this->serviceManager->get($this->receiver['receiver']);

        if (! ($receiver instanceof SchemaReceiverInterface)) {
            throw new \Exception("receiver must implement " . SchemaReceiverInterface::class);
        }

        $this->options['schema'] = $receiver->receiveSchema($this->builder, $this->receiver['options']);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "";
    }

    public function jsonSerialize()
    {
        return $this->value();
    }

    /**
     * @return array
     */
    public function convertToDatabaseValue()
    {
        return [
            '__receiver__'  => $this->receiver,
            '__value__' => $this->value()
        ];
    }

    /**
     * @return string
     */
    public static function baseDatabaseType(): string
    {
        return JsonType::class;
    }
}

<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\CommonTypes\Entity;

use Doctrine\DBAL\Types\JsonType;
use Ixocreate\Contract\Schema\ElementInterface;
use Ixocreate\Contract\Schema\SchemaInterface;
use Ixocreate\Contract\Schema\SchemaReceiverInterface;
use Ixocreate\Contract\Schema\TransformableInterface;
use Ixocreate\Contract\Type\DatabaseTypeInterface;
use Ixocreate\Contract\Type\TypeInterface;
use Ixocreate\Entity\Entity\Definition;
use Ixocreate\Entity\Entity\DefinitionCollection;
use Ixocreate\Entity\Type\AbstractType;
use Ixocreate\Schema\Builder;
use Ixocreate\ServiceManager\ServiceManager;

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

        if (\array_key_exists('__receiver__', $value) && \array_key_exists('__value__', $value)) {
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
        $type->extractReceiver();
        $type->value = $type->transform($value);

        return $type;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function transform($value)
    {
        if (!\is_array($value) || empty($value)) {
            return [];
        }

        $definitions = [];
        $entityData = [];

        /** @var ElementInterface $element */
        foreach ($this->getSchema()->all() as $element) {
            $definitions[] = new Definition($element->name(), $element->type(), true, true);
            $entityData[$element->name()] = null;
            if (\array_key_exists($element->name(), $value)) {
                $entityData[$element->name()] = $value[$element->name()];
            }

            if ($element instanceof TransformableInterface) {
                $entityData[$element->name()] = $element->transform($entityData[$element->name()]);
            }
        }

        return (new \Ixocreate\Schema\Entity\Schema($entityData, new DefinitionCollection($definitions)))->toArray();
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

        //TODO this is a dirty way of receiving the service
        $receiver = null;
        if ($this->serviceManager->has($this->receiver['receiver'])) {
            $receiver = $this->serviceManager->get($this->receiver['receiver']);
        }

        if (empty($receiver)) {
            foreach (\array_keys($this->serviceManager->getServiceManagerConfig()->getSubManagers()) as $subManager) {
                if ($this->serviceManager->get($subManager)->has($this->receiver['receiver'])) {
                    $receiver = $this->serviceManager->get($subManager)->get($this->receiver['receiver']);
                    break;
                }
            }
        }

        if (! ($receiver instanceof SchemaReceiverInterface)) {
            throw new \Exception("receiver must implement " . SchemaReceiverInterface::class);
        }

        $this->options['schema'] = $receiver->receiveSchema($this->builder, $this->receiver['options']);
    }

    private function extractReceiver(): void
    {
        if (!empty($this->receiver)) {
            return;
        }

        $receiver = $this->getSchema()->schemaReceiver();
        if (empty($receiver)) {
            return;
        }

        $this->receiver = [
            'receiver' => \get_class($receiver),
            'options' => [],
        ];
    }

    public function __get($name)
    {
        if (\array_key_exists($name, $this->value())) {
            return $this->value()[$name];
        }

        return new class() {
            public function __get($name)
            {
                return $this;
            }

            public function __call($name, $arguments)
            {
                return $this;
            }

            public function __toString()
            {
                return "";
            }
        };
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
        $values = [];

        foreach ($this->value() as $name => $val) {
            if ($val instanceof DatabaseTypeInterface) {
                $values[$name] = $val->convertToDatabaseValue();
                continue;
            }

            $values[$name] = $val;
        }
        return [
            '__receiver__'  => $this->receiver,
            '__value__' => $values,
        ];
    }

    public function __debugInfo()
    {
        return [
            '__receiver__'  => $this->receiver,
            '__value__' => $this->value(),
        ];
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
        return 'schema';
    }
}

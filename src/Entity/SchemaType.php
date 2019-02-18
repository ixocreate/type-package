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
use Ixocreate\Contract\Schema\SchemaProviderInterface;
use Ixocreate\Contract\Schema\SchemaReceiverInterface;
use Ixocreate\Contract\Type\TransformableInterface;
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
    private $provider;

    /**
     * @var SchemaInterface
     */
    private $schema;

    /**
     * @var Builder
     */
    private $builder;

    public function __construct(ServiceManager $serviceManager, Builder $builder)
    {
        $this->serviceManager = $serviceManager;
        $this->builder = $builder;
    }

    /**
     * @param $value
     * @param array $options
     * @return TypeInterface
     * @throws \Exception
     */
    public function create($value, array $options = []): TypeInterface
    {
        $provider = null;

        if (\is_array($value) && \array_key_exists('__provider__', $value) && \array_key_exists('__value__', $value)) {
            $provider = $value['__provider__'];
            $value = $value['__value__'];
        }

        if ($provider === null && array_key_exists('provider', $options)) {
            $provider = $options['provider'];
        }

        if ($provider !== null) {
            if (!is_array($provider) || !\array_key_exists('class', $provider) || !\array_key_exists('name', $provider)) {
                throw new \Exception('Invalid schema provider');
            }
            if (empty($provider['options'])) {
                $provider['options'] = [];
            }
        }
        // TODO: remove when migration is done
        if (empty($options['schema']) &&  \array_key_exists('__receiver__', $value) && \array_key_exists('__value__', $value)) {
            $receiverData = $value['__receiver__'];
            $value = $value['__value__'];

            $receiver = null;
            if ($this->serviceManager->has($receiverData['receiver'])) {
                $receiver = $this->serviceManager->get($receiverData['receiver']);
            }

            if (empty($receiver)) {
                foreach (\array_keys($this->serviceManager->getServiceManagerConfig()->getSubManagers()) as $subManager) {
                    if ($this->serviceManager->get($subManager)->has($receiverData['receiver'])) {
                        $receiver = $this->serviceManager->get($subManager)->get($receiverData['receiver']);
                        break;
                    }
                }
            }

            if ($receiver instanceof SchemaReceiverInterface) {
                $options['schema'] = $receiver->receiveSchema($this->builder, $receiverData['options']);
            } else {
                if (! ($receiver instanceof SchemaProviderInterface)) {
                    throw new \Exception("receiver must implement " . SchemaProviderInterface::class . ' or ' . SchemaReceiverInterface::class);
                }

                $name = '';
                if (!empty($receiverData['options']['pageType'])) {
                    $name = $receiverData['options']['pageType'];
                }
                $provider = [
                    'class' => $receiverData['receiver'],
                    'name' => $name
                ];

                $options['schema'] = $receiver->provideSchema($name, $this->builder, $receiverData['options']);
            }
        }

        $type = new SchemaType($this->serviceManager, $this->builder);
        $type->options = $options;
        $type->provider = $provider;

        $type->value = $type->transform($value);

        return $type;
    }

    /**
     * @param $value
     * @return array|mixed
     * @throws \Exception
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

    /**
     * @return SchemaInterface|null
     * @throws \Exception
     */
    private function getSchema(): ?SchemaInterface
    {
        if ($this->schema !== null) {
            return $this->schema;
        }

        if (!empty($this->options['schema']) && $this->options['schema'] instanceof SchemaInterface) {
            $this->schema = $this->options['schema'];
            return $this->schema;
        }

        if (!empty($this->provider) && !empty($this->provider['class']) && !empty($this->provider['name'])) {

            //TODO this is a dirty way of receiving the service
            $provider = null;
            if ($this->serviceManager->has($this->provider['class'])) {
                $provider = $this->serviceManager->get($this->provider['class']);
            }

            if (empty($provider)) {
                foreach (\array_keys($this->serviceManager->getServiceManagerConfig()->getSubManagers()) as $subManager) {
                    if ($this->serviceManager->get($subManager)->has($this->provider['class'])) {
                        $provider = $this->serviceManager->get($subManager)->get($this->provider['class']);
                        break;
                    }
                }
            }

            if (! ($provider instanceof SchemaProviderInterface)) {
                throw new \Exception("provider must implement " . SchemaProviderInterface::class);
            }

            $this->schema = $provider->provideSchema($this->provider['name'], $this->builder, $this->provider['options']);
            return $this->schema;
        }

        throw new \Exception("Cant initialize without schema");
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
            '__provider__'  => $this->provider,
            '__value__' => $values,
        ];
    }

    public function __debugInfo()
    {
        return [
            '__provider__'  => $this->provider,
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

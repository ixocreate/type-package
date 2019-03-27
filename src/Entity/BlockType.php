<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\CommonTypes\Entity;

use Doctrine\DBAL\Types\JsonType;
use Ixocreate\Application\ApplicationConfig;
use Ixocreate\Cms\Block\BlockInterface;
use Ixocreate\Contract\Schema\ElementInterface;
use Ixocreate\Contract\Schema\SchemaInterface;
use Ixocreate\Contract\Type\TransformableInterface;
use Ixocreate\Contract\Type\DatabaseTypeInterface;
use Ixocreate\Contract\Type\TypeInterface;
use Ixocreate\Entity\Entity\Definition;
use Ixocreate\Entity\Entity\DefinitionCollection;
use Ixocreate\Entity\Type\AbstractType;
use Ixocreate\Schema\Builder;
use Ixocreate\Template\Renderer;

final class BlockType extends AbstractType implements DatabaseTypeInterface
{
    /**
     * @var Builder
     */
    private $builder;

    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @var ApplicationConfig
     */
    private $applicationConfig;

    public function __construct(Builder $builder, Renderer $renderer, ApplicationConfig $applicationConfig)
    {
        $this->builder = $builder;
        $this->renderer = $renderer;
        $this->applicationConfig = $applicationConfig;
    }

    /**
     * @param $value
     * @param array $options
     * @throws \Exception
     * @return TypeInterface
     */
    public function create($value, array $options = []): TypeInterface
    {
        $type = clone $this;
        $type->options = $options;

        if (empty($type->getSchema())) {
            throw new \Exception('Cant initialize without schema');
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
        if (!\is_array($value) || empty($value)) {
            return [];
        }

        $definitions = [];
        $entityData = [];

        /** @var ElementInterface $element */
        foreach ($this->getSchema()->elements() as $element) {
            $definitions[] = new Definition($element->name(), $element->type(), true, true);
            $entityData[$element->name()] = null;
            if (\is_array($value) && \array_key_exists($element->name(), $value)) {
                $entityData[$element->name()] = $value[$element->name()];
            }

            if ($element instanceof TransformableInterface) {
                $entityData[$element->name()] = $element->transform($entityData[$element->name()]);
            }
        }

        return (new \Ixocreate\Schema\Entity\Schema($entityData, new DefinitionCollection($definitions)))->toArray();
    }

    /**
     * @return SchemaInterface
     */
    private function getSchema(): SchemaInterface
    {
        return $this->getBlock()->receiveSchema($this->builder);
    }

    /**
     * @return BlockInterface
     */
    private function getBlock(): BlockInterface
    {
        return $this->options['block'];
    }

    public function __debugInfo()
    {
        return [
            'block' => $this->getBlock(),
            'value' => $this->value(),
        ];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->renderer->render($this->getBlock()->template(), $this->value());
        } catch (\Throwable $e) {
            if (!$this->applicationConfig->isDevelopment()) {
                return '';
            }

            $errorResponse = 'Error in ' . $this->getBlock()->label() . " Block!\n\n" . $e;
            return $errorResponse;
        }
    }

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

        return \array_merge(
            ['_type' => $this->getBlock()->serviceName()],
            $values
        );
    }

    public static function baseDatabaseType(): string
    {
        return JsonType::class;
    }

    public function jsonSerialize()
    {
        return \array_merge(
            ['_type' => $this->getBlock()->serviceName()],
            $this->value()
        );
    }

    public static function serviceName(): string
    {
        return 'block';
    }
}

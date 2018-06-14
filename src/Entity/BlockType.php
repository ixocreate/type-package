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
use KiwiSuite\Cms\Block\BlockInterface;
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

final class BlockType extends AbstractType
{
    /**
     * @var Builder
     */
    private $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function create($value, array $options = []): TypeInterface
    {
        $type = clone $this;
        $type->options = $options;

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

    /**
     * @return SchemaInterface
     */
    private function getSchema(): SchemaInterface
    {
        return $this->getBlock()->schema($this->builder);
    }

    /**
     * @return BlockInterface
     */
    private function getBlock(): BlockInterface
    {
        return $this->options['block'];
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
        return array_merge(
            ['_type' => $this->getBlock()->serviceName()],
            $this->value()
        );
    }
}

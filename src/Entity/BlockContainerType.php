<?php
/**
 * kiwi-suite/common-types (https://github.com/kiwi-suite/common-types)
 *
 * @package kiwi-suite/common-types
 * @see https://github.com/kiwi-suite/common-types
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace KiwiSuite\CommonTypes\Entity;

use KiwiSuite\Cms\Block\BlockSubManager;
use KiwiSuite\Contract\Schema\SchemaInterface;
use KiwiSuite\Contract\Type\TypeInterface;
use KiwiSuite\Entity\Type\AbstractType;
use KiwiSuite\Entity\Type\Type;

final class BlockContainerType extends AbstractType
{
    /**
     * @var BlockSubManager
     */
    private $blockSubManager;

    /**
     * BlockType constructor.
     * @param BlockSubManager $blockSubManager
     */
    public function __construct(BlockSubManager $blockSubManager)
    {
        $this->blockSubManager = $blockSubManager;
    }

    public function create($value, array $options = []): TypeInterface
    {
        if (empty($options['blocks']) || !is_array($options['blocks'])) {
            $options['blocks'] = ['*'];
        }

        $options['blocks'] = $this->parseBlockOption(array_values($options['blocks']));

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
        $result = [];

        if (!is_array($value) || empty($value)) {
            return $result;
        }

        foreach ($value as $item) {
            if (empty($item['_type'])) {
                continue;
            }

            if (!$this->blockSubManager->has($item['_type'])) {
                continue;
            }

            $block = $this->blockSubManager->get($item['_type']);

            unset($item['_type']);
            $result[] = Type::create($item, BlockType::class, ['block' => $block]);
        }

        return $result;
    }

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
     * @return array
     */
    public function blocks(): array
    {
        return $this->options()['blocks'];
    }

    /**
     * @return mixed|string
     */
    public function jsonSerialize()
    {
        return $this->value();
    }

    public function __toString()
    {
        $return = [];

        foreach ($this->value() as $block) {
            try {
                $return[] = (string) $block;
            } catch (\Throwable $exception) {

            }

        }
        return implode("\n", $return);
    }

    /**
     * @param array $blocks
     * @return array
     */
    private function parseBlockOption(array $blocks): array
    {
        $parsedBlocks = [];

        foreach ($blocks as $blockName) {
            if (strpos($blockName, '*') === false) {
                if (array_key_exists($blockName, $this->blockSubManager->getServiceManagerConfig()->getNamedServices())) {
                    $parsedBlocks[] = $blockName;
                }
                continue;
            }

            $beginningPart = substr($blockName, 0, strpos($blockName, '*'));

            foreach (array_keys($this->blockSubManager->getServiceManagerConfig()->getNamedServices()) as $mappingBlock) {
                if (substr($mappingBlock, 0, strlen($beginningPart)) === $beginningPart) {
                    $parsedBlocks[] = $mappingBlock;
                }
            }
        }

        return $parsedBlocks;
    }

    public function __debugInfo()
    {
        return [
            'blocks' => $this->blocks(),
            'value' => $this->value(),
        ];
    }

    public static function serviceName(): string
    {
        return 'block_container';
    }
}

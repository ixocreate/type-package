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

use Doctrine\DBAL\Types\JsonType;
use KiwiSuite\Cms\Block\BlockSubManager;
use KiwiSuite\Contract\Type\DatabaseTypeInterface;
use KiwiSuite\Contract\Type\TypeInterface;
use KiwiSuite\Entity\Type\AbstractType;
use KiwiSuite\Entity\Type\Type;

final class BlockContainerType extends AbstractType implements DatabaseTypeInterface
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
        if (is_array($value) && array_key_exists('__value__', $value) && array_key_exists('__blocks__', $value)) {
            $blocks = $value['__blocks__'];
            $value = $value['__value__'];
        }

        if (!empty($options['blocks'])) {
            $blocks = $options['blocks'];
        }

        if (empty($blocks) || !is_array($blocks)) {
            $blocks = ['*'];
        }

        $options['blocks'] = $this->parseBlockOption(array_values($blocks));

        $type = clone $this;
        $type->options = $options;

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
            '__blocks__'  => $this->blocks(),
            '__value__' => $values
        ];
    }

    public static function baseDatabaseType(): string
    {
        return JsonType::class;
    }
}

<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Package\Type;

/**
 * Interface TransformableInterface
 * @package Ixocreate\Package\Schema
 */
interface TransformableInterface
{
    public function transform($data): TypeInterface;
}

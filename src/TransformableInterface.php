<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Type\Package;

/**
 * Interface TransformableInterface
 * @package Ixocreate\Schema\Package
 */
interface TransformableInterface
{
    public function transform($data): TypeInterface;
}

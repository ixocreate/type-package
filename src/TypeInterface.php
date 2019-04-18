<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Type\Package;

interface TypeInterface extends \JsonSerializable
{
    const TYPE_STRING = 'string';

    const TYPE_INT = 'int';

    const TYPE_FLOAT = 'float';

    const TYPE_BOOL = 'bool';

    const TYPE_ARRAY = 'array';

    const TYPE_CALLABLE = 'callable';

    /**
     * @param $value
     * @param array $options
     * @return TypeInterface
     */
    public function create($value, array $options = []): TypeInterface;

    /**
     * @return mixed
     */
    public function value();

    /**
     * @return mixed
     * @deprecated
     */
    public function getValue();

    /**
     * @return array
     */
    public function options(): array;

    /**
     * @return mixed
     */
    public function __debugInfo();
}

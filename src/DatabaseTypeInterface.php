<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Type\Package;

interface DatabaseTypeInterface
{
    /**
     * @return mixed
     */
    public function convertToDatabaseValue();

    /**
     * @return string
     */
    public static function baseDatabaseType(): string;
}

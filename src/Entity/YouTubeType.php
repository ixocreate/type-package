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

use Doctrine\DBAL\Types\StringType;
use KiwiSuite\Contract\Type\DatabaseTypeInterface;
use KiwiSuite\Entity\Type\AbstractType;

final class YouTubeType extends AbstractType implements DatabaseTypeInterface
{
    /**
     * @param $value
     * @return string
     */
    protected function transform($value)
    {
        return $value;
    }

    /**
     * @return string
     */
    public function convertToDatabaseValue()
    {
        return (string) $this;
    }

    /**
     * @return string
     */
    public static function baseDatabaseType(): string
    {
        return StringType::class;
    }

    public static function serviceName(): string
    {
        return 'youtube';
    }
}

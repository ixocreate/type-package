<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\CommonTypes\Entity;

use Doctrine\DBAL\Types\StringType;
use Ixocreate\Contract\Type\DatabaseTypeInterface;
use Ixocreate\Entity\Type\AbstractType;

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

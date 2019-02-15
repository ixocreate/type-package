<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\CommonTypes\Entity;

use Doctrine\DBAL\Types\JsonType;
use Ixocreate\Contract\Schema\BuilderInterface;
use Ixocreate\Contract\Schema\ElementInterface;
use Ixocreate\Contract\Schema\ElementProviderInterface;
use Ixocreate\Contract\Type\DatabaseTypeInterface;
use Ixocreate\Entity\Type\AbstractType;
use Ixocreate\Schema\Elements\MapElement;
use Ixocreate\Schema\ElementSubManager;

final class MapType extends AbstractType implements DatabaseTypeInterface, ElementProviderInterface
{
    /**
     * @param $value
     * @return mixed
     */
    protected function transform($value)
    {
        $default = [
            'lng' => null,
            'lat' => null,
        ];
        if (!\is_array($value)) {
            return $default;
        }

        if (!\array_key_exists('lat', $value) || !\array_key_exists('lng', $value)) {
            return $default;
        }

        $default['lat'] = (float) $value['lat'];
        $default['lng'] = (float) $value['lng'];

        return $default;
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
        return $this->value();
    }

    /**
     * @return string
     */
    public function convertToDatabaseValue()
    {
        return $this->value();
    }

    /**
     * @return string
     */
    public static function baseDatabaseType(): string
    {
        return JsonType::class;
    }

    public static function serviceName(): string
    {
        return 'map';
    }

    public function provideElement(BuilderInterface $builder): ElementInterface
    {
        return $builder->get(MapElement::class);
    }
}

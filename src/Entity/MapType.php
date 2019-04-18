<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Type\Entity;

use Doctrine\DBAL\Types\JsonType;
use Ixocreate\Schema\BuilderInterface;
use Ixocreate\Schema\ElementInterface;
use Ixocreate\Schema\ElementProviderInterface;
use Ixocreate\Type\DatabaseTypeInterface;
use Ixocreate\Entity\Type\AbstractType;
use Ixocreate\Schema\Elements\MapElement;

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

    public function lat(): ?float
    {
        return $this->latitude();
    }

    public function latString(): string
    {
        return $this->latitudeString();
    }

    public function latitude(): ?float
    {
        return $this->value['lat'];
    }

    public function latitudeString(): string
    {
        if ($this->value['lat'] === null) {
            return '';
        }
        return \json_encode($this->value['lat']);
    }

    public function lng(): ?float
    {
        return $this->longitude();
    }

    public function lngString(): string
    {
        return $this->longitudeString();
    }

    public function longitude(): ?float
    {
        return $this->value['lng'];
    }

    public function longitudeString(): string
    {
        if ($this->value['lng'] === null) {
            return '';
        }
        return \json_encode($this->value['lng']);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '';
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

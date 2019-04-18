<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Package\Type\Entity;

use Doctrine\DBAL\Types\JsonType;
use Ixocreate\Package\Schema\BuilderInterface;
use Ixocreate\Package\Schema\ElementInterface;
use Ixocreate\Package\Schema\ElementProviderInterface;
use Ixocreate\Package\Type\DatabaseTypeInterface;
use Ixocreate\Package\Entity\Type\AbstractType;
use Ixocreate\Package\Schema\Elements\PriceElement;

final class PriceType extends AbstractType implements DatabaseTypeInterface, ElementProviderInterface
{
    /**
     * @param $value
     * @return mixed
     */
    protected function transform($value)
    {
        $default = [
            'currency' => null,
            'price' => null,
        ];
        if (!\is_array($value)) {
            return $default;
        }

        if (!\array_key_exists('currency', $value) || !\array_key_exists('price', $value)) {
            return $default;
        }

        $default['currency'] = (string) $value['currency'];
        $default['price'] = (float) $value['price'];

        return $default;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (empty($this->value())) {
            return "";
        }
        return (string) $this->value()['price'];
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
        return 'price';
    }

    public function provideElement(BuilderInterface $builder): ElementInterface
    {
        return $builder->get(PriceElement::class);
    }
}

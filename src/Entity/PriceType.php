<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Type\Package\Entity;

use Doctrine\DBAL\Types\JsonType;
use Ixocreate\Schema\Package\BuilderInterface;
use Ixocreate\Schema\Package\ElementInterface;
use Ixocreate\Schema\Package\ElementProviderInterface;
use Ixocreate\Type\Package\DatabaseTypeInterface;
use Ixocreate\Entity\Package\Type\AbstractType;
use Ixocreate\Schema\Package\Elements\PriceElement;

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

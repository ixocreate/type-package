<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Package\Type\Entity;

use Ixocreate\Package\Schema\BuilderInterface;
use Ixocreate\Package\Schema\ElementInterface;
use Ixocreate\Package\Schema\ElementProviderInterface;
use Ixocreate\Package\Type\DatabaseTypeInterface;
use Ixocreate\Package\Entity\Type\AbstractType;
use Ixocreate\Package\Schema\Elements\DateElement;

final class DateType extends AbstractType implements DatabaseTypeInterface, ElementProviderInterface
{
    /**
     * @param $value
     * @throws \Exception
     * @return \DateTimeImmutable
     */
    protected function transform($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return new \DateTimeImmutable('@' . $value->getTimestamp());
        }

        if (\is_string($value)) {
            $value = \strtotime($value);
        }

        if (\is_int($value)) {
            return new \DateTimeImmutable('@' . $value);
        }

        if (\is_array($value) && \array_key_exists('date', $value) && \array_key_exists('timezone', $value)) {
            return new \DateTimeImmutable($value['date'], new \DateTimeZone($value['timezone']));
        }

        throw new \Exception("invalid date format");
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return ($this->value() === null) ? null : $this->value()->format('Y-m-d');
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
        return \Doctrine\DBAL\Types\DateType::class;
    }

    public static function serviceName(): string
    {
        return 'date';
    }

    public function provideElement(BuilderInterface $builder): ElementInterface
    {
        return $builder->get(DateElement::class);
    }
}

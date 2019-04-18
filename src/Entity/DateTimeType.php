<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Type\Entity;

use Ixocreate\Entity\Type\AbstractType;
use Ixocreate\Schema\BuilderInterface;
use Ixocreate\Schema\ElementInterface;
use Ixocreate\Schema\ElementProviderInterface;
use Ixocreate\Schema\Elements\DateTimeElement;
use Ixocreate\Type\DatabaseTypeInterface;

final class DateTimeType extends AbstractType implements DatabaseTypeInterface, ElementProviderInterface
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
        return ($this->value() === null) ? null : $this->value()->format('c');
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
        return \Doctrine\DBAL\Types\DateTimeType::class;
    }

    public static function serviceName(): string
    {
        return 'datetime';
    }

    public function provideElement(BuilderInterface $builder): ElementInterface
    {
        return $builder->get(DateTimeElement::class);
    }
}

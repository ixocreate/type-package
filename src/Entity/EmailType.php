<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Type\Entity;

use Assert\Assertion;
use Doctrine\DBAL\Types\StringType;
use Ixocreate\Entity\Type\AbstractType;
use Ixocreate\Type\DatabaseTypeInterface;

final class EmailType extends AbstractType implements DatabaseTypeInterface
{
    /**
     * @param $value
     * @throws \Assert\AssertionFailedException
     * @return string
     */
    protected function transform($value)
    {
        Assertion::email($value);

        return $value;
    }

    /**
     * @return string
     */
    public function convertToDatabaseValue()
    {
        return (string)$this;
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
        return 'email';
    }
}

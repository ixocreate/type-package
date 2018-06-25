<?php
declare(strict_types=1);

namespace KiwiSuite\CommonTypes\Entity;

use Doctrine\DBAL\Types\StringType;
use KiwiSuite\Contract\Type\DatabaseTypeInterface;
use KiwiSuite\Entity\Type\AbstractType;

final class ActiveType extends AbstractType implements DatabaseTypeInterface
{
    /**
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    public function transform($value)
    {
        if (!\in_array($value, ['active', 'inactive'])) {
            //TODO Exception
            throw new \Exception("invalid type");
        }

        return $value;
    }

    public function convertToDatabaseValue()
    {
        return (string) $this;
    }

    public static function baseDatabaseType(): string
    {
        return StringType::class;
    }

    public static function serviceName(): string
    {
        return 'active';
    }
}

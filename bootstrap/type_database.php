<?php
namespace KiwiSuite\CommonTypes;

use Doctrine\DBAL\Types\GuidType;
use Doctrine\DBAL\Types\StringType;
use KiwiSuite\CommonTypes\Entity\ColorType;
use KiwiSuite\CommonTypes\Entity\DateTimeType;
use KiwiSuite\CommonTypes\Entity\DateType;
use KiwiSuite\CommonTypes\Entity\EmailType;
use KiwiSuite\CommonTypes\Entity\UuidType;
use KiwiSuite\Database\Type\TypeConfigurator;

/** @var TypeConfigurator $type */
$type->addType(EmailType::class, StringType::class);
$type->addType(ColorType::class, StringType::class);
$type->addType(UuidType::class, GuidType::class);
$type->addType(DateTimeType::class, \Doctrine\DBAL\Types\DateTimeType::class);
$type->addType(DateType::class, \Doctrine\DBAL\Types\DateType::class);

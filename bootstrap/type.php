<?php
namespace KiwiSuite\CommonTypes;

use KiwiSuite\CommonTypes\Entity\ColorType;
use KiwiSuite\CommonTypes\Entity\DateTimeType;
use KiwiSuite\CommonTypes\Entity\DateType;
use KiwiSuite\CommonTypes\Entity\EmailType;
use KiwiSuite\CommonTypes\Entity\UuidType;
use KiwiSuite\Entity\Type\TypeConfigurator;

/** @var TypeConfigurator $type */
$type->addType(EmailType::class);
$type->addType(ColorType::class);
$type->addType(UuidType::class);
$type->addType(DateTimeType::class);
$type->addType(DateType::class);

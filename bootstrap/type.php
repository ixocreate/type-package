<?php
namespace KiwiSuite\CommonTypes;

use KiwiSuite\CommonTypes\Entity\BlockContainerType;
use KiwiSuite\CommonTypes\Entity\BlockType;
use KiwiSuite\CommonTypes\Entity\CollectionType;
use KiwiSuite\CommonTypes\Entity\ColorType;
use KiwiSuite\CommonTypes\Entity\DateTimeType;
use KiwiSuite\CommonTypes\Entity\DateType;
use KiwiSuite\CommonTypes\Entity\EmailType;
use KiwiSuite\CommonTypes\Entity\Factory\SchemaTypeFactory;
use KiwiSuite\CommonTypes\Entity\HtmlType;
use KiwiSuite\CommonTypes\Entity\LinkType;
use KiwiSuite\CommonTypes\Entity\MapType;
use KiwiSuite\CommonTypes\Entity\PriceType;
use KiwiSuite\CommonTypes\Entity\SchemaType;
use KiwiSuite\CommonTypes\Entity\UuidType;
use KiwiSuite\CommonTypes\Entity\YouTubeType;
use KiwiSuite\Entity\Type\TypeConfigurator;

/** @var TypeConfigurator $type */
$type->addType(EmailType::class);
$type->addType(ColorType::class);
$type->addType(UuidType::class);
$type->addType(DateTimeType::class);
$type->addType(DateType::class);
$type->addType(CollectionType::class);
$type->addType(BlockContainerType::class);
$type->addType(BlockType::class);
$type->addType(LinkType::class);
$type->addType(HtmlType::class);
$type->addType(YouTubeType::class);
$type->addType(PriceType::class);
$type->addType(MapType::class);
$type->addType(SchemaType::class, SchemaTypeFactory::class);

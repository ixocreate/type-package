<?php
namespace Ixocreate\CommonTypes;

use Ixocreate\CommonTypes\Entity\BlockContainerType;
use Ixocreate\CommonTypes\Entity\BlockType;
use Ixocreate\CommonTypes\Entity\CollectionType;
use Ixocreate\CommonTypes\Entity\ColorType;
use Ixocreate\CommonTypes\Entity\DateTimeType;
use Ixocreate\CommonTypes\Entity\DateType;
use Ixocreate\CommonTypes\Entity\EmailType;
use Ixocreate\CommonTypes\Entity\Factory\CollectionTypeFactory;
use Ixocreate\CommonTypes\Entity\Factory\SchemaTypeFactory;
use Ixocreate\CommonTypes\Entity\HtmlType;
use Ixocreate\CommonTypes\Entity\LinkType;
use Ixocreate\CommonTypes\Entity\MapType;
use Ixocreate\CommonTypes\Entity\PriceType;
use Ixocreate\CommonTypes\Entity\SchemaType;
use Ixocreate\CommonTypes\Entity\UuidType;
use Ixocreate\CommonTypes\Entity\YouTubeType;
use Ixocreate\Entity\Type\TypeConfigurator;

/** @var TypeConfigurator $type */
$type->addType(EmailType::class);
$type->addType(ColorType::class);
$type->addType(UuidType::class);
$type->addType(DateTimeType::class);
$type->addType(DateType::class);
$type->addType(BlockContainerType::class);
$type->addType(BlockType::class);
$type->addType(LinkType::class);
$type->addType(HtmlType::class);
$type->addType(YouTubeType::class);
$type->addType(PriceType::class);
$type->addType(MapType::class);
$type->addType(SchemaType::class, SchemaTypeFactory::class);
$type->addType(CollectionType::class, CollectionTypeFactory::class);

<?php
namespace Ixocreate\Package\Type;

use Ixocreate\Package\Type\Entity\BlockContainerType;
use Ixocreate\Package\Type\Entity\BlockType;
use Ixocreate\Package\Type\Entity\CollectionType;
use Ixocreate\Package\Type\Entity\ColorType;
use Ixocreate\Package\Type\Entity\DateTimeType;
use Ixocreate\Package\Type\Entity\DateType;
use Ixocreate\Package\Type\Entity\EmailType;
use Ixocreate\Package\Type\Entity\Factory\CollectionTypeFactory;
use Ixocreate\Package\Type\Entity\Factory\SchemaTypeFactory;
use Ixocreate\Package\Type\Entity\HtmlType;
use Ixocreate\Package\Type\Entity\LinkType;
use Ixocreate\Package\Type\Entity\MapType;
use Ixocreate\Package\Type\Entity\PriceType;
use Ixocreate\Package\Type\Entity\SchemaType;
use Ixocreate\Package\Type\Entity\UuidType;
use Ixocreate\Package\Type\Entity\YouTubeType;
use Ixocreate\Package\Entity\Type\TypeConfigurator;

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

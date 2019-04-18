<?php
namespace Ixocreate\Type\Package;

use Ixocreate\Type\Package\Entity\BlockContainerType;
use Ixocreate\Type\Package\Entity\BlockType;
use Ixocreate\Type\Package\Entity\CollectionType;
use Ixocreate\Type\Package\Entity\ColorType;
use Ixocreate\Type\Package\Entity\DateTimeType;
use Ixocreate\Type\Package\Entity\DateType;
use Ixocreate\Type\Package\Entity\EmailType;
use Ixocreate\Type\Package\Entity\Factory\CollectionTypeFactory;
use Ixocreate\Type\Package\Entity\Factory\SchemaTypeFactory;
use Ixocreate\Type\Package\Entity\HtmlType;
use Ixocreate\Type\Package\Entity\LinkType;
use Ixocreate\Type\Package\Entity\MapType;
use Ixocreate\Type\Package\Entity\PriceType;
use Ixocreate\Type\Package\Entity\SchemaType;
use Ixocreate\Type\Package\Entity\UuidType;
use Ixocreate\Type\Package\Entity\YouTubeType;
use Ixocreate\Entity\Package\Type\TypeConfigurator;

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

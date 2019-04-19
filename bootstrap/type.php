<?php

namespace Ixocreate\Type;

use Ixocreate\Type\Entity\BlockContainerType;
use Ixocreate\Type\Entity\BlockType;
use Ixocreate\Type\Entity\CollectionType;
use Ixocreate\Type\Entity\ColorType;
use Ixocreate\Type\Entity\DateTimeType;
use Ixocreate\Type\Entity\DateType;
use Ixocreate\Type\Entity\EmailType;
use Ixocreate\Type\Entity\Factory\CollectionTypeFactory;
use Ixocreate\Type\Entity\Factory\SchemaTypeFactory;
use Ixocreate\Type\Entity\HtmlType;
use Ixocreate\Type\Entity\LinkType;
use Ixocreate\Type\Entity\MapType;
use Ixocreate\Type\Entity\PriceType;
use Ixocreate\Type\Entity\SchemaType;
use Ixocreate\Type\Entity\UuidType;
use Ixocreate\Type\Entity\YouTubeType;

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

<?php
/**
 * kiwi-suite/common-types (https://github.com/kiwi-suite/common-types)
 *
 * @package kiwi-suite/common-types
 * @link https://github.com/kiwi-suite/common-types
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace Ixocreate\CommonTypes\Entity\Factory;

use Ixocreate\CommonTypes\Entity\CollectionType;
use Ixocreate\Contract\ServiceManager\FactoryInterface;
use Ixocreate\Contract\ServiceManager\ServiceManagerInterface;
use Ixocreate\Schema\Builder;

final class CollectionTypeFactory implements FactoryInterface
{

    /**
     * @param ServiceManagerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ServiceManagerInterface $container, $requestedName, array $options = null)
    {
        return new CollectionType($container, $container->get(Builder::class));
    }
}

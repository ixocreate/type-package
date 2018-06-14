<?php
namespace KiwiSuite\CommonTypes\Entity\Factory;

use KiwiSuite\CommonTypes\Entity\SchemaType;
use KiwiSuite\Contract\ServiceManager\FactoryInterface;
use KiwiSuite\Contract\ServiceManager\ServiceManagerInterface;
use KiwiSuite\Schema\Builder;

final class SchemaTypeFactory implements FactoryInterface
{

    /**
     * @param ServiceManagerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ServiceManagerInterface $container, $requestedName, array $options = null)
    {
        return new SchemaType($container, $container->get(Builder::class));
    }
}

<?php
namespace KiwiSuite\CommonTypes\Bootstrap;

use Doctrine\DBAL\Types\GuidType;
use Doctrine\DBAL\Types\StringType;
use KiwiSuite\Application\Bootstrap\BootstrapInterface;
use KiwiSuite\Application\ConfiguratorItem\ConfiguratorRegistry;
use KiwiSuite\Application\Service\ServiceRegistry;
use KiwiSuite\CommonTypes\Entity\EmailType;
use KiwiSuite\CommonTypes\Entity\UuidType;
use KiwiSuite\ServiceManager\ServiceManager;

final class CommonTypesBootstrap implements BootstrapInterface
{

    /**
     * @param ConfiguratorRegistry $configuratorRegistry
     */
    public function configure(ConfiguratorRegistry $configuratorRegistry): void
    {
        $configuratorRegistry->getConfigurator('typeConfigurator')->addFactory(EmailType::class);
        $configuratorRegistry->getConfigurator('databaseTypeConfigurator')->addType(EmailType::class, StringType::class);

        $configuratorRegistry->getConfigurator('typeConfigurator')->addFactory(UuidType::class);
        $configuratorRegistry->getConfigurator('databaseTypeConfigurator')->addType(UuidType::class, GuidType::class);


    }

    /**
     * @param ServiceRegistry $serviceRegistry
     */
    public function addServices(ServiceRegistry $serviceRegistry): void
    {

    }

    /**
     * @return array|null
     */
    public function getConfiguratorItems(): ?array
    {
        return null;
    }

    /**
     * @return array|null
     */
    public function getDefaultConfig(): ?array
    {
        return null;
    }

    /**
     * @param ServiceManager $serviceManager
     */
    public function boot(ServiceManager $serviceManager): void
    {
    }
}

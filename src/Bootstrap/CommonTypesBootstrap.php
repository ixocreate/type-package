<?php
namespace KiwiSuite\CommonTypes\Bootstrap;

use Doctrine\DBAL\Types\GuidType;
use Doctrine\DBAL\Types\StringType;
use KiwiSuite\Application\Bootstrap\BootstrapInterface;
use KiwiSuite\Application\ConfiguratorItem\ConfiguratorRegistry;
use KiwiSuite\Application\Service\ServiceRegistry;
use KiwiSuite\CommonTypes\Entity\ColorType;
use KiwiSuite\CommonTypes\Entity\DateTimeType;
use KiwiSuite\CommonTypes\Entity\DateType;
use KiwiSuite\CommonTypes\Entity\EmailType;
use KiwiSuite\CommonTypes\Entity\UuidType;
use KiwiSuite\Database\Type\TypeConfigurator;
use KiwiSuite\Entity\ConfiguratorItem\TypeConfiguratorItem;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfigurator;

final class CommonTypesBootstrap implements BootstrapInterface
{

    /**
     * @param ConfiguratorRegistry $configuratorRegistry
     */
    public function configure(ConfiguratorRegistry $configuratorRegistry): void
    {
        /** @var ServiceManagerConfigurator $typeConfigurator */
        $typeConfigurator = $configuratorRegistry->get(TypeConfiguratorItem::class);

        /** @var TypeConfigurator $databaseTypeConfigurator */
        $databaseTypeConfigurator = $configuratorRegistry->get(\KiwiSuite\Database\ConfiguratorItem\TypeConfiguratorItem::class);

        $typeConfigurator->addFactory(EmailType::class);
        $databaseTypeConfigurator->addType(EmailType::class, StringType::class);

        $typeConfigurator->addFactory(ColorType::class);
        $databaseTypeConfigurator->addType(ColorType::class, StringType::class);

        $typeConfigurator->addFactory(UuidType::class);
        $databaseTypeConfigurator->addType(UuidType::class, GuidType::class);

        $typeConfigurator->addFactory(DateTimeType::class);
        $databaseTypeConfigurator->addType(DateTimeType::class, \Doctrine\DBAL\Types\DateTimeType::class);

        $typeConfigurator->addFactory(DateType::class);
        $databaseTypeConfigurator->addType(DateType::class, \Doctrine\DBAL\Types\DateType::class);
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

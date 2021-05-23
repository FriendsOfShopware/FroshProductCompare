<?php declare(strict_types=1);

namespace Frosh\FroshProductCompare;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class FroshProductCompare extends Plugin
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/DependencyInjection/'));
        $loader->load('services.xml');
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            parent::uninstall($uninstallContext);

            return;
        }

        $this->cleanRelatedData();
        parent::uninstall($uninstallContext);
    }

    /**
     * @throws DBALException
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function cleanRelatedData(): void
    {
        /** @var Connection $connection */
        $connection = $this->container->get(Connection::class);

        $connection->executeStatement('
            DROP TABLE IF EXISTS frosh_cross_selling_comparable;
        ');
    }
}

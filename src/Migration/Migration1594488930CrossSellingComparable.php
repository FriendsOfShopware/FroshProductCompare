<?php declare(strict_types=1);

namespace Frosh\FroshProductCompare\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1594488930CrossSellingComparable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1594488930;
    }

    public function update(Connection $connection): void
    {
        $connection->executeUpdate('
            CREATE TABLE `frosh_cross_selling_comparable` (
                `id` BINARY(16) NOT NULL PRIMARY KEY,
                `product_cross_selling_id` BINARY(16) NOT NULL,
                `is_comparable` TINYINT(1) NULL DEFAULT 0,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                CONSTRAINT `uniq.frosh_cross_selling_comparable.product_cross_selling_id` UNIQUE (`product_cross_selling_id`),
                CONSTRAINT `fk.frosh_cross_selling_comparable.product_cross_selling_id` FOREIGN KEY (`product_cross_selling_id`)
                REFERENCES `product_cross_selling` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}

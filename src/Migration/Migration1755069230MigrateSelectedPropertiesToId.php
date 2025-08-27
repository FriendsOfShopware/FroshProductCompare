<?php

declare(strict_types=1);

namespace Frosh\FroshProductCompare\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1755069230MigrateSelectedPropertiesToId extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1755069230;
    }

    public function update(Connection $connection): void
    {
        $systemConfigEntries = $connection->fetchAllKeyValue("SELECT LOWER(HEX(id)), configuration_value FROM system_config WHERE configuration_key = 'FroshProductCompare.config.selectedProperties'");

        foreach ($systemConfigEntries as $systemConfigId => $rawConfigurationValue) {
            if(!is_string($rawConfigurationValue)) {
                continue;
            }

            $migratedPropertyIds = [];
            $selectedPropertiesConfig = json_decode($rawConfigurationValue, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($selectedPropertiesConfig)) {
                continue;
            }
            $selectedPropertiesConfig = $selectedPropertiesConfig['_value'] ?? [];

            if(!is_array($selectedPropertiesConfig)) {
                continue;
            }

            foreach ($selectedPropertiesConfig as $propertyData) {
                $extractedPropertyId = !is_array($propertyData) ? $propertyData : ($propertyData['id'] ?? null);
                if (!empty($extractedPropertyId)) {
                    $migratedPropertyIds[] = $extractedPropertyId;
                }
            }

            $connection->executeStatement('UPDATE IGNORE system_config SET configuration_value = :configuration_value WHERE id = :id', [
                'configuration_value' => json_encode(['_value' => $migratedPropertyIds], JSON_THROW_ON_ERROR),
                'id' => Uuid::fromHexToBytes($systemConfigId),
            ]);
        }
    }

    public function updateDestructive(Connection $connection): void
    {}
}

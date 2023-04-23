<?php

namespace OctopusPress\Plugin\StatisticalAnalysis\Provider;

use Doctrine\DBAL\ParameterType;
use OctopusPress\Bundle\Bridge\Bridger;
use OctopusPress\Bundle\Plugin\PluginProviderInterface;

class StatisticalProvider implements PluginProviderInterface
{
    private Bridger $bridger;

    public function __construct(Bridger $bridger)
    {
        $this->bridger = $bridger;
    }


    public function user(string $subType, int $userId)
    {
        $connection = $this->bridger->getEntityManager()->getConnection();
        return (int) $connection->executeQuery(
            'SELECT count FROM statistical_analysis WHERE type = ? AND sub_type = ? AND object_id = ? LIMIT 1',
            ['user', $subType, $userId],
            [ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER],
        )->fetchOne();
    }

    public function post(string $subType, int $userId)
    {

    }
}

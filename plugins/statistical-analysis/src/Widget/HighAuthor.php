<?php

namespace OctopusPress\Plugin\StatisticalAnalysis\Widget;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use OctopusPress\Bundle\Widget\AbstractWidget;
use Traversable;
use Twig\TemplateWrapper;

class HighAuthor extends AbstractWidget implements \IteratorAggregate
{

    protected function template(): string|TemplateWrapper
    {
        // TODO: Implement template() method.
    }

    /**
     * @throws Exception
     */
    protected function context(array $attributes = []): array
    {
        // TODO: Implement context() method.
        $entityManager = $this->getBridger()->getEntityManager();
        $result = $entityManager->getConnection()
            ->executeQuery(
                'SELECT object_id FROM statistical_analysis WHERE type = ? AND sub_type = ? ORDER BY count DESC LIMIT ?',
                ['user', 'author', (int) ($attributes['limit'] ?? 10)],
                [ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER]
            )->fetchFirstColumn();
        if (empty($result)) {
            return [
                'authors' => [],
            ];
        }
        $orderByMap = array_flip($result);
        $users = $this->getBridger()->getUserRepository()
            ->findBy([
                'id' => $result,
            ]);
        $overOrder = [];
        foreach ($users as $user) {
            $overOrder[$orderByMap[$user->getId()]] = $user;
        }
        ksort($overOrder, SORT_NUMERIC);
        return [
            'authors' => $overOrder,
        ];
    }

    public function delayRegister(): void
    {
        // TODO: Implement delayRegister() method.
    }

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        // TODO: Implement getIterator() method.
        $context = $this->getContext();
        return new \ArrayIterator($context['authors']);
    }
}

<?php

namespace OctopusPress\Plugin\StatisticalAnalysis\Widget;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use OctopusPress\Bundle\Widget\AbstractWidget;
use Traversable;
use Twig\TemplateWrapper;

class HighPosts extends AbstractWidget implements \IteratorAggregate
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
        $types = (array) ($attributes['type'] ?? []);
        if (empty($types)) {
            $types = $this->getBridger()->getPost()
                ->getShowFrontTypes();
        }
        $result = $entityManager->getConnection()
            ->executeQuery(
                'SELECT object_id FROM statistical_analysis WHERE type = ? AND sub_type IN (?) ORDER BY count DESC LIMIT ?',
                ['post', $types, (int) ($attributes['limit'] ?? 10)],
                [ParameterType::STRING, ArrayParameterType::STRING, ParameterType::INTEGER]
            )->fetchFirstColumn();
        if (empty($result)) {
            return [
                'entities' => [],
            ];
        }
        $orderByMap = array_flip($result);
        $posts = $this->getBridger()->getPostRepository()->createQuery(['id' => $result])->getResult();
        $overOrder = [];
        foreach ($posts as $item) {
            $overOrder[$orderByMap[$item->getId()]] = $item;
        }
        ksort($overOrder, SORT_NUMERIC);
        return [
            'entities' => $posts,
        ];
    }

    public function delayRegister(): void
    {
        // TODO: Implement delayRegister() method.
    }

    public function getIterator(): Traversable
    {
        // TODO: Implement getIterator() method.
        $context = $this->getContext();
        return new \ArrayIterator($context['entities'] ?? []);
    }
}

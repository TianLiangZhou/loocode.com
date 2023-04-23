<?php

namespace OctopusPress\Plugin\StatisticalAnalysis\Widget;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\Types;
use OctopusPress\Bundle\Entity\Post;
use OctopusPress\Bundle\Entity\TermTaxonomy;
use OctopusPress\Bundle\Widget\AbstractWidget;
use Traversable;
use Twig\TemplateWrapper;

class HighTaxonomyPosts extends AbstractWidget implements \IteratorAggregate
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
        if (empty($attributes['taxonomy']) || !($attributes['taxonomy'] instanceof TermTaxonomy)) {
            return [
                'entities' => [],
            ];
        }
        $taxonomy = $attributes['taxonomy'];
        $types = (array) ($attributes['type'] ?? []);
        if (empty($types)) {
            $types = $this->getBridger()->getPost()
                ->getShowFrontTypes();
        }
        $connection = $this->getBridger()->getEntityManager()->getConnection();
        $allObjects = $connection->executeQuery(
            'SELECT object_id FROM term_relationships WHERE term_taxonomy_id = ? and type IN (?) and status = ? ORDER BY created_at DESC LIMIT 10000',
            [$taxonomy->getId(), $types, Post::STATUS_PUBLISHED],
            [ParameterType::INTEGER, ArrayParameterType::STRING, ParameterType::STRING],
        )->fetchFirstColumn();
        if (empty($allObjects)) {
            return ['entities' => []];
        }
        $result = $connection
            ->executeQuery(
                'SELECT object_id FROM statistical_analysis WHERE type = ? AND sub_type IN (?) AND object_id IN (?) ORDER BY count DESC LIMIT ?',
                ['post', $types, $allObjects, (int) ($attributes['limit'] ?? 10)],
                [ParameterType::STRING, ArrayParameterType::STRING, ArrayParameterType::INTEGER, ParameterType::INTEGER]
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
            'entities' => $overOrder,
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

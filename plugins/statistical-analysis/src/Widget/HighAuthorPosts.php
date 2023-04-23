<?php

namespace OctopusPress\Plugin\StatisticalAnalysis\Widget;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\Types;
use OctopusPress\Bundle\Entity\Post;
use OctopusPress\Bundle\Widget\AbstractWidget;
use Traversable;
use Twig\TemplateWrapper;

class HighAuthorPosts extends AbstractWidget implements \IteratorAggregate
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
        if (empty($attributes['author']) || $attributes['author'] < 1) {
            return [
                'entities' => [],
            ];
        }
        $types = (array) ($attributes['type'] ?? []);
        if (empty($types)) {
            $types = $this->getBridger()->getPost()
                ->getShowFrontTypes();
        }
        $connection = $this->getBridger()->getEntityManager()->getConnection();
        $result = $connection->executeQuery(
                'SELECT id FROM posts WHERE author = ? AND type IN (?) AND status = ?',
                [(int) $attributes['author'], $types, Post::STATUS_PUBLISHED],
                [ParameterType::INTEGER, ArrayParameterType::STRING, ParameterType::STRING]
            )->fetchFirstColumn();
        if (empty($result)) {
            return [
                'entities' => [],
            ];
        }
        $result = $connection->executeQuery(
            'SELECT object_id FROM statistical_analysis WHERE type = ? AND sub_type IN (?) AND object_id IN (?) ORDER BY count DESC LIMIT ?',
            ['post', $types, $result, (int) ($attributes['limit'] ?? 10)],
            [ParameterType::STRING, ArrayParameterType::STRING, ArrayParameterType::INTEGER, ParameterType::INTEGER]
        )->fetchFirstColumn();
        if (empty($result)) {
            return [
                'entities' => [],
            ];
        }
        $orderByMap = array_flip($result);
        $posts = $this->getBridger()->getPostRepository()
            ->createQuery(['id' => $result])->getResult();
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
        return new \ArrayIterator($context['entities']);
    }
}

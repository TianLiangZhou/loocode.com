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

class HighTaxonomies extends AbstractWidget implements \IteratorAggregate
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
        $taxonomy = $attributes['taxonomy'] ?? TermTaxonomy::TAG;
        $connection = $this->getBridger()->getEntityManager()->getConnection();
        $result = $connection
            ->executeQuery(
                'SELECT object_id FROM statistical_analysis WHERE type = ? AND sub_type = ? ORDER BY count DESC LIMIT ?',
                ['taxonomy', $taxonomy, (int) ($attributes['limit'] ?? 10)],
                [ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER]
            )->fetchFirstColumn();
        if (empty($result)) {
            return ['entities' => []];
        }
        $orderByMap = array_flip($result);
        $taxonomies = $this->getBridger()->getTaxonomyRepository()
            ->taxonomies($taxonomy, ['id' => $result]);
        $overOrder = [];
        foreach ($taxonomies as $item) {
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

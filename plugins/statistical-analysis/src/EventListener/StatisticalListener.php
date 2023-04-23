<?php

namespace OctopusPress\Plugin\StatisticalAnalysis\EventListener;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use OctopusPress\Bundle\Bridge\Bridger;
use OctopusPress\Bundle\Entity\Post;
use OctopusPress\Bundle\Entity\TermTaxonomy;
use OctopusPress\Bundle\Entity\User;
use OctopusPress\Bundle\Event\OctopusEvent;
use OctopusPress\Bundle\Event\PostEvent;
use OctopusPress\Bundle\Event\PostStatusUpdateEvent;
use OctopusPress\Bundle\Support\ArchiveDataSet;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 *
 */
class StatisticalListener implements EventSubscriberInterface
{
    private Bridger $bridger;

    public function __construct(Bridger $bridger)
    {
        $this->bridger = $bridger;

        $this->bridger->getHook()->add('footer', [$this, 'footer']);
    }

    /**
     * @return void
     */
    public function footer(): void
    {
        $option = $this->bridger->getOptionRepository();
        if ($option->value('_baidu_analysis') && ($identity = $option->value('_baidu_analysis_identity'))) {
            echo <<<EOF
<script>var _hmt = _hmt || []; (function() { var hm = document.createElement("script"); hm.src = "https://hm.baidu.com/hm.js?{$identity}"; var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(hm, s); })();</script>
EOF;
        }
        if ($option->value('_google_analysis') && ($identity = $option->value('_google_analysis_identity'))) {
            echo <<<EOF
<script async src="https://www.googletagmanager.com/gtag/js?id={$identity}"></script>
<script>window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', '{$identity}');</script>
EOF;
        }
    }


    /**
     * @param FinishRequestEvent $event
     * @return void
     * @throws Exception
     */
    public function onFinish(FinishRequestEvent $event): void
    {
        $activatedRoute = $this->bridger->getActivatedRoute();
        if ($activatedRoute->isSingular() || $activatedRoute->isArchives()) {
            $this->statistical($event->getRequest());
        }
    }

    /**
     * @param PostEvent $event
     * @return void
     * @throws Exception
     */
    public function onPostDelete(PostEvent $event): void
    {
        $authorId = $event->getPost()->getAuthor()->getId();
        $connection = $this->bridger->getEntityManager()->getConnection();
        $connection->executeQuery(
            'UPDATE statistical_analysis SET `count` = `count` - 1 WHERE type = ? AND sub_type = ? AND object_id = ? AND count > 0',
            ['user', 'creation', $authorId],
            [ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER]
        );
    }


    /**
     * @param PostEvent $event
     * @return void
     * @throws Exception
     */
    public function onPostSave(PostEvent $event): void
    {
        $post = $event->getPost();
        $oldStatus = $event->getOldStatus();
        $status = $post->getStatus();
        if ($oldStatus === $status) {
            return ;
        }
        if (empty($oldStatus) && $status !== Post::STATUS_PUBLISHED) {
            return ;
        }
        if ($oldStatus !== Post::STATUS_PUBLISHED && $status !== Post::STATUS_PUBLISHED) {
            return ;
        }
        $authorId = $post->getAuthor()->getId();
        $connection = $this->bridger->getEntityManager()->getConnection();
        $record = $connection->executeQuery(
            'SELECT id FROM statistical_analysis WHERE type = ? AND sub_type = ? AND object_id = ? LIMIT 1',
            ['user', 'creation', $authorId],
            [ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER]
        )->fetchAssociative();
        $date = date('Y-m-d H:i:s');
        if (empty($record) && $status === Post::STATUS_PUBLISHED) {
            $connection->executeStatement(
                'INSERT INTO statistical_analysis (type, sub_type, object_id, count, updated_at) VALUE (?, ?, ?, ?, ?)',
                ['user', 'creation', $authorId, 1, $date],
                [ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER, ParameterType::INTEGER, ParameterType::STRING]
            );
        } else {
            $connection->executeQuery(
                'UPDATE statistical_analysis SET `count` = `count` + ? WHERE type = ? AND sub_type = ? AND object_id = ? AND count > 0',
                [$status === Post::STATUS_PUBLISHED ? 1 : -1, 'user', 'creation', $authorId],
                [ParameterType::INTEGER, ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER]
            );
        }
    }


    /**
     * @param PostStatusUpdateEvent $event
     * @return void
     * @throws Exception
     */
    public function onPostStatus(PostStatusUpdateEvent $event): void
    {
        $posts = $event->getPosts();
        $authors = [];
        foreach ($posts as $post) {
            $status = $post->getStatus();
            if ($status !== Post::STATUS_PUBLISHED) {
                continue;
            }
            $authorId = $post->getAuthor()->getId();
            $authors[$authorId] = ($authors[$authorId] ?? 0) + 1;
        }
        if (count($authors) < 1) {
            return ;
        }
        $connection = $this->bridger->getEntityManager()->getConnection();
        foreach ($authors as $authorId => $count) {
            $connection->executeQuery(
                'UPDATE statistical_analysis SET `count` = `count` + ? WHERE type = ? AND sub_type = ? AND object_id = ? AND count > 0',
                ['user', 'creation', $authorId, -$count],
                [ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER, ParameterType::INTEGER]
            );
        }
    }



    public static function getSubscribedEvents(): array
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            KernelEvents::FINISH_REQUEST => ['onFinish', 32],
            OctopusEvent::POST_DELETE => ['onPostDelete', 32],
            OctopusEvent::POST_SAVE_AFTER => ['onPostSave', 32],
            OctopusEvent::POST_STATUS_UPDATE => ['onPostStatus', 32],
        ];
    }

    /**
     * @param Request $request
     * @return void
     * @throws Exception
     */
    private function statistical(Request $request): void
    {
        $result = $this->bridger->getControllerResult();
        if ($result == null) {
            return ;
        }
        $collection = [];
        if ($result instanceof Post) {
            $type = 'post';
            $subType = $result->getType();
            $objectId = $result->getId();
            $collection[] = ['post', $result->getType(), $result->getId()];
            $collection[] = ['user', 'author', $result->getAuthor()->getId()];
            if (($parent = $result->getParent()) != null) {
                $collection[] = ['post', $parent->getType(), $parent->getId()];
            }
        }
        if ($result instanceof ArchiveDataSet && ($taxonomy = $result->getArchiveTaxonomy()) instanceof TermTaxonomy) {
            $collection[] = ['taxonomy', $taxonomy->getTaxonomy(), $taxonomy->getId()];
        }
        if (count($collection) < 1) {
            return ;
        }
        /**
         * @var
         */
        $entityManager = $this->bridger->getEntityManager();
        $connection = $entityManager->getConnection();
        foreach ($collection as $item) {
            $record = $connection->executeQuery(
                'SELECT id, count FROM statistical_analysis WHERE type = ? and sub_type = ? AND object_id = ? LIMIT 1',
                [$item[0], $item[1], $item[2]]
            )->fetchAssociative();
            if ($record) {
                $connection->executeStatement(
                    'UPDATE statistical_analysis SET count = `count` + 1, updated_at = ?  WHERE id = ?',
                    [date('Y-m-d H:i:s'),
                    $record['id']]
                );
            } else {
                $connection->executeStatement(
                    'INSERT INTO statistical_analysis (`type`, `sub_type`, `object_id`, `count`, `updated_at`) VALUE (?, ?, ?, ?, ?)',
                    [$item[0], $item[1], $item[2], 1, date('Y-m-d H:i:s')]
                );
            }
        }
    }
}

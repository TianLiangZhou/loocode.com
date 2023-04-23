<?php
namespace OctopusPress\Plugin\StatisticalAnalysis;


use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Schema\Table;
use OctopusPress\Bundle\Bridge\Bridger;
use OctopusPress\Bundle\Entity\Post;
use OctopusPress\Bundle\Plugin\Manifest;
use OctopusPress\Bundle\Plugin\PluginInterface;
use OctopusPress\Bundle\Plugin\PluginProviderInterface;
use OctopusPress\Plugin\StatisticalAnalysis\Controller\StatisticalController;
use OctopusPress\Plugin\StatisticalAnalysis\EventListener\StatisticalListener;
use OctopusPress\Plugin\StatisticalAnalysis\Provider\StatisticalProvider;
use OctopusPress\Plugin\StatisticalAnalysis\Widget\HighAuthor;
use OctopusPress\Plugin\StatisticalAnalysis\Widget\HighAuthorPosts;
use OctopusPress\Plugin\StatisticalAnalysis\Widget\HighPosts;
use OctopusPress\Plugin\StatisticalAnalysis\Widget\HighTaxonomies;
use OctopusPress\Plugin\StatisticalAnalysis\Widget\HighTaxonomyPosts;

class StatisticalAnalysis implements PluginInterface
{

    public static function manifest(): Manifest
    {
        // TODO: Implement manifest() method.
        return Manifest::builder()
            ->setName("统计分析")
            ->addAuthor('OctopusPress.dev', 'https://octopuspress.dev')
            ->setVersion('1.0.0')
            ->setMinVersion('1.0.0')
            ->setMinPhpVersion('8.1')
            ->setDescription('插件用于帖子的浏览统计，类目的浏览统计，作品统计，作者浏览统计。第三方百度，谷歌统计分析。')
            ;
    }

    /**
     * @throws \Exception
     */
    public function launcher(Bridger $bridger): void
    {
        // TODO: Implement launcher() method.
        $bridger->getHook()->add('setup_theme', function () use ($bridger) {
            $bridger->getWidget()
                ->registerForClassName(HighPosts::class)
                ->registerForClassName(HighAuthor::class)
                ->registerForClassName(HighAuthorPosts::class)
                ->registerForClassName(HighTaxonomyPosts::class)
                ->registerForClassName(HighTaxonomies::class)
            ;
        });
        $bridger->getHook()->add('plugin_action_links', function (array $actions, string $name) {
            return $actions;
        });
        $bridger->getPlugin()->registerRoute(StatisticalController::class);
    }

    /**
     * @throws Exception
     * @throws SchemaException
     */
    public function activate(Bridger $bridger): void
    {
        // TODO: Implement activate() method.
        $connection = $bridger->getEntityManager()->getConnection();
        $schemaManager = $connection->createSchemaManager();
        $tables = $this->getTables();
        foreach ($tables as $table) {
            $name = $table->getName();
            if (!$schemaManager->tablesExist($name)) {
                $schemaManager->createTable($table);
            }
        }
        $date = date('Y-m-d H:i:s');
        $results = $connection->executeQuery(
            'SELECT count(1) as cnt, author FROM posts WHERE status = ? OR status = ? GROUP BY author',
            [Post::STATUS_PUBLISHED, Post::STATUS_PRIVATE],
            [ParameterType::STRING, ParameterType::STRING]
        )->fetchAllAssociative();
        foreach ($results as $item) {
            $exists = $connection->executeQuery(
                'SELECT * FROM statistical_analysis WHERE type = ? AND sub_type = ? AND object_id = ? LIMIT 1',
                ['user', 'creation', $item['author']],
                [ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER]
            )->fetchOne();
            if ($exists) {
                $connection->executeStatement(
                    'UPDATE statistical_analysis SET count = ? WHERE type = ? AND sub_type = ? AND object_id = ?',
                    [$item['cnt'], 'user', 'creation', $item['author']],
                    [ParameterType::INTEGER, ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER]
                );
            } else {
                $connection->executeStatement(
                    'INSERT INTO statistical_analysis (type, sub_type, object_id, count, updated_at) VALUE (?, ?, ?, ?, ?)',
                    ['user', 'creation', $item['author'], $item['cnt'], $date],
                    [ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER, ParameterType::INTEGER, ParameterType::STRING]
                );
            }

        }
    }

    public function uninstall(Bridger $bridger): void
    {
        // TODO: Implement uninstall() method.
    }

    public function getServices(Bridger $bridger): array
    {
        // TODO: Implement getServices() method.
        return [
            new StatisticalController($bridger),
            new StatisticalListener($bridger),
        ];
    }

    public function provider(Bridger $bridger): ?PluginProviderInterface
    {
        return new StatisticalProvider($bridger);
    }

    /**
     * @return Table[]
     * @throws SchemaException
     */
    private function getTables(): array
    {
        $statisticalPost = new Table('statistical_analysis');
        $statisticalPost->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $statisticalPost->addColumn('type', 'string', ['length' => 32]);
        $statisticalPost->addColumn('sub_type', 'string', ['length' => 32]);
        $statisticalPost->addColumn('object_id', 'integer', ['unsigned' => true]);
        $statisticalPost->addColumn('count', 'integer', ['unsigned' => true]);
        $statisticalPost->addColumn('updated_at', 'datetime');
        $statisticalPost->setPrimaryKey(['id']);
        $statisticalPost->addIndex(['type']);
        $statisticalPost->addIndex(['object_id']);
        $statisticalPost->addIndex(['type', 'sub_type', 'count']);
        return [$statisticalPost];
    }
}

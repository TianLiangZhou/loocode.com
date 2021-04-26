<?php
declare(strict_types=1);


namespace App\Http\Controllers\Frontend;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use stdClass;

class FrontendController extends Controller
{

    /**
     * @var array
     */
    static array $options = [];

    public string $theme = 'default';

    public function __construct()
    {
        parent::__construct();
        if (empty(self::$options)) {
            self::$options = $this->getSystemOptions();
        }
        if (isset(self::$options['theme'])) {
            $this->theme = self::$options['theme'] ?? 'default';
        }
    }

    /**
     * @param string $t
     * @param string $k
     * @param string $d
     * @return stdClass
     */
    protected function getSeo(string $t = '', string $k = '', string $d = ''): stdClass
    {
        $global = [
            'static_domain' => config('app.asset_url'),
            'links' => [],
            'user' => request()->user(),
            'menu' => [],
            'options' => [
                'site_url' => self::$options['site_url'] ?? "",
                'site_title' => self::$options['site_title'] ?? "",
                'site_append_title' => self::$options['site_append_title'],
            ],
            'ad' => [
                'google' => !empty(self::$options['google_ad']) && isset(self::$options['google_ad_open']) && self::$options['google_ad_open'] === 'true'
                    ? self::$options['google_ad'] : '',
                'baidu'  => !empty(self::$options['baidu_ad']) && isset(self::$options['baidu_ad_open']) && self::$options['baidu_ad_open'] === 'true'
                    ? self::$options['baidu_ad'] : '',
            ],
            'analysis' => [
                'google' => self::$options['google_analysis'] ?? '',
                'baidu'  => self::$options['baidu_analysis'] ?? '',
                'cnzz'   => self::$options['cnzz_analysis'] ?? '',
            ]
        ];
        View::share($global);
        $title = $t ? $t . ' - ' . self::$options['site_title']
            : self::$options['site_title'] .
            (self::$options['site_append_title'] ? ' - ' . self::$options['site_append_title'] : '');
        $seo = new stdClass();
        $seo->title = $title;
        $seo->keyword = $k ? : self::$options['site_keyword'];
        $seo->description = $d ? : self::$options['site_description'];
        return $seo;
    }

    /**
     * @return array
     */
    protected function getSystemOptions(): array
    {
        $options = DB::select('SELECT option_name, option_value FROM options');
        $arr = [];
        foreach ($options as $item) {
            $arr[$item->option_name] = $item->option_value;
        }
        return $arr;
    }

    /**
     * @param array $objectIdSets
     * @return array[]
     */
    protected function getIdSetsMetesAndTaxonomy(array $objectIdSets): array
    {
        $bindParamStr = rtrim(str_repeat('?,', count($objectIdSets)), ',');
        $sql = <<<EOF
SELECT post_id, meta_key, meta_value FROM postmeta WHERE post_id IN ($bindParamStr)
EOF;
        $metas = DB::select($sql, $objectIdSets);
        $sql = <<<EOF
SELECT t1.taxonomy, t2.object_id, t3.slug FROM term_taxonomy as t1
    LEFT JOIN term_relationships as t2 ON (t1.term_taxonomy_id=t2.term_taxonomy_id)
LEFT JOIN terms as t3 ON (t1.term_id=t3.term_id)
WHERE t2.object_id IN ($bindParamStr)
EOF;
        $taxonomies = DB::select($sql, $objectIdSets);
        $postTag = $postMeta = [];
        foreach ($taxonomies as $item) {
            if ($item->taxonomy == "post_tag") {
                $postTag[$item->object_id] = array_merge($postTag[$item->object_id] ?? [], [$item->slug]);
            }
        }
        foreach ($metas as $item) {
            $postMeta[$item->post_id] = array_merge(
                $postMeta[$item->post_id] ?? [],
                [$item->meta_key => $item->meta_value]
            );
        }
        return [$postMeta, $postTag];
    }
}

<?php
declare(strict_types=1);

namespace App\Http\Controllers\Frontend;


use App\Events\Post;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Class HomeController
 * @package App\Http\Controllers\Frontend
 */
class HomeController extends FrontendController
{

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|View
     * @throws BindingResolutionException
     */
    public function index(): View
    {
        $prePage = 30;
        $paginator = \Corcel\Model\Post::published()
            ->type('post')
            ->orderBy('post_date', 'DESC')
            ->paginate($prePage, ['ID', 'post_title', 'post_modified', 'post_author', 'post_name'], 'p');
        return view($this->theme . '.index', [
            'posts' => $paginator,
            'hotPosts' => $this->getHot(),
            'seo'   => $this->getSeo(),
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function search(Request $request): RedirectResponse
    {
        $q = $request->query('q');
        $url = 'https://www.google.com/search?q=site:%s %s';
        return redirect(sprintf($url, $request->getHttpHost(), $q));
    }

    /**
     * @return array
     */
    private function getHot(): array
    {
        $sql = <<<EOF
SELECT post_id FROM postmeta WHERE meta_key = ? ORDER BY meta_value DESC LIMIT 10
EOF;
        $result = DB::select($sql, ['_lc_post_views']);
        if (empty($result)) {
            return [];
        }
        $idSets = [];
        foreach ($result as $item) {
            $idSets[] = $item->post_id;
        }
        $bindParamStr = rtrim(str_repeat('?,', count($idSets)), ',');
        $sql = <<<EOF
SELECT id, post_title, post_type FROM posts WHERE id IN ({$bindParamStr})
EOF;
        return DB::select($sql, $idSets);
    }
}

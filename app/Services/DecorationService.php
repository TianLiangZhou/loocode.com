<?php


namespace App\Services;


use Corcel\Model\Option;
use Corcel\Model\Post;
use Corcel\Model\Taxonomy;
use stdClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class DecorationService extends BaseService
{
    /**
     * @var PostService
     */
    private PostService $postService;
    /**
     * @var TaxonomyService
     */
    private TaxonomyService $taxonomyService;

    /**
     * DecorationService constructor.
     * @param PostService $postService
     * @param TaxonomyService $taxonomyService
     */
    public function __construct(PostService $postService, TaxonomyService $taxonomyService)
    {
        $this->postService = $postService;
        $this->taxonomyService = $taxonomyService;
    }

    /**
     * @param string $path
     * @return array
     */
    public function themes(string $path): array
    {
        $iterator = Finder::create()->files()->name('theme.json')->in($path);
        $themes = [];
        $theme = Option::get('theme') ?? 'default';
        foreach ($iterator as $file) {
            /**
             * @var $file SplFileInfo
             */
            $manifest = json_decode(file_get_contents($file->getPathname()));
            if (isset($manifest->image)
                && $manifest->image
                && (strpos($manifest->image, '/') === 0 || strpos($manifest->image, '.') === 0)
            ) {
                $manifest->image = 'data:image/png;base64, ' . base64_encode(file_get_contents(realpath($manifest->image)));
            }
            $manifest->token = $file->getRelativePath();
            $manifest->enable = $theme === $manifest->token;
            $themes[] = $manifest;
        }
        return $themes;
    }

    /**
     * @return stdClass
     */
    public function navigateStructData(): stdClass
    {
        // $tag = Taxonomy::name("post_tag")->orderBy("term_taxonomy_id", "DESC")->limit(20)->get();
        $data = new stdClass();
        $data->page = $this->postService->getTypeLimit("page");
        $data->post = $this->postService->getTypeLimit("post");
        $data->categories = $this->taxonomyService->taxonomy("category");
        // $data->tag = $tag;
        $data->menu = $this->taxonomyService->taxonomy("nav_menu");
        return $data;
    }

}

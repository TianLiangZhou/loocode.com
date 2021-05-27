<?php


namespace App\Services;


use Corcel\Model\Post;
use stdClass;

class ExtensionService extends BaseService
{
    /**
     * @var Post
     */
    private Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }


    /**
     * @param string $taxonomy
     * @return stdClass|null
     */
    public function findMetaTaxonomy(string $taxonomy): ?stdClass
    {
        $post = $this->post->type('meta')->where('post_name', $taxonomy)->first();
        if ($post) {
            return json_decode($post->post_content);
        }
        return null;
    }

    /**
     * @param array $metas
     * @return bool
     */
    public function saveTaxonomyMeta(array $metas): bool
    {
        foreach ($metas as $meta) {
            $this->post->type('meta')->where('post_name', $meta['taxonomy']['value'])->delete();
            $post = clone $this->post;
            $post->post_name = $post->post_title = $meta['taxonomy']['value'];
            $post->post_status = 'open';
            $post->post_excerpt = '';
            $post->post_type = 'meta';
            $post->to_ping = $post->pinged = $post->post_content_filtered = '';
            $post->post_content = json_encode($meta);
            $post->save();
        }
        return true;
    }
}

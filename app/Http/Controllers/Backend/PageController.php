<?php


namespace App\Http\Controllers\Backend;


use App\Attributes\Route;
use App\Http\Result;
use App\Models\PostContent;
use Corcel\Model\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use stdClass;

/**
 * Class PageController
 * @package App\Http\Controllers\Backend
 */
#[Route(title: "页面", sort: 2, icon: "file")]
class PageController extends BackendController
{
    /**
     * @return Result
     */
    #[Route(title: "所有页面", parent: "页面", sort: 1, link: "/app/page")]
    public function anchor(): Result
    {
        return Result::ok();
    }

    /**
     * @param Request $request
     * @return Result
     */
    #[Route(title: "页面列表", parent: "所有页面", sort: 1)]
    public function index(Request $request): Result
    {
        $posts = Post::select('id','post_author', 'post_title', 'post_status', 'post_modified', 'comment_count')
            ->type("page")
            ->orderBy('id', 'DESC');
        if ($request->query->has('id_like')) {
            $posts->where('ID',$request->query->get('id_like'));
        }
        if ($request->query->has('post_author_like')) {
            $posts->where('post_author', $request->query->getInt('post_author_like'));
        }
        if ($request->query->has('post_title_like')) {
            $posts->where('post_title', 'like', '%' . $request->query->get('post_title_like') . '%');
        }
        $posts = $posts->without("meta")->paginate(
            $request->query->getInt("data_per_page", 30),
            ['*'],
            'data_current_page',
        );
        return Result::ok($posts);
    }

    /**
     * @return Result
     */
    #[Route(title: "新建页面", parent: "页面", sort: 1, link: "/app/page/new")]
    public function new(): Result
    {
        return Result::ok();
    }

    /**
     * @param Request $request
     * @return Result
     */
    #[Route(title: "创建页面", parent: "新建页面", sort: 1)]
    public function store(Request $request): Result
    {
        $data = $request->json()->all();
        $data['to_ping'] = $data['pinged'] = $data['post_content_filtered'] = "";
        if (empty($data['post_date'])) {
            $data['post_date'] = now();
        }
        $isContentSeparation = config('app.content_separation');
        $content = $data['post_content'];
        if (!$isContentSeparation) {
            $data['post_content'] = "";
        }
        $post = new Post($data);
        $post->post_name = $data['post_name'] ?? $data['post_title'];
        $post->post_status = $data['post_status'];
        $post->post_password = "";
        $post->post_author = isset($data['post_user']) && $data['post_user'] ? (int) $data['post_user'] : auth('backend')->id();
        $post->post_type = "page";
        if ($post->save() && $isContentSeparation) {
            $pc = new PostContent();
            $pc->post_id = $post->ID;
            $pc->post_content = $content;
            $pc->save();
        }
        return Result::ok([
            'id' => $post->ID,
        ], "页面创建成功");
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Result
     */
    #[Route(title: "更新页面", parent: "新建页面", sort: 1, link: "/app/page/new")]
    public function update(int $id, Request $request)
    {
        $post = Post::find($id);
        if ($post == null || $post->post_type != 'page') {
            return Result::err(500, '页面不存在');
        }
        $data = $request->json()->all();
        $isContentSeparation = config('app.content_separation');
        $content = $data['post_content'];
        if (!$isContentSeparation) {
            $post->post_content = $content;
        }
        $post->post_title = $data['post_title'];
        $post->post_name = $data['post_name'] ?? $data['post_title'];
        $post->post_status = $data['post_status'];
        if ($post->save() && $isContentSeparation) {
            $pc = PostContent::find($id);
            $pc->post_content = $content;
            $pc->save();
        }
        return Result::ok(null, "页面更新成功");
    }

    /**
     * @param int $id
     * @return Result
     */
    #[Route(title: "页面详情", parent: "新建页面", sort: 1, link: "/app/page/new")]
    public function show(int $id): Result
    {
        $post = Post::find($id);
        if ($post == null) {
            return Result::err(404, "文章不存在");
        }
        $data = new stdClass();
        $data->post_title = $post->post_title;
        $data->post_excerpt = $post->post_excerpt;
        $editor = config('app.editor');
        if ($editor == 'markdown') {
            $data->post_content = $post->meta->markdown ?? $post->post_content;
        } elseif (config('app.content_separation')) {
            $data->post_content = PostContent::find($id)->post_content;
        } else {
            $data->post_content = $post->post_content;
        }
        $data->post_status = $post->post_status;
        $data->post_type = $post->post_type;
        $data->categories = [];
        $data->tags = [];
        return Result::ok($data);
    }
}

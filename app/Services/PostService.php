<?php


namespace App\Services;

use App\Http\Result;
use Corcel\Model\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use stdClass;

/**
 * Class PostService
 * @package App\Services
 * @property Post $model
 */
class PostService extends BaseService
{
    /**
     * PostService constructor.
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->model = $post;
    }

    /**
     * @param Request $request
     * @param string $typeName
     * @return LengthAwarePaginator
     */
    public function getPaginator(Request $request, string $typeName): LengthAwarePaginator
    {
        $paginator = $this->model->without("meta")->type($typeName)
            ->orderBy('ID', 'DESC');
        if ($request->query->has('id_like')) {
            $paginator->where('ID',$request->query->get('id_like'));
        }
        if ($request->query->has('post_author_like')) {
            $paginator->where('post_author', $request->query->getInt('post_author_like'));
        }
        if ($request->query->has('post_title_like')) {
            $paginator->where('post_title', 'like', '%' . $request->query->get('post_title_like') . '%');
        }
        return $paginator->paginate(
            $request->query->getInt("data_per_page", 30),
            ['*'],
            'data_current_page',
        );
    }

    /**
     * @param string $type
     * @param int $limit
     * @return Collection
     */
    public function getTypeLimit(string $type, int $limit  = 20)
    {
        return $this->model->published()->without('meta')->select("ID", "post_title")->type($type)->orderBy("ID", "DESC")->limit($limit)->get();
    }

    /**
     * @param int $id
     * @return ?Post
     */
    public function id(int $id): ?Model
    {
        try {
            $model = $this->getById($id);
        } catch (\Exception $_) {
            return null;
        }
        return $model;
    }

    /**
     * @param string $name
     * @param string $postType
     * @return Model|null
     */
    public function getOneNameByType(string $name, string $postType): ?Model
    {
        try {
            $model = $this->where("post_name", $name)->where("post_type", $postType)->first();
        } catch (\Exception $_) {
            return null;
        }
        return $model;
    }

    /**
     * @param string $name
     * @return Model|null
     */
    public function getOneName(string $name): ?Model
    {
        try {
            $model = $this->where("post_name", $name)->first();
        } catch (\Exception $_) {
            return null;
        }
        return $model;
    }

    /**
     * @param string $name
     * @param string $type
     * @param array $data
     * @param string $status
     * @return Post
     */
    public function save(string $name, string $type, array $data, string $status = 'publish'): Post
    {
        $model = clone $this->model;
        $model->post_name = $name;
        $model->post_status = $status;
        $model->post_type = $type;
        $model->to_ping = $model->pinged = $model->post_content_filtered = '';
        $model->fill($data);
        $model->save();
        return $model;
    }

    /**
     * @param array $dynamicData
     * @param int $id
     * @return Post
     */
    public function dynamicModelSave(array $dynamicData, int $id): Post
    {
        $model = $this->model;
        if ($id > 0) {
            $model = $this->id($id);
        }
        if ($model == null) {
            throw new \RuntimeException("不存在的数据更新");
        }
        $model->post_status = "publish";
        $model->to_ping = $model->pinged = $model->post_content_filtered = '';
        foreach (['post_title', 'post_excerpt', 'post_name', 'post_type'] as $name) {
            $model->setAttribute($name, $dynamicData[$name] ?? "");
            unset($dynamicData[$name]);
        }
        $model->post_content = json_encode($dynamicData ?? new stdClass());
        $model->save();
        return $model;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function deleteByType(string $type)
    {
        $collection = $this->where("post_type", $type)->get();
        foreach ($collection as $item) {
            $item->meta()->delete();
            $item->delete();
        }
        return true;
    }
}

<?php


namespace App\Services;

use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class TaxonomyService
 * @package App\Services
 */
class TaxonomyService extends BaseService
{
    /**
     * @var Term
     */
    private Term $term;

    /**
     * TaxonomyService constructor.
     * @param Taxonomy $taxonomy
     * @param Term $term
     */
    public function __construct(Taxonomy $taxonomy, Term $term)
    {
        $this->model = $taxonomy;
        $this->term = $term;
    }

    /**
     * @param Request $request
     * @param string $taxonomyName
     * @param bool $isPage
     * @return mixed
     */
    public function taxonomy(Request $request, string $taxonomyName, bool $isPage = true)
    {
        if ($request->query->has('name_like')) {
            $name = $request->query->get('name_like');
        } else {
            $name = $request->get('name');
        }
        $model = $this->model->name($taxonomyName)->with("meta");
        if ($name) {
            $model = $model->whereHas('term', function ($query) use ($name) {
                $query->where('slug', 'like', '%' . $name . '%');
            });
        }
        if ($isPage) {
            return $model->orderBy('term_taxonomy_id', 'desc')
                ->paginate(
                    $request->query->getInt("data_per_page", 30),
                    ['*'],
                    'data_current_page'
                );
        }
        return $model->orderBy('term_taxonomy_id', 'desc')->get();
    }

    /**
     * @param string $taxonomy
     * @param array $body
     * @return ?Model
     */
    public function createTaxonomy(string $taxonomy, array $body): ?Model
    {
        if (empty($body['slug'])) {
            $body['slug'] = $body['name'];
        }
        /**
         * @var $term Term
         */
        $term = $this->term->firstOrCreate(['name' => $body['name']], ['slug' => $body['slug']]);
        if ($term->taxonomy != null && $term->taxonomy->taxonomy == $body['taxonomy']) {
            return $term->taxonomy;
        }
        $this->model->taxonomy = $taxonomy;
        $this->model->description = $body['description'] ?? "";
        $this->model->parent = $body['parent'] ?? 0;
        $taxonomyModel = $term->taxonomy()->save($this->model);
        if (!empty($body['meta'])) {
            foreach ($body['meta'] as $name => $value) {
                $term->saveField($name, is_scalar($value) ? $value : json_encode($value));
            }
        }
        return $taxonomyModel ? : null;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function deleteTaxonomy(string $name)
    {
        return $this->where("taxonomy", $name)->delete();
    }

    /**
     * @param int $id
     * @param array $body
     * @return Model
     */
    public function updateTaxonomy(int $id, array $body): Model
    {
        if (empty($body['slug'])) {
            $body['slug'] = $body['name'];
        }
        $taxonomy = $this->getById($id);
        if ($taxonomy == null) {
            throw new \RuntimeException("归类不存在");
        }
        $taxonomy->description = $body['description'] ?? "";
        $taxonomy->parent = $body['parent'] ?? 0;
        /**
         * @var $term Term
         */
        $term = $this->term->find($taxonomy->term_id);
        $term->name = $body['name'];
        $term->slug = $body['slug'];
        $term->taxonomy()->save($taxonomy);
        $term->save();
        if (!empty($body['meta'])) {
            foreach ($body['meta'] as $name => $value) {
                $term->saveField($name, is_scalar($value) ? $value : json_encode($value));
            }
        }
        return $taxonomy;
    }
}

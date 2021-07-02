<?php


namespace App\Services;

use App\Models\Option;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Class OptionService
 * @package App\Services
 * @property Option $model
 */
class OptionService extends BaseService
{
    /**
     * OptionService constructor.
     */
    public function __construct(Option $option)
    {
        $this->model = $option;
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getPaginator(Request $request): LengthAwarePaginator
    {
        $option = DB::table($this->model->getTable());
        if ($request->query->has("option_id_like") && ($id = $request->query->getInt('option_id_like'))) {
            $option->where("option_id", $id);
        }
        if ($request->query->has("option_name_like") && ($name = $request->query->get('option_name_like'))) {
            $option->where("option_name", $name);
        }
        return $option->paginate(
            $request->query->getInt("data_per_page", 30),
            ['*'],
            'data_current_page'
        );
    }

    /**
     * @param array $options
     * @return ?Model
     */
    public function saveOptions(array $options)
    {
        $model = null;
        foreach ($options as $name => $value) {
            $this->create(["option_name" => $name, "option_value" => $value]);
        }
        return $model;
    }

    /**
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        $name = $data['option_name'];
        $value = $data['option_value'];
        return $this->model->updateOrCreate(
            ['option_name' => $name],
            [
                'option_value' => is_scalar($value)
                    ? (is_bool($value) ? ($value ? 'true' : 'false') : $value)
                    : json_encode($value) ,
                'autoload' => 'yes'
            ]
        );
    }


    /**
     * @param Option $option
     * @param array $data
     * @return bool
     */
    public function update(Option $option, array $data): bool
    {
        $value = $data['option_value'];
        $option->option_value = is_scalar($value)
            ? (is_bool($value) ? ($value ? 'true' : 'false') : $value)
            : json_encode($value);
        return $option->save();
    }

    /**
     * @param array $optionNames
     * @return stdClass
     */
    public function options(array $optionNames): stdClass
    {
        $asArray = $this->model::asArray($optionNames);
        if (empty($asArray)) {
            return new stdClass();
        }
        return (object) $asArray;
    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function deleteOptions(array $columns)
    {
        return $this->whereIn("option_name", $columns)->delete();
    }


    /**
     * @param string $name
     * @return ?Option
     */
    public function oneByName(string $name)
    {
        return $this->model::get($name);
    }
}

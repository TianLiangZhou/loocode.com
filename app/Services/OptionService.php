<?php


namespace App\Services;

use Corcel\Model\Option;
use Illuminate\Database\Eloquent\Model;
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
     * @param array $options
     * @return ?Model
     */
    public function saveOptions(array $options)
    {
        $model = null;
        foreach ($options as $name => $value) {
            $model = $this->model->updateOrCreate(
                ['option_name' => $name],
                ['option_value' => is_scalar($value) ? $value : json_encode($value) , 'autoload' => 'yes']
            );
        }
        return $model;
    }

    /**
     * @param array $optionNames
     * @return stdClass
     */
    public function options(array $optionNames)
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
}

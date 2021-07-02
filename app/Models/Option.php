<?php


namespace App\Models;


use App\Helpers\Helper;

class Option extends \Corcel\Model\Option
{
    /**
     * @return mixed
     */
    public function getValueAttribute()
    {
        return Helper::formatValue($this->option_value);
    }
}

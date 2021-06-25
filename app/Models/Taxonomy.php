<?php


namespace App\Models;


use Corcel\Model\Meta\TermMeta;

/**
 * Class Taxonomy
 * @package App\Models
 */
class Taxonomy extends \Corcel\Model\Taxonomy
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meta()
    {
        return $this->hasMany(TermMeta::class, 'term_id', 'term_id');
    }
}

<?php


namespace App\Models;


/**
 * Class Term
 * @package App\Models
 */
class Term extends \Corcel\Model\Term
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'slug',
    ];

}

<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostContent extends Model
{
    use HasFactory;

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = 'post_content';

    protected $primaryKey = 'post_id';

    /**
     * @var array
     */
    protected $fillable = [
        'post_id',
        'post_content'
    ];
}


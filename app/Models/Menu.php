<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';

    const CREATED_AT = 'created_date';

    const UPDATED_AT = null;

    protected $fillable = [
        "parent_id",
        "name",
        "hidden",
        "weight",
        "class",
        "url",
        "link",
        "object_id"
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->belongsTo(Menu::class, 'id', 'parent_id', Menu::class);
    }
}

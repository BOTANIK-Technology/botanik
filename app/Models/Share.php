<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Share
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Share newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Share newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Share query()
 * @mixin \Eloquent
 */
class Share extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'text',
        'img',
        'button'
    ];
}

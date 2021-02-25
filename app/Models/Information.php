<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Information
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Information newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Information newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Information query()
 * @mixin \Eloquent
 */
class Information extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'text',
        'img',
        'button',
        'addresses'
    ];
}

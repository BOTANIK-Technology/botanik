<?php

namespace App\Models;

use App\Traits\RelationHelper;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceAddress extends Model
{
    use HasFactory, RelationHelper;

    public $table = "services_addresses";

    public $timestamps = false;

    protected $fillable = [
        'service_id',
        'address_id',
    ];

}

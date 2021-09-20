<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Api
 *
 * @property integer $id
 * @property string $config
 * @property string $slug
 * @method static Builder|Api newModelQuery()
 * @method static Builder|Api newQuery()
 * @method static Builder|Api query()
 * @mixin Eloquent
 */
class Api extends Model
{
    use HasFactory;

    /**
     * @param array $data
     * @return bool
     */
    public function updateConfig (array $data): bool
    {
        if (!empty($this->config) && !empty($data)) {

            $new_config = [];

            foreach (json_decode($this->config, JSON_OBJECT_AS_ARRAY) as $k => $item) {
                if (
                    isset($data[$k]) &&
                    $data[$k] !== $item
                )
                    $new_config[$k] = $data[$k];
            }

            $diff = array_diff_key($data, $new_config);
            if (!empty($diff)) {
                foreach ($diff as $key => $value)
                    $new_config[$key] = $data[$key];
            }

            $this->config = json_encode($new_config);

        } else {
            $this->config = json_encode($data);
        }

        return $this->save();
    }
}

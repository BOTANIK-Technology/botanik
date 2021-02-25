<?php

namespace App\Traits;


trait RelationHelper
{
    /**
     * If isset relation return array of the ids of relation objects
     * else return false.
     *
     * @param string $relation
     * @return array|bool
     */
    public function getIds(string $relation)
    {
        if (!isset($this->$relation))
            return false;

        $ids = [];
        foreach ($this->$relation as $obj)
            $ids[] = $obj->id;
        return $ids;
    }
}

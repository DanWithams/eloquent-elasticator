<?php

namespace DanWithams\EloquentElasticator\Models;

class SortItem
{
    public function __construct(protected string $field, protected string $order = 'asc', protected string $mode = 'avg')
    {

    }

    public function toArray()
    {
        return [
            $this->field => [
                'order' => $this->order,
                'mode' => $this->mode,
            ]
        ];
    }
}

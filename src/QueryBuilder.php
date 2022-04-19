<?php

namespace DanWithams\EloquentElasticator;

class QueryBuilder
{
    public function __construct()
    {

    }

    public static function query()
    {
        return new self();
    }

    public function toArray()
    {
        return [
            'query' => [
                'match' => [
                    'testField' => 'abc'
                ]
            ]
        ];
    }
}

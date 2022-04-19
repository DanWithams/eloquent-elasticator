<?php

namespace DanWithams\EloquentElasticator\Concerns;

interface Client
{
    public function index();

    public function delete();

    public function query();
}

<?php

namespace DanWithams\EloquentElasticator\Models;

class Field
{
    public function __construct(protected string $name, protected ?int $boost = null)
    {

    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getBoost(): ?int
    {
        return $this->boost;
    }

    public function setBoost(?int $boost): void
    {
        $this->boost = $boost;
    }

    public function __toString(): string
    {
        return $this->name . (!is_null($this->boost) ? '^' . $this->boost : '');
    }
}

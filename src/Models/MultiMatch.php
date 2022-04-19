<?php

namespace DanWithams\EloquentElasticator\Models;

use Ramsey\Collection\Collection;
use DanWithams\EloquentElasticator\Models\Contracts\MatchCriteria;

class MultiMatch implements MatchCriteria
{
    protected Collection $fields;
    protected string $type;
    protected string $operator = 'or';

    const TYPE_BEST_FIELDS = 'best_fields';
    const TYPE_MOST_FIELDS = 'most_fields';
    const TYPE_CROSS_FIELDS = 'cross_fields';
    const TYPE_PHRASE = 'phrase';
    const TYPE_PHRASE_PREFIX = 'phrase_prefix';
    const TYPE_BOOL_PREFIX = 'bool_prefix';

    const TYPES = [
        self::TYPE_BEST_FIELDS,
        self::TYPE_MOST_FIELDS,
        self::TYPE_CROSS_FIELDS,
        self::TYPE_PHRASE,
        self::TYPE_PHRASE_PREFIX,
        self::TYPE_BOOL_PREFIX,
    ];

    public function __construct(protected string $queryString = '')
    {
        $this->fields = collect();
        $this->type = collect(self::TYPES)->first();
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function setQueryString(string $queryString): self
    {
        $this->queryString = $queryString;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     */
    public function setOperator(string $operator): self
    {
        $operator = strtolower($operator);
        if (in_array($operator, ['and', 'or'])) {
            $this->operator = $operator;
        }

        return $this;
    }

    public function addField($name, $boost = 1): self
    {
        $this->fields->put($name, [
            'name' => $name,
            'boost' => $boost,
        ]);

        return $this;
    }

    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function clearFields(): self
    {
        $this->fields = collect();

        return $this;
    }

    public function toArray()
    {
        return [
            'multi_match' => [
                'query' => $this->queryString,
                'type' => $this->type,
                'fields' => $this->fields->map(fn (Field $field) => (string) $field)->values()->all(),
            ],
        ];
    }
}

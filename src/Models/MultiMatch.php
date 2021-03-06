<?php

namespace DanWithams\EloquentElasticator\Models;

use Illuminate\Support\Collection;
use DanWithams\EloquentElasticator\Models\Contracts\QueryCriteria;

class MultiMatch implements QueryCriteria
{
    protected Collection $fields;
    protected string $type;
    protected int|string|null $fuzziness = null;
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function setOperator(string $operator): self
    {
        $operator = strtolower($operator);
        if (in_array($operator, ['and', 'or'])) {
            $this->operator = $operator;
        }

        return $this;
    }

    public function getFuzziness(): int|string|null
    {
        return $this->fuzziness;
    }

    public function setFuzziness(int|string|null $fuzziness = null): self
    {
        if (!is_string($fuzziness) || $fuzziness === 'AUTO') {
            $this->fuzziness = $fuzziness;
        }

        return $this;
    }

    public function addField(Field $field): self
    {
        $this->fields->put($field->getName(), $field);

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
            'multi_match' => collect([
                    'query' => $this->queryString,
                    'type' => $this->type,
                    'fuzziness' => $this->fuzziness,
                    'fields' => $this->fields->map(fn (Field $field) => (string) $field)->values()->all(),
                ])
                ->filter(fn ($value) => $value)
                ->all(),
        ];
    }
}

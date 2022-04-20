<?php

namespace DanWithams\EloquentElasticator\Models;

use Illuminate\Support\Collection;
use DanWithams\EloquentElasticator\Models\Contracts\QueryCriteria;

class QueryString implements QueryCriteria
{
    protected Collection $fields;
    protected string $fuzziness = '';

    public function __construct(protected string $queryString = '')
    {
        $this->fields = collect();
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

    public function getFuzziness(): string
    {
        return $this->fuzziness;
    }

    public function setFuzziness(?string $fuzziness = null): self
    {
        if (is_null($fuzziness) || $fuzziness === 'AUTO') {
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
            'query_string' => collect([
                'query' => '*' . $this->queryString . '*',
                'fuzziness' => $this->fuzziness,
                'fields' => $this->fields->map(fn (Field $field) => (string) $field)->values()->all(),
            ])
                ->filter(fn ($value) => $value)
                ->all(),
        ];
    }
}

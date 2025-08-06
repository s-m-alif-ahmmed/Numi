<?php

namespace App\Services\Spoonacular;

class SpoonacularResultsService
{
    protected ?array $results = null;

    public function setResults(array $results): void
    {
        $this->results = $results;
    }

    public function getResults(): array
    {
        return $this->results ?? [];
    }

    public function hasResults(): bool
    {
        return ! empty($this->results);
    }

    public function clearResults(): void
    {
        $this->results = null;
    }
}

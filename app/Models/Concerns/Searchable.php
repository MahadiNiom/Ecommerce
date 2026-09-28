<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    /**
     * Build a case-insensitive "contains" pattern for a LIKE comparison.
     *
     * User input reaches the query as a binding, but `%`, `_` and the escape
     * character itself are still wildcard syntax and have to be neutralised or
     * a search for "50%" would match every row.
     */
    public static function searchPattern(string $term): string
    {
        return '%'.addcslashes(mb_strtolower(trim($term)), '%_\\').'%';
    }

    /**
     * Scope a query to records whose name contains the search term.
     */
    #[Scope]
    protected function matchingName(Builder $query, string $term): void
    {
        $query->whereRaw(
            'LOWER('.$query->getModel()->qualifyColumn('name').") LIKE ? ESCAPE '\\'",
            [static::searchPattern($term)]
        );
    }
}

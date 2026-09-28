<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Type-ahead endpoint backing the navbar search box. Read-only and public,
     * so it carries a rate limit rather than an authorization check.
     */
    public function __invoke(Request $request, SearchService $search): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        return response()->json($search->search($validated['q'] ?? ''));
    }
}

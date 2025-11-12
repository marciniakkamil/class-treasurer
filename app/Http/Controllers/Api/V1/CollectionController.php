<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Filters\CollectionFilters;
use App\Http\Requests\Api\V1\StoreCollectionRequest;
use App\Http\Requests\Api\V1\UpdateCollectionRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Collection;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class CollectionController extends BaseController
{
    use AuthorizesRequests;

    /**
     * GET /api/v1/collections
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
        /* @var \App\Models\User $user */
        $user = $request->user();

        $query = Collection::query()
            ->visibleTo($user)
            ->applyFilters(CollectionFilters::fromArray((array) $request->query('filters', [])));

        // Optional aggregates
        $include = collect((array) $request->query('include'));
        if ($include->contains('aggregates')) {
            $query->withDashboardAggregates();
        }

        // Sorting: comma-separated fields, prefix '-' for desc
        $sortParam = (string) $request->query('sort', '-created_at');
        $this->applySorting($query, $sortParam);

        // Pagination
        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage)->appends($request->query());

        return CollectionResource::collection($paginator);
    }

    /**
     * GET /api/v1/collections/{collection}
     */
    public function show(Request $request, Collection $collection): CollectionResource
    {
        $include = collect((array) $request->query('include'));
        if ($include->contains('aggregates')) {
            $collection->loadCount(['guardians', 'payments', 'expenses'])
                ->loadSum('payments', 'amount')
                ->loadSum('expenses', 'amount');
        }

        return new CollectionResource($collection);
    }

    /**
     * POST /api/v1/collections
     */
    public function store(StoreCollectionRequest $request): JsonResponse
    {
        $this->authorize('create', Collection::class);

        /* @var \App\Models\User $user */
        $user = $request->user();
        $data = $request->validated();

        $collection = null;

        DB::transaction(function () use ($user, $data) {
            $collection = Collection::query()->create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'school_year' => $data['school_year'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'active',
                'is_active' => (bool)($data['is_active'] ?? false),
            ]);
        });

        return (new CollectionResource($collection))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * PUT/PATCH /api/v1/collections/{collection}
     */
    public function update(UpdateCollectionRequest $request, Collection $collection): CollectionResource
    {
        $user = $request->user();
        if (! $user->isAdmin() && $user->id !== $collection->user_id) {
            abort(403);
        }
        $data = $request->validated();

        DB::transaction(function () use ($data, $collection) {
            $collection->fill($data);
            $collection->save();
        });

        return new CollectionResource($collection);
    }

    /**
     * DELETE /api/v1/collections/{collection}
     */
    public function destroy(Request $request, Collection $collection): JsonResponse
    {
        $user = $request->user();
        if (! $user->isAdmin() && $user->id !== $collection->user_id) {
            abort(403);
        }

        DB::transaction(function () use ($collection) {
            $collection->delete();
        });

        return response()->json(null, 204);
    }

    /**
     * Apply safe sorting rules.
     */
    private function applySorting(\App\Models\Builders\CollectionBuilder $query, string $sortParam): void
    {
        $fields = array_filter(array_map('trim', explode(',', $sortParam)));
        $allowed = [
            'created_at' => 'created_at',
            'name' => 'name',
            'school_year' => 'school_year',
        ];

        if ($fields === []) {
            $query->orderByDesc('created_at');

            return;
        }

        foreach ($fields as $field) {
            $direction = 'asc';
            if (str_starts_with($field, '-')) {
                $direction = 'desc';
                $field = substr($field, 1);
            }

            if (! array_key_exists($field, $allowed)) {
                continue;
            }

            $query->orderBy($allowed[$field], $direction);
        }
    }
}

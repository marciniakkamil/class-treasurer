<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Collections\CreateCollectionAction;
use App\Filters\CollectionFilters;
use App\Http\Requests\Api\V1\StoreCollectionRequest;
use App\Http\Requests\Api\V1\UpdateCollectionRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Collection;
use App\Services\CollectionReadService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
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
    public function index(Request $request, CollectionReadService $readService)
    {
        /* @var \App\Models\User $user */
        $user = $request->user();

        $filters = CollectionFilters::fromArray((array) $request->query('filters', []));
        $include = collect((array) $request->query('include'));
        $withAggregates = $include->contains('aggregates');
        $sort = (string) $request->query('sort', '-created_at');
        $perPage = (int) $request->query('per_page', 15);

        $paginator = $readService
            ->paginateForApi($user, $filters, $sort, $perPage, $withAggregates)
            ->appends($request->query());

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
    public function store(StoreCollectionRequest $request, CreateCollectionAction $action): JsonResponse
    {
        $this->authorize('create', Collection::class);

        /* @var \App\Models\User $user */
        $user = $request->user();
        $data = $request->validated();

        $collection = $action->execute($user, $data);

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
}

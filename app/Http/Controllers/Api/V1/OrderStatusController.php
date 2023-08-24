<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Filters\BaseFilter;
use App\Http\Requests\OrderStatusRequest;

class OrderStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, BaseFilter $filter): JsonResponse
    {
        $statuses = OrderStatus::filter($filter)
            ->paginate($request->get('limit', 10))
            ->withQueryString();

        return $this->success(data: $statuses, onlyData: true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderStatusRequest $request): JsonResponse
    {
        $status = OrderStatus::create($request->validated());
        return $this->success(['uuid' => $status->uuid]);
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderStatus $orderStatus): JsonResponse
    {
        return $this->success($orderStatus);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderStatusRequest $request, OrderStatus $orderStatus): JsonResponse
    {
        $orderStatus->update($request->validated());
        return $this->success($orderStatus);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderStatus $orderStatus): JsonResponse
    {
        $orderStatus->delete();
        return $this->success([]);
    }
}

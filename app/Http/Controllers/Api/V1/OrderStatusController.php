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
     * Get a listing of all order statuses.
     * @unauthenticated
     */
    public function index(Request $request, BaseFilter $filter): JsonResponse
    {
        $statuses = OrderStatus::filter($filter)
            ->paginate($request->get('limit', 10))
            ->withQueryString();

        return $this->success(data: $statuses, onlyData: true);
    }

    /**
     * Create an order status.
     */
    public function store(OrderStatusRequest $request): JsonResponse
    {
        $status = OrderStatus::create($request->validated());
        return $this->success(['uuid' => $status->uuid]);
    }

    /**
     * Get an existing order status.
     * @unauthenticated
     * @param OrderStatus $orderStatus The uuid of the order status
     */
    public function show(OrderStatus $orderStatus): JsonResponse
    {
        return $this->success($orderStatus);
    }

    /**
     * Update an existing order status.
     * @param OrderStatus $orderStatus The uuid of the order status
     */
    public function update(OrderStatusRequest $request, OrderStatus $orderStatus): JsonResponse
    {
        $orderStatus->update($request->validated());
        return $this->success($orderStatus);
    }

    /**
     * Delete an existing order status.
     * @param OrderStatus $orderStatus The uuid of the order status
     */
    public function destroy(OrderStatus $orderStatus): JsonResponse
    {
        $orderStatus->delete();
        return $this->success([]);
    }
}

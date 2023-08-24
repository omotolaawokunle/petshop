<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Filters\BaseFilter;
use App\Models\Payment;
use App\Http\Requests\PaymentRequest;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, BaseFilter $filter): JsonResponse
    {
        $payments = Payment::filter($filter)
            ->paginate($request->get('limit', 10))
            ->withQueryString();

        return $this->success(data: $payments, onlyData: true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentRequest $request): JsonResponse
    {
        $payment = Payment::create($request->toArray());
        return $this->success(['uuid' => $payment->uuid]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment): JsonResponse
    {
        return $this->success($payment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaymentRequest $request, Payment $payment): JsonResponse
    {
        $payment->update($request->toArray());
        return $this->success($payment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment): JsonResponse
    {
        $payment->delete();
        return $this->success([]);
    }
}

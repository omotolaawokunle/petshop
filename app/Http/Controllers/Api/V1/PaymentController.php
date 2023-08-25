<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Filters\BaseFilter;
use App\Http\Requests\PaymentRequest;

class PaymentController extends Controller
{
    /**
     * Get a listing of all payments.
     */
    public function index(Request $request, BaseFilter $filter): JsonResponse
    {
        $payments = Payment::filter($filter)
            ->paginate($request->get('limit', 10))
            ->withQueryString();

        return $this->success(data: $payments, onlyData: true);
    }

    /**
     * Create a payment.
     */
    public function store(PaymentRequest $request): JsonResponse
    {
        $payment = Payment::create($request->toArray());
        return $this->success(['uuid' => $payment->uuid]);
    }

    /**
     * Get an existing payment.
     * @param Payment $payment The uuid of the payment
     */
    public function show(Payment $payment): JsonResponse
    {
        return $this->success($payment);
    }

    /**
     * Update an existing payment.
     * @param Payment $payment The uuid of the payment
     */
    public function update(PaymentRequest $request, Payment $payment): JsonResponse
    {
        $payment->update($request->toArray());
        return $this->success($payment);
    }

    /**
     * Delete an existing payment.
     * @param Payment $payment The uuid of the payment
     */
    public function destroy(Payment $payment): JsonResponse
    {
        $payment->delete();
        return $this->success([]);
    }
}

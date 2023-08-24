<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\Payment;
use App\Models\Product;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'order_status_uuid' => 'required|string|exists:order_statuses,uuid',
            'payment_uuid' => 'required|string|exists:payments,uuid',
            'products' => 'required|array',
            'products.*.product' => 'required|string|exists:products,uuid',
            'products.*.quantity' => 'required|integer|min:1',
            'address' => 'required|array',
            'address.shipping' => 'required|string',
            'address.billing' => 'required|string',
        ];
    }

    public function toArray(): array
    {
        /** @var OrderStatus $orderStatus */
        $orderStatus = OrderStatus::where('uuid', $this->get('order_status_uuid'))->first();
        /** @var Payment $payment */
        $payment = Payment::where('uuid', $this->get('payment_uuid'))->first();
        /** @var User $user */
        $user = Auth::user();
        return [
            'order_status_id' => $orderStatus->id,
            'payment_id' => $payment->id,
            'user_id' => $user->id,
            'products' => $this->get('products'),
            'address' => $this->get('address'),
            'delivery_fee' => $this->getDeliveryFee(),
            'amount' => $this->getOrderAmount(),
            'shipped_at' => strtolower($orderStatus->title) === 'shipped' ? now() : null,
        ];
    }

    public function getOrderAmount(): float
    {
        return (float) collect($this->get('products'))->map(function ($row) {
            $product = Product::select('uuid', 'price')
                ->where('uuid', $row['product'])
                ->first();
            return $product->price * $row['quantity'];
        })->sum();
    }

    public function getDeliveryFee(): float
    {
        if ($this->getOrderAmount() > 500) return (float) 15;
        return (float) 0;
    }
}

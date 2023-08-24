<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\OrderRequest;
use App\Http\Controllers\Controller;
use App\Services\Filters\BaseFilter;
use App\Http\Resources\OrderResource;
use App\Services\Filters\OrderFilter;
use App\Http\Resources\OrderCollection;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Facades\Invoice;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, BaseFilter $filter): JsonResponse
    {
        $orders = Order::filter($filter)
            ->paginate($request->get('limit', 10))
            ->withQueryString();

        return $this->success(data: new OrderCollection($orders), onlyData: true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request): JsonResponse
    {
        $order = Order::create($request->toArray());
        return $this->success(['uuid' => $order->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): JsonResponse
    {
        return $this->success(new OrderResource($order));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderRequest $request, Order $order): JsonResponse
    {
        $order->update($request->toArray());
        return $this->success(new OrderResource($order->fresh()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): JsonResponse
    {
        if ($order->delete()) {
            return $this->success([]);
        }
        return $this->error("Error deleting order!");
    }

    public function shippedOrders(Request $request, OrderFilter $filter): JsonResponse
    {
        $orders = Order::filter($filter)
            ->whereHas('orderStatus', function ($query) {
                return $query->where('order_statuses.title', 'shipped');
            })
            ->paginate($request->get('limit', 10))
            ->withQueryString();
        return $this->success(data: new OrderCollection($orders), onlyData: true);
    }

    public function dashboard(Request $request, OrderFilter $filter): JsonResponse
    {
        $orders = Order::filter($filter)
            ->paginate($request->get('limit', 10))
            ->withQueryString();
        return $this->success(data: new OrderCollection($orders), onlyData: true);
    }

    public function downloadInvoice(Order $order): StreamedResponse
    {
        $items = collect($order->products)
            ->map(fn ($product) => $this->getInvoiceItem($product))
            ->all();
        $customer = $this->getCustomerData($order->user);
        $invoice = Invoice::make()
            ->name('Petshop')
            ->buyer($customer)
            ->addItems($items)
            ->series(Str::orderedUuid())
            ->serialNumberFormat('{SEQUENCE}')
            ->filename($order->uuid)
            ->currencySymbol('$')
            ->currencyCode('USD')
            ->currencyFormat('{SYMBOL} {VALUE}')
            ->shipping($order->delivery_fee);

        $invoice->order = $order;

        return response()->streamDownload(fn () => $invoice->stream(), "{$order->uuid}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }

    protected function getCustomerData(User $user): Buyer
    {
        return new Buyer([
            'name' => $user->first_name,
            'custom_fields' => [
                'last_name' => $user->last_name,
                'ID' => $user->uuid,
                'phone' => $user->phone_number,
                'email' => $user->email,
                'address' => $user->address,
            ]
        ]);
    }

    protected function getInvoiceItem(array $product): InvoiceItem
    {
        return (new InvoiceItem())
            ->title($product['product'])
            ->pricePerUnit($product['price'])
            ->quantity($product['quantity'])
            ->description($product['uuid']);
    }
}

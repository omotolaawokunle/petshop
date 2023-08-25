<?php

namespace App\Services\Traits;

use App\Models\User;
use Illuminate\Support\Str;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;

trait Invoicable
{
    public function getCustomerData(User $user): Buyer
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

    public function getInvoiceItem(array $product): InvoiceItem
    {
        return (new InvoiceItem())
            ->title($product['product'])
            ->pricePerUnit($product['price'])
            ->quantity($product['quantity'])
            ->description($product['uuid']);
    }

    /**
     * @param  array<array> $items
     */
    public function createInvoice(User $customer, string $name, float $deliveryFee, string $filename, array $items): Invoice
    {
        $customer = $this->getCustomerData($customer);
        $items = collect($items)
            ->map(fn ($item) => $this->getInvoiceItem($item))
            ->all();

        return Invoice::make()
            ->name($name)
            ->buyer($customer)
            ->addItems($items)
            ->series(Str::orderedUuid())
            ->serialNumberFormat('{SEQUENCE}')
            ->filename($filename)
            ->currencySymbol('$')
            ->currencyCode('USD')
            ->currencyFormat('{SYMBOL} {VALUE}')
            ->shipping($deliveryFee);
    }
}

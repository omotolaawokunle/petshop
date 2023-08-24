<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\PaymentType;

class PaymentRequest extends FormRequest
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
        $requiredIfCreditCard = Rule::requiredIf(fn () => $this->get('type') == PaymentType::CreditCard->value);
        $requiredIfCash = Rule::requiredIf(fn () => $this->get('type') == PaymentType::CashOnDelivery->value);
        $requiredIfTransfer = Rule::requiredIf(fn () => $this->get('type') == PaymentType::BankTransfer->value);
        return [
            'type' => ['required', new Enum(PaymentType::class)],
            'details' => ['required', 'array'],
            'details.holder_name' => [$requiredIfCreditCard, 'string'],
            'details.number' => [$requiredIfCreditCard, 'string'],
            'details.ccv' => [$requiredIfCreditCard, 'integer'],
            'details.expire_date' => [$requiredIfCreditCard, 'string'],
            'details.first_name' => [$requiredIfCash, 'string'],
            'details.last_name' => [$requiredIfCash, 'string'],
            'details.address' => [$requiredIfCash, 'string'],
            'details.swift' => [$requiredIfTransfer, 'string'],
            'details.iban' => [$requiredIfTransfer, 'string'],
            'details.name' => [$requiredIfTransfer, 'string'],
        ];
    }

    public function toArray(): array
    {
        $details = $this->get('details');
        $type = $this->get('type');
        return match ($this->get('type')) {
            PaymentType::CreditCard->value  => [
                'type' => $type,
                'details' => [
                    'holder_name' => $details['holder_name'],
                    'number' => $details['number'],
                    'ccv' => $details['ccv'],
                    'expire_date' => $details['expire_date'],
                ]
            ],
            PaymentType::CashOnDelivery->value => [
                'type' => $type,
                'details' => [
                    'first_name' => $details['first_name'],
                    'last_name' => $details['last_name'],
                    'address' => $details['address'],
                ]
            ],
            PaymentType::BankTransfer->value => [
                'type' => $type,
                'details' => [
                    'swift' => $details['swift'],
                    'iban' => $details['iban'],
                    'name' => $details['name'],
                ]
            ],
        };
    }
}

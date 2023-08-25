<?php

namespace App\Http\Requests;

use App\Enums\PaymentType;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

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
        $type = strtolower($this->get('type'));
        $requiredIfCreditCard = Rule::requiredIf(fn () => $type === PaymentType::CreditCard->value);
        $requiredIfCash = Rule::requiredIf(fn () => $type === PaymentType::CashOnDelivery->value);
        $requiredIfTransfer = Rule::requiredIf(fn () => $type === PaymentType::BankTransfer->value);
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
        switch ($type) {
            case PaymentType::CreditCard->value:
                return $this->getCreditCardValues($type, $details);
            case PaymentType::CashOnDelivery->value:
                return $this->getCashValues($type, $details);
            case PaymentType::BankTransfer->value:
                return $this->getBankTransferValues($type, $details);
            default:
                return ['type' => $type, 'details' => $details];
        }
    }

    /**
     * @param  array<string|array> $details
     * @return array
     */
    public function getCreditCardValues(string $type, array $details): array
    {
        return [
            'type' => $type,
            'details' => [
                'holder_name' => $details['holder_name'],
                'number' => $details['number'],
                'ccv' => $details['ccv'],
                'expire_date' => $details['expire_date'],
            ]
        ];
    }

    /**
     * @param  array<string|array> $details
     * @return array
     */
    public function getBankTransferValues(string $type, array $details): array
    {
        return [
            'type' => $type,
            'details' => [
                'swift' => $details['swift'],
                'iban' => $details['iban'],
                'name' => $details['name'],
            ]
        ];
    }

    /**
     * @param  array<string|array> $details
     * @return array
     */
    public function getCashValues(string $type, array $details): array
    {
        return [
            'type' => $type,
            'details' => [
                'first_name' => $details['first_name'],
                'last_name' => $details['last_name'],
                'address' => $details['address'],
            ]
        ];
    }
}

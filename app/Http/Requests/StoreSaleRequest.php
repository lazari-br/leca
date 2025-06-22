<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ou implemente sua lógica de autorização
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'payment_date' => 'required|date',
            'customer_name' => 'nullable|string|max:255',
            'sale_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'installments' => 'nullable|integer|min:1',
            'installment_value' => 'nullable|numeric|min:0',
            'commission_value' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variation_id' => 'required|exists:product_variations,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'payment_date.required' => 'A data de pagamento é obrigatória.',
            'payment_date.date' => 'A data de pagamento deve ser uma data válida.',
            'customer_name.string' => 'O nome do cliente deve ser um texto válido.',
            'customer_name.max' => 'O nome do cliente não pode ter mais de 255 caracteres.',
            'sale_date.required' => 'A data da venda é obrigatória.',
            'sale_date.date' => 'A data da venda deve ser uma data válida.',
            'payment_method.required' => 'O método de pagamento é obrigatório.',
            'payment_method.string' => 'O método de pagamento deve ser um texto válido.',
            'payment_method.max' => 'O método de pagamento não pode ter mais de 255 caracteres.',
            'installments.integer' => 'O número de parcelas deve ser um número inteiro.',
            'installments.min' => 'O número de parcelas deve ser no mínimo 1.',
            'installment_value.numeric' => 'O valor da parcela deve ser um número válido.',
            'installment_value.min' => 'O valor da parcela deve ser maior ou igual a zero.',
            'commission_value.numeric' => 'O valor da comissão deve ser um número válido.',
            'commission_value.min' => 'O valor da comissão deve ser maior ou igual a zero.',
            'items.required' => 'É necessário informar pelo menos um item.',
            'items.array' => 'Os itens devem estar em formato de lista.',
            'items.min' => 'É necessário informar pelo menos um item.',
            'items.*.product_id.required' => 'O produto é obrigatório para cada item.',
            'items.*.product_id.exists' => 'O produto selecionado não existe.',
            'items.*.product_variation_id.required' => 'A variação do produto é obrigatória para cada item.',
            'items.*.product_variation_id.exists' => 'A variação do produto selecionada não existe.',
            'items.*.quantity.required' => 'A quantidade é obrigatória para cada item.',
            'items.*.quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'items.*.quantity.min' => 'A quantidade deve ser no mínimo 1.',
            'items.*.unit_price.required' => 'O preço unitário é obrigatório para cada item.',
            'items.*.unit_price.numeric' => 'O preço unitário deve ser um número válido.',
            'items.*.unit_price.min' => 'O preço unitário deve ser maior ou igual a zero.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'payment_date' => 'data de pagamento',
            'customer_name' => 'nome do cliente',
            'sale_date' => 'data da venda',
            'payment_method' => 'método de pagamento',
            'installments' => 'número de parcelas',
            'installment_value' => 'valor da parcela',
            'commission_value' => 'valor da comissão',
            'items' => 'itens',
            'items.*.product_id' => 'produto',
            'items.*.product_variation_id' => 'variação do produto',
            'items.*.quantity' => 'quantidade',
            'items.*.unit_price' => 'preço unitário',
        ];
    }
}

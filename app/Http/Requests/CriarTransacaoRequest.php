<?php

namespace App\Http\Requests;

use App\Enums\MetodosPagamentoEnum;
use App\Enums\TipoCobrancaEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CriarTransacaoRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string'],
            'descricao' => ['required', 'string'],
            'dataLimite' => ['required', 'date', 'after_or_equal:tomorrow'],
            'valor' => ['required', 'numeric', 'min:10'],
            'metodoPagamento' => ['required', Rule::enum(MetodosPagamentoEnum::class)],
            'tipoCobranca' => ['required', Rule::enum(TipoCobrancaEnum::class)],
            'clienteId' => ['nullable', 'integer'],
        ];
    }
}

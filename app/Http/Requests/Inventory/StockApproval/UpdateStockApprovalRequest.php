<?php

namespace App\Http\Requests\Inventory\StockApproval;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockApprovalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Otorisasi sudah ditangani secara manual di controller (menggunakan $this->authorize)
        // Namun, praktiknya lebih baik ditangani di controller untuk Model spesifik atau menggunakan gate
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|max:500',
        ];
    }
}

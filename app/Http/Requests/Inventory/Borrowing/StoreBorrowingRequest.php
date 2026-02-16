<?php

namespace App\Http\Requests\Inventory\Borrowing;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Sparepart;

class StoreBorrowingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Main auth handled by Policy/Route
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'expected_return_at' => 'required|date|after_or_equal:today',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $sparepart = $this->route('sparepart'); // Get sparepart from route binding
            
            if ($sparepart instanceof Sparepart) {
                $check = $sparepart->canBeBorrowed($this->input('quantity', 0));
                
                if ($check !== true) {
                    // Check is either true or error string
                    $validator->errors()->add('borrow_error', $check);
                }
            }
        });
    }
}

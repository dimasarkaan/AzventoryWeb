<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('location'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $location = $this->route('location');
        $locationId = $location ? $location->id : null;

        return [
            'name' => 'required|string|max:191|unique:locations,name,'.$locationId,
            'is_active' => 'sometimes|boolean',
        ];
    }
}

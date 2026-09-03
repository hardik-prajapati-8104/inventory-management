<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id'  => ['nullable', 'integer', 'exists:menus,id'],
            'name'       => ['required', 'string', 'max:255'],
            'icon'       => ['nullable', 'string', 'max:100'],
            'is_heading' => ['sometimes', 'boolean'],
            'link_type'  => ['required_unless:is_heading,1', Rule::in(['route', 'url'])],
            'route_name' => ['nullable', 'required_if:link_type,route', 'string', 'max:150'],
            'url'        => ['nullable', 'required_if:link_type,url', 'string', 'max:255'],
            'permission' => ['nullable', 'string', 'max:150'],
            'target'     => ['nullable', Rule::in(['_self', '_blank'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_heading' => $this->boolean('is_heading'),
            'is_active'  => $this->has('is_active') ? $this->boolean('is_active') : true,
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->route_name && ! Route::has($this->route_name)) {
                $validator->errors()->add('route_name', 'The selected route name does not exist.');
            }
        });
    }
}

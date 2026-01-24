<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'parent_id' => $this->parent_id ?: null,
        ]);
    }

    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug,'.$this->route('category')->id],
            'status' => ['required', 'boolean'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string'],
        ];
    }
}

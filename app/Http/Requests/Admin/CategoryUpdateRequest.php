<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug' . ($this->category ? ',' . $this->category->id : '')],
            'description' => ['nullable', 'string'],
            'image' => [
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,webp',
                'max:2048', // 2MB max
            ],
            'is_featured' => ['boolean'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'icon' => ['nullable', 'string', 'max:50'],
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->name),
        ]);
    }
}

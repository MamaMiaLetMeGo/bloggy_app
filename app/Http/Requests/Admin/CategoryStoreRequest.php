<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CategoryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Later we can add proper authorization
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug' . ($this->category ? ',' . $this->category->id : '')],
            'description' => ['nullable', 'string'],
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->name),
        ]);
    }
}
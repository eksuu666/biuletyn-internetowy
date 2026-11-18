<?php

namespace Modules\Home\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulletinIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization is enforced by the controller's permission middleware.
    }

    public function rules(): array
    {
        $required = $this->isMethod('POST') ? 'required' : 'nullable';

        return [
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:500'],
            'published_on' => ['required', 'date_format:Y-m-d'],
            'is_published' => ['required', 'boolean'],
            'pdf' => [$required, 'file', 'mimes:pdf', 'max:51200'],
            'cover' => [$required, 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:max_width=5000,max_height=7000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_published' => $this->boolean('is_published')]);
    }

    public function attributes(): array
    {
        return ['title' => 'tytuł', 'description' => 'opis', 'published_on' => 'data wydania', 'pdf' => 'plik PDF', 'cover' => 'okładka'];
    }
}

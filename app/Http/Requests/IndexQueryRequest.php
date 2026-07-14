<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class IndexQueryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'   => ['sometimes', 'nullable', 'string', 'max:255'],
            'page'     => ['sometimes', 'nullable', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:10'],
            'tags'     => ['sometimes', 'nullable', 'array'],
            'tags.*'   => ['string'], // inside tags array value must be string
            'sort'     => ['sometimes', 'nullable', 'string', Rule::in(['latest', 'oldest', 'title', 'views'])],
        ];
    }

    // reject any query param not in rules()
    protected function prepareForValidation()
    {
        $allowed = array_keys($this->rules()); // ['search','status']
        $unknown = array_diff(array_keys($this->query()), $allowed);

        if (! empty($unknown)) {
            throw new HttpResponseException(
                response()->json([
                    'status'  => false,
                    'message' => 'Unknown query parameter(s): ' . implode(', ', $unknown),
                ], 422)
            );
        }

        if ($this->has('tags')) {
            $requestedTags = is_array($this->tags) ? $this->tags : explode(',', $this->tags);
            $this->merge([
                'tags' => array_map('trim', $requestedTags),
            ]);
        }
    }
}

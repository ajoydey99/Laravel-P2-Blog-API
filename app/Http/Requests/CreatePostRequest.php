<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePostRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'    => 'required|string|max:255|min:5',
            'subtitle' => 'nullable|string|max:255',
            'body'     => 'required|string|max:255|min:5',
            'image'    => 'sometimes|image|max:2048',
            'tags'     => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please fill title field',
            'body.required'  => 'Please fill body field',
        ];
    }
}

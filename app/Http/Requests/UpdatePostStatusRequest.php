<?php
namespace App\Http\Requests;

use App\Enums\PostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(PostStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'status.enum' => 'Invalid status. Allowed values: 0 (Pending), 1 (Published), 2 (Rejected)',
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdminScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'bus_id' => ($isUpdate ? 'sometimes' : 'required') . '|exists:buses,id',
            'route_id' => ($isUpdate ? 'sometimes' : 'required') . '|exists:routes,id',
            'departure_time' => ($isUpdate ? 'sometimes' : 'required') . '|date|after:now',
            'arrival_time' => ($isUpdate ? 'sometimes' : 'required') . '|date|after:departure_time',
            'price' => ($isUpdate ? 'sometimes' : 'required') . '|numeric|min:0',
            'available_seats' => ($isUpdate ? 'sometimes' : 'required') . '|integer|min:0',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 422));
    }
}

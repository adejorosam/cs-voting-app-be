<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $todayDate = date('m/d/Y');
        return [
            'name' => 'string|required',
            'company_id' => 'integer|required|exists:companies,id',
            'date' => 'required|date_format:Y-m-d|after:'.$todayDate
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaletEntradaValidate extends FormRequest
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
        return [
            // 'sub_lote_id' => 'required|numeric',
            'peso' => 'required|between:0,999.99',
            'num_jabas' => 'required|numeric',

        ];
    }
    
    protected function failedValidation(Validator $validator) {
        //extraer array
        $sin_array=str_replace(["[","]"], "",json_encode($validator->errors()));
        
        throw new HttpResponseException(response()->json([
            "status" => "VALIDATION",
            "data"   =>  json_decode($sin_array)
        ], 200));
    }
}

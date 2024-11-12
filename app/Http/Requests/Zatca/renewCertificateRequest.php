<?php

namespace App\Http\Requests\Zatca;

use Illuminate\Foundation\Http\FormRequest;

class renewCertificateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'otp'=>'required'
        ];
    }
    public function authorize()
    {
        return true;
    }
}

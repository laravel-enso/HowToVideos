<?php

namespace LaravelEnso\HowTo\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateTagRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return ['name' => 'required'];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeviceRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rule_txt = 'required';
        if (request()->filled('device_id')) {
            $rule_added = ['device_id' => $rule_txt.'|exists:devices,id'];
            $rule_txt = 'nullable';
        } else {
            $rule_added = ['device_temp_id' => $rule_txt.'|exists:device_temps,id'];
        }
        return $rule_added  + [
            'project'    => $rule_txt,
            'machine'    => $rule_txt,
            'process'    => $rule_txt,
            'version'    => $rule_txt,
            'country_id' => $rule_txt.'|exists:countries,id',
            'city_id'    => $rule_txt.'|exists:countries,id',
        ];
    }
}

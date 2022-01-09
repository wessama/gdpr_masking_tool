<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreMassRTBFRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        abort_if(Gate::denies('request_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

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
            'instance_id'   => 'required|uuid',
            'mass_request'  => 'required_without_all:file,email|string',
            'file'          => 'required_without_all:mass_request,email|file|max:2097152|mimes:csv,txt',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'instance_id.required'          => 'Please choose an instance',
            'instance_id.uuid'              => 'Please choose an instance',
            'file.required_without'         => 'Please upload a file',
            'mass_request.required_without'  => 'No file uploaded'
        ];
    }

}

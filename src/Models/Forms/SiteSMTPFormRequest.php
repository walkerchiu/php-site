<?php

namespace WalkerChiu\Site\Models\Forms;

use Illuminate\Support\Facades\Request;
use WalkerChiu\Core\Models\Forms\FormRequest;

class SiteSMTPFormRequest extends FormRequest
{
    /**
     * @Override Illuminate\Foundation\Http\FormRequest::getValidatorInstance
     */
    protected function getValidatorInstance()
    {
        $request = Request::instance();
        $data = $this->all();
        if (
            $request->isMethod('put')
            && empty($data['id'])
            && isset($request->id)
        ) {
            $data['id'] = (string) $request->id;
            $this->getInputSource()->replace($data);
        }

        return parent::getValidatorInstance();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return Array
     */
    public function attributes()
    {
        return [
            'smtp_host'       => trans('php-site::site.smtp_host'),
            'smtp_port'       => trans('php-site::site.smtp_port'),
            'smtp_encryption' => trans('php-site::site.smtp_encryption'),
            'smtp_username'   => trans('php-site::site.smtp_username'),
            'smtp_password'   => trans('php-site::site.smtp_password')
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return Array
     */
    public function rules()
    {
        $rules = [
            'smtp_host'       => 'nullable|required_with:smtp_port|string|min:7|max:255',
            'smtp_port'       => 'nullable|required_with:smtp_encryption|numeric|min:1|max:65535',
            'smtp_encryption' => 'nullable|required_with:smtp_username|string|min:2|max:5',
            'smtp_username'   => 'nullable|required_with:smtp_password|string|min:2|max:255',
            'smtp_password'   => 'nullable|required_with:smtp_username|string|min:4|max:255'
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','string','exists:'.config('wk-core.table.site.sites').',id']]);
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return Array
     */
    public function messages()
    {
        return [
            'id.required'                   => trans('php-core::validation.required'),
            'id.string'                     => trans('php-core::validation.string'),
            'id.exists'                     => trans('php-core::validation.exists'),
            'smtp_host.required_with'       => trans('php-core::validation.required_with'),
            'smtp_host.string'              => trans('php-core::validation.string'),
            'smtp_host.min'                 => trans('php-core::validation.min'),
            'smtp_host.max'                 => trans('php-core::validation.max'),
            'smtp_port.required_with'       => trans('php-core::validation.required_with'),
            'smtp_port.numeric'             => trans('php-core::validation.string'),
            'smtp_port.min'                 => trans('php-core::validation.min'),
            'smtp_port.max'                 => trans('php-core::validation.max'),
            'smtp_encryption.required_with' => trans('php-core::validation.required_with'),
            'smtp_encryption.string'        => trans('php-core::validation.string'),
            'smtp_encryption.min'           => trans('php-core::validation.min'),
            'smtp_encryption.max'           => trans('php-core::validation.max'),
            'smtp_username.required_with'   => trans('php-core::validation.required_with'),
            'smtp_username.string'          => trans('php-core::validation.string'),
            'smtp_username.min'             => trans('php-core::validation.min'),
            'smtp_username.max'             => trans('php-core::validation.max'),
            'smtp_password.required_with'   => trans('php-core::validation.required_with'),
            'smtp_password.string'          => trans('php-core::validation.string'),
            'smtp_password.min'             => trans('php-core::validation.min'),
            'smtp_password.max'             => trans('php-core::validation.max')
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        //
    }
}

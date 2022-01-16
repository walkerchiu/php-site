<?php

namespace WalkerChiu\Site\Models\Forms;

use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use WalkerChiu\Core\Models\Forms\FormRequest;

class EmailFormRequest extends FormRequest
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
            'site_id'     => trans('php-site::email.site_id'),
            'type'        => trans('php-site::email.type'),
            'serial'      => trans('php-site::email.serial'),
            'is_enabled'  => trans('php-site::email.is_enabled'),

            'name'        => trans('php-site::email.name'),
            'description' => trans('php-site::email.description'),
            'subject'     => trans('php-site::email.subject'),
            'content'     => trans('php-site::email.content'),

            'email_register_id' => trans('php-site::email.emailType.register'),
            'email_login_id'    => trans('php-site::email.emailType.login'),
            'email_reset_id'    => trans('php-site::email.emailType.reset'),
            'email_checkout_id' => trans('php-site::email.emailType.checkout'),
            'email_order_id'    => trans('php-site::email.emailType.order')
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
            'site_id'     => ['required','string','exists:'.config('wk-core.table.site.sites').',id'],
            'type'        => ['required', Rule::in(config('wk-core.class.site.emailType')::getCodes())],
            'serial'      => '',
            'is_enabled'  => 'boolean',

            'name'        => 'required|string|max:255',
            'description' => '',
            'subject'     => 'required|string|max:255',
            'content'     => 'required',

            'email_register_id' => ['nullable', 'string', 'exists:'.config('wk-core.table.site.emails').',id'],
            'email_login_id'    => ['nullable', 'string', 'exists:'.config('wk-core.table.site.emails').',id'],
            'email_reset_id'    => ['nullable', 'string', 'exists:'.config('wk-core.table.site.emails').',id'],
            'email_checkout_id' => ['nullable', 'string', 'exists:'.config('wk-core.table.site.emails').',id'],
            'email_order_id'    => ['nullable', 'string', 'exists:'.config('wk-core.table.site.emails').',id']
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','string','exists:'.config('wk-core.table.site.emails').',id']]);
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
            'id.required'        => trans('php-core::validation.required'),
            'id.string'          => trans('php-core::validation.string'),
            'id.exists'          => trans('php-core::validation.exists'),
            'site_id.required'   => trans('php-core::validation.required'),
            'site_id.string'     => trans('php-core::validation.string'),
            'site_id.exists'     => trans('php-core::validation.exists'),
            'type.required'      => trans('php-core::validation.required'),
            'type.in'            => trans('php-core::validation.in'),
            'is_enabled.boolean' => trans('php-core::validation.boolean'),

            'name.required'      => trans('php-core::validation.required'),
            'name.string'        => trans('php-core::validation.string'),
            'name.max'           => trans('php-core::validation.max'),
            'subject.required'   => trans('php-core::validation.required'),
            'subject.string'     => trans('php-core::validation.string'),
            'subject.max'        => trans('php-core::validation.max'),
            'content.required'   => trans('php-core::validation.required'),

            'email_register_id.string' => trans('php-core::validation.string'),
            'email_register_id.exists' => trans('php-core::validation.exists'),
            'email_login_id.string'    => trans('php-core::validation.string'),
            'email_login_id.exists'    => trans('php-core::validation.exists'),
            'email_reset_id.string'    => trans('php-core::validation.string'),
            'email_reset_id.exists'    => trans('php-core::validation.exists'),
            'email_checkout_id.string' => trans('php-core::validation.string'),
            'email_checkout_id.exists' => trans('php-core::validation.exists'),
            'email_order_id.string'    => trans('php-core::validation.string'),
            'email_order_id.exists'    => trans('php-core::validation.exists')
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
    }
}

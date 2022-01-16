<?php

namespace WalkerChiu\Site\Models\Forms;

use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use WalkerChiu\Core\Models\Forms\FormRequest;
use WalkerChiu\Currency\Models\Services\CurrencyService;

class SiteFormRequest extends FormRequest
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
            'type'               => trans('php-site::site.type'),
            'serial'             => trans('php-site::site.serial'),
            'vat'                => trans('php-site::site.vat'),
            'identifier'         => trans('php-site::site.identifier'),
            'language'           => trans('php-site::site.language'),
            'language_supported' => trans('php-site::site.language_supported'),
            'timezone'           => trans('php-site::site.timezone'),
            'area_supported'     => trans('php-site::site.area_supported'),
            'currency_id'        => trans('php-site::site.currency_id'),
            'currency_supported' => trans('php-site::site.currency_supported'),
            'view_template'      => trans('php-site::site.view_template'),
            'email_template'     => trans('php-site::site.email_template'),
            'skin'               => trans('php-site::site.skin'),
            'script_head'        => trans('php-site::stock.script_head'),
            'script_footer'      => trans('php-site::stock.script_footer'),
            'options'            => trans('php-site::stock.options'),
            'images'             => trans('php-site::site.images'),
            'can_guestOrder'     => trans('php-site::site.can_guestOrder'),
            'can_guestComment'   => trans('php-site::site.can_guestComment'),
            'is_main'            => trans('php-site::site.is_main'),
            'is_enabled'         => trans('php-site::site.is_enabled'),

            'smtp_host'          => trans('php-site::site.smtp_host'),
            'smtp_port'          => trans('php-site::site.smtp_port'),
            'smtp_encryption'    => trans('php-site::site.smtp_encryption'),
            'smtp_username'      => trans('php-site::site.smtp_username'),
            'smtp_password'      => trans('php-site::site.smtp_password'),

            'name'               => trans('php-site::site.name'),
            'description'        => trans('php-site::site.description'),
            'keywords'           => trans('php-site::site.keywords'),
            'remarks'            => trans('php-site::site.remarks')
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
            'type'               => '',
            'serial'             => '',
            'vat'                => '',
            'identifier'         => 'required|string|max:255',
            'language'           => ['required', Rule::in(config('wk-core.class.core.language')::getCodes())],
            'language_supported' => 'required|array',
            'timezone'           => ['required', 'timezone', Rule::in(config('wk-core.class.core.timeZone')::getValues())],
            'area_supported'     => 'required|array',
            'view_template'      => '',
            'email_template'     => '',
            'skin'               => '',
            'script_head'        => '',
            'script_footer'      => '',
            'options'            => 'nullable|json',
            'images'             => 'nullable|json',
            'can_guestOrder'     => 'boolean',
            'can_guestComment'   => 'boolean',
            'is_main'            => 'boolean',
            'is_enabled'         => 'boolean',

            'smtp_host'          => 'nullable|required_with:smtp_port|string|min:7|max:255',
            'smtp_port'          => 'nullable|required_with:smtp_encryption|numeric|min:1|max:65535',
            'smtp_encryption'    => 'nullable|required_with:smtp_username|string|min:2|max:5',
            'smtp_username'      => 'nullable|required_with:smtp_password|string|min:2|max:255',
            'smtp_password'      => 'nullable|required_with:smtp_username|string|min:4|max:255',

            'name'               => 'required|string|max:255',
            'description'        => '',
            'keywords'           => '',
            'remarks'            => ''
        ];
        if (config('wk-site.onoff.currency')) {
            $service = new CurrencyService();
            $rules = array_merge($rules, [
                'currency_id'        => ['required', Rule::in($service->getEnabledSettingId())],
                'currency_supported' => 'required|array'
            ]);
        } else {
            $rules = array_merge($rules, [
                'currency_id'        => '',
                'currency_supported' => 'nullable|array'
            ]);
        }

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
            'id.required'                 => trans('php-core::validation.required'),
            'id.string'                   => trans('php-core::validation.string'),
            'id.exists'                   => trans('php-core::validation.exists'),
            'identifier.required'         => trans('php-core::validation.required'),
            'identifier.max'              => trans('php-core::validation.max'),
            'language.required'           => trans('php-core::validation.required'),
            'language.in'                 => trans('php-core::validation.in'),
            'language_supported.required' => trans('php-core::validation.required'),
            'language_supported.array'    => trans('php-core::validation.array'),
            'timezone.required'           => trans('php-core::validation.required'),
            'timezone.timezone'           => trans('php-core::validation.timezone'),
            'timezone.in'                 => trans('php-core::validation.in'),
            'area_supported.required'     => trans('php-core::validation.required'),
            'area_supported.array'        => trans('php-core::validation.array'),
            'currency_id.required'        => trans('php-core::validation.required'),
            'currency_id.in'              => trans('php-core::validation.in'),
            'currency_supported.required' => trans('php-core::validation.required'),
            'currency_supported.array'    => trans('php-core::validation.array'),
            'options.json'                => trans('php-core::validation.json'),
            'images.json'                 => trans('php-core::validation.json'),
            'can_guestOrder.boolean'      => trans('php-core::validation.boolean'),
            'can_guestComment.boolean'    => trans('php-core::validation.boolean'),
            'is_main.boolean'             => trans('php-core::validation.boolean'),
            'is_enabled.boolean'          => trans('php-core::validation.boolean'),

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
            'smtp_password.max'             => trans('php-core::validation.max'),

            'name.required'       => trans('php-core::validation.required'),
            'name.string'         => trans('php-core::validation.string'),
            'name.max'            => trans('php-core::validation.max')
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
        $validator->after( function ($validator) {
            $data = $validator->getData();
            if (
                isset($data['language_supported'])
                && is_array($data['language_supported'])
            ) {
                foreach ($data['language_supported'] as $item) {
                    if (!in_array($item, config('wk-core.class.core.language')::getCodes()))
                        $validator->errors()->add('language_supported', trans('php-core::validation.in'));
                }
            }
            if (
                isset($data['area_supported'])
                && is_array($data['area_supported'])
            ) {
                foreach ($data['area_supported'] as $item) {
                    if (!in_array($item, config('wk-core.class.core.countryZone')::getCodes()))
                        $validator->errors()->add('area_supported', trans('php-core::validation.in'));
                }
            }
            if (config('wk-site.onoff.currency')) {
                if (
                    isset($data['currency_supported'])
                    && is_array($data['currency_supported'])
                ) {
                    $service = new CurrencyService();
                    foreach ($data['currency_supported'] as $item) {
                        if (!in_array($item, $service->getEnabledSettingId()))
                            $validator->errors()->add('currency_supported', trans('php-core::validation.in'));
                    }
                }
            }
            if (isset($data['identifier'])) {
                $result = config('wk-core.class.site.site')::where('identifier', $data['identifier'])
                                ->when(isset($data['id']), function ($query) use ($data) {
                                    return $query->where('id', '<>', $data['id']);
                                  })
                                ->exists();
                if ($result)
                    $validator->errors()->add('identifier', trans('php-core::validation.unique', ['attribute' => trans('php-site::site.identifier')]));
            }
        });
    }
}

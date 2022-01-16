<?php

namespace WalkerChiu\Site\Models\Forms;

use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use WalkerChiu\Core\Models\Forms\FormRequest;

class LayoutFormRequest extends FormRequest
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
            'site_id'        => trans('php-site::layout.site_id'),
            'type'           => trans('php-site::layout.type'),
            'serial'         => trans('php-site::layout.serial'),
            'identifier'     => trans('php-site::layout.identifier'),
            'script_head'    => trans('php-site::layout.script_head'),
            'script_footer'  => trans('php-site::layout.script_footer'),
            'options'        => trans('php-site::layout.options'),
            'order'          => trans('php-site::layout.order'),
            'is_highlighted' => trans('php-site::layout.is_highlighted'),
            'is_enabled'     => trans('php-site::layout.is_enabled'),

            'name'           => trans('php-site::layout.name'),
            'description'    => trans('php-site::layout.description'),
            'keywords'       => trans('php-site::layout.keywords'),
            'content'        => trans('php-site::layout.content')
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
            'site_id'        => ['required','string','exists:'.config('wk-core.table.site.sites').',id'],
            'type'           => ['required', Rule::in(config('wk-core.class.site.layoutType')::getCodes())],
            'serial'         => '',
            'identifier'     => 'required|string|max:255',
            'script_head'    => '',
            'script_footer'  => '',
            'options'        => 'nullable|json',
            'order'          => 'nullable|numeric|min:0',
            'is_highlighted' => 'boolean',
            'is_enabled'     => 'boolean',

            'name'           => 'required|string|max:255',
            'description'    => '',
            'keywords'       => '',
            'content'        => 'required'
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','string','exists:'.config('wk-core.table.site.layouts').',id']]);
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
            'id.required'            => trans('php-core::validation.required'),
            'id.string'              => trans('php-core::validation.string'),
            'id.exists'              => trans('php-core::validation.exists'),
            'site_id.required'       => trans('php-core::validation.required'),
            'site_id.string'         => trans('php-core::validation.string'),
            'site_id.exists'         => trans('php-core::validation.exists'),
            'type.required'          => trans('php-core::validation.required'),
            'type.in'                => trans('php-core::validation.in'),
            'identifier.required'    => trans('php-core::validation.required'),
            'identifier.max'         => trans('php-core::validation.max'),
            'options.json'           => trans('php-core::validation.json'),
            'order.numeric'          => trans('php-core::validation.numeric'),
            'order.min'              => trans('php-core::validation.min'),
            'is_highlighted.boolean' => trans('php-core::validation.boolean'),
            'is_enabled.boolean'     => trans('php-core::validation.boolean'),

            'name.required'          => trans('php-core::validation.required'),
            'name.string'            => trans('php-core::validation.string'),
            'name.max'               => trans('php-core::validation.max'),
            'content.required'       => trans('php-core::validation.required')
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
            if (isset($data['identifier'])) {
                $result = config('wk-core.class.site.layout')::where('identifier', $data['identifier'])
                                ->when(isset($data['site_id']), function ($query) use ($data) {
                                    return $query->where('site_id', $data['site_id']);
                                  })
                                ->when(isset($data['id']), function ($query) use ($data) {
                                    return $query->where('id', '<>', $data['id']);
                                  })
                                ->exists();
                if ($result)
                    $validator->errors()->add('identifier', trans('php-core::validation.unique', ['attribute' => trans('php-site::layout.identifier')]));
            }
        });
    }
}

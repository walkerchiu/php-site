<?php

namespace WalkerChiu\Site\Models\Entities;

use WalkerChiu\Core\Models\Entities\LangTrait;
use WalkerChiu\Core\Models\Entities\UuidEntity;

class Email extends UuidEntity
{
    use LangTrait;



    /**
     * Create a new instance.
     *
     * @param Array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('wk-core.table.site.emails');

        $this->fillable = array_merge($this->fillable, [
            'site_id',
            'type',
            'serial'
        ]);

        parent::__construct($attributes);
    }

    /**
     * Get it's lang entity.
     *
     * @return Lang
     */
    public function lang()
    {
        if (
            config('wk-core.onoff.core-lang_core')
            || config('wk-site.onoff.core-lang_core')
        ) {
            return config('wk-core.class.core.langCore');
        } else {
            return config('wk-core.class.site.emailLang');
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function langs()
    {
        if (
            config('wk-core.onoff.core-lang_core')
            || config('wk-site.onoff.core-lang_core')
        ) {
            return $this->langsCore();
        } else {
            return $this->hasMany(config('wk-core.class.site.emailLang'), 'morph_id', 'id');
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site()
    {
        return $this->belongsTo(config('wk-core.class.site.site'), 'site_id', 'id');
    }
}

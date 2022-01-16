<?php

namespace WalkerChiu\Site\Models\Entities;

use WalkerChiu\Core\Models\Entities\LangTrait;
use WalkerChiu\Core\Models\Entities\UuidEntity;

class Layout extends UuidEntity
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
        $this->table = config('wk-core.table.site.layouts');

        $this->fillable = array_merge($this->fillable, [
            'site_id',
            'type',
            'serial', 'identifier',
            'script_head', 'script_footer',
            'options',
            'order',
            'is_highlighted'
        ]);

        $this->casts = array_merge($this->casts, [
            'is_highlighted' => 'boolean'
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
            return config('wk-core.class.site.layoutLang');
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
            return $this->hasMany(config('wk-core.class.site.layoutLang'), 'morph_id', 'id');
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site()
    {
        return $this->belongsTo(config('wk-core.class.site.site'), 'site_id', 'id');
    }

    /**
     * Get all of the categories for the layout.
     *
     * @param String  $type
     * @param Bool    $is_enabled
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function categories($type = null, $is_enabled = null)
    {
        $table = config('wk-core.table.morph-category.categories_morphs');
        return $this->morphToMany(config('wk-core.class.morph-category.category'), 'morph', $table)
                    ->when(is_null($type), function ($query) {
                          return $query->whereNull('type');
                      }, function ($query) use ($type) {
                          return $query->where('type', $type);
                      })
                    ->unless( is_null($is_enabled), function ($query) use ($is_enabled) {
                        return $query->where('is_enabled', $is_enabled);
                    });
    }

    /**
     * Get all of the comments for the layout.
     *
     * @param String  $user_id
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments($user_id = null)
    {
        return $this->morphMany(config('wk-core.class.morph-comment.comment'), 'morph')
                    ->when($user_id, function ($query, $user_id) {
                                return $query->where('user_id', $user_id);
                            });
    }
}

<?php

namespace WalkerChiu\Site\Models\Entities;

use WalkerChiu\Core\Models\Entities\LangTrait;
use WalkerChiu\Core\Models\Entities\UuidEntity;
use WalkerChiu\Currency\Models\Entities\CurrencyTrait;
use WalkerChiu\MorphImage\Models\Entities\ImageTrait;

class Site extends UuidEntity
{
    use LangTrait;
    use CurrencyTrait;
    use ImageTrait;



    /**
     * Create a new instance.
     *
     * @param Array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('wk-core.table.site.sites');

        $this->fillable = array_merge($this->fillable, [
            'type',
            'serial', 'vat', 'identifier',
            'language', 'language_supported',
            'timezone',
            'area_supported',
            'currency_id', 'currency_supported',
            'smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_username', 'smtp_password',
            'email_theme', 'layout_theme',
            'view_template', 'email_template', 'skin',
            'script_head', 'script_footer',
            'options', 'images',
            'can_guestOrder', 'can_guestComment',
            'is_main'
        ]);

        $this->casts = array_merge($this->casts, [
            'language_supported' => 'json',
            'area_supported'     => 'json',
            'currency_supported' => 'json',
            'options'            => 'json',
            'images'             => 'json',
            'can_guestOrder'     => 'boolean',
            'can_guestComment'   => 'boolean',
            'is_main'            => 'boolean'
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
            return config('wk-core.class.site.siteLang');
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
            return $this->hasMany(config('wk-core.class.site.siteLang'), 'morph_id', 'id');
        }
    }

    /**
     * @param String  $type
     * @param Bool    $is_enabled
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function layouts($type = null, $is_enabled = null)
    {
        return $this->hasMany(config('wk-core.class.site.layout'), 'site_id', 'id')
                    ->when($type, function ($query, $type) {
                        return $query->where('type', $type);
                    })
                    ->unless( is_null($is_enabled), function ($query) use ($is_enabled) {
                        return $query->where('is_enabled', $is_enabled);
                    });
    }

    /**
     * @param String  $type
     * @param Bool    $is_enabled
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emails($type = null, $is_enabled = null)
    {
        return $this->hasMany(config('wk-core.class.site.email'), 'site_id', 'id')
                    ->when($type, function ($query, $type) {
                        return $query->where('type', $type);
                    })
                    ->unless( is_null($is_enabled), function ($query) use ($is_enabled) {
                        return $query->where('is_enabled', $is_enabled);
                    });
    }

    /**
     * @param String  $type
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function email(string $type)
    {
        return $this->HasOne(config('wk-core.class.site.email'), 'site_id', 'id')
                    ->ofEnabled()
                    ->where('type', $type)
                    ->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function firewalls()
    {
        return $this->morphMany(config('wk-core.class.firewall.setting'), 'morph');
    }

    /**
     * @param String  $type
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function addresses($type = null)
    {
        return $this->morphMany(config('wk-core.class.morph-address.address'), 'morph')
                    ->when($type, function ($query, $type) {
                                return $query->where('type', $type);
                            });
    }

    /**
     * @param String  $type
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function boards($type = null)
    {
        return $this->morphMany(config('wk-core.class.morph-board.board'), 'host')
                    ->when($type, function ($query, $type) {
                                return $query->where('type', $type);
                            });
    }

    /**
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function coupons()
    {
        return $this->morphMany(config('wk-core.class.coupon.coupon'), 'host');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function points()
    {
        return $this->morphMany(config('wk-core.class.point.setting'), 'host');
    }

    /**
     * @param String  $type
     * @param String  $category
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function links($type = null, $category = null)
    {
        return $this->morphMany(config('wk-core.class.morph-link.link'), 'morph')
                    ->when($type, function ($query, $type) {
                                return $query->where('type', $type);
                            })
                    ->when($category, function ($query, $category) {
                                return $query->where('category', $category);
                            });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function newsletters()
    {
        return $this->morphMany(config('wk-core.class.newsletter.article'), 'morph');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function payments()
    {
        return $this->morphMany(config('wk-core.class.payment.payment'), 'morph');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function roles()
    {
        return $this->morphMany(config('wk-core.class.role.role'), 'morph');
    }

    /**
     * Get all of the categories for the site.
     *
     * @param String  $type
     * @param Bool    $is_enabled
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function categories($type = null, $is_enabled = null)
    {
        return $this->morphMany(config('wk-core.class.morph-category.category'), 'host')
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
     * Get all of the tags for the site.
     *
     * @param Bool  $is_enabled
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags($is_enabled = null)
    {
        return $this->morphMany(config('wk-core.class.morph-tag.tag'), 'host')
                    ->unless( is_null($is_enabled), function ($query) use ($is_enabled) {
                        return $query->where('is_enabled', $is_enabled);
                    });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stocks()
    {
        return $this->hasMany(config('wk-core.class.mall-shelf.stock'), 'stock_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function channels()
    {
        return $this->hasMany(config('wk-core.class.mall-cart.channel'), 'channel_id', 'id');
    }

    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfOnlyGuestOrder($query)
    {
        return $query->where('can_guestOrder', 1);
    }

    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfNotOnlyGuestOrder($query)
    {
        return $query->where('can_guestOrder', 0);
    }

    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfOnlyGuestComment($query)
    {
        return $query->where('can_guestComment', 1);
    }

    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfNotOnlyGuestComment($query)
    {
        return $query->where('can_guestComment', 0);
    }
}

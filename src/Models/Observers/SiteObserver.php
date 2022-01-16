<?php

namespace WalkerChiu\Site\Models\Observers;

use WalkerChiu\Currency\Models\Services\CurrencyService;

class SiteObserver
{
    /**
     * Handle the entity "retrieved" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function retrieved($entity)
    {
        //
    }

    /**
     * Handle the entity "creating" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function creating($entity)
    {
        //
    }

    /**
     * Handle the entity "created" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function created($entity)
    {
        //
    }

    /**
     * Handle the entity "updating" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function updating($entity)
    {
        //
    }

    /**
     * Handle the entity "updated" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function updated($entity)
    {
        //
    }

    /**
     * Handle the entity "saving" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function saving($entity)
    {
        if (!is_null($entity->language)) {
            if (!in_array($entity->language, config('wk-core.class.core.language')::getCodes()))
                return false;
        }
        if (!is_null($entity->timezone)) {
            if (!in_array($entity->timezone, config('wk-core.class.core.timeZone')::getValues()))
                return false;
        }
        if (config('wk-site.onoff.currency')) {
            $service = new CurrencyService();
            if (!is_null($entity->currency_id)) {
                if (!in_array($entity->currency_id, $service->getEnabledSettingId())) {
                    return false;
                }
            }
            if (!is_null($entity->currency_supported)) {
                foreach ($entity->currency_supported as $id) {
                    if (!in_array($id, $service->getEnabledSettingId())) {
                        return false;
                    }
                }
            }
        }
        if ($entity->is_main) {
            config('wk-core.class.site.site')
                ::withTrashed()
                ->where('id', '<>', $entity->id)
                ->update(['is_main' => 0]);
        }
        if (
            config('wk-core.class.site.site')
                ::where('id', '<>', $entity->id)
                ->where('identifier', $entity->identifier)
                ->exists()
        )
            return false;
    }

    /**
     * Handle the entity "saved" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function saved($entity)
    {
        //
    }

    /**
     * Handle the entity "deleting" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function deleting($entity)
    {
        if ($entity->is_main)
            return false;
    }

    /**
     * Handle the entity "deleted" event.
     *
     * Its Lang will be automatically removed by database.
     *
     * @param Entity  $entity
     * @return void
     */
    public function deleted($entity)
    {
        if (!config('wk-site.soft_delete')) {
            $entity->forceDelete();
        }

        if ($entity->isForceDeleting()) {
            $entity->langs()->withTrashed()
                            ->forceDelete();
            foreach ($entity->layouts as $layout) {
                if (
                    config('wk-site.onoff.morph-category')
                    && !empty(config('wk-core.class.morph-category.category'))
                ) {
                    $layout->categories()->detach();
                }
                if (
                    config('wk-site.onoff.morph-comment')
                    && !empty(config('wk-core.class.morph-comment.comment'))
                ) {
                    $layout->comments()->withTrashed()->forceDelete();
                }
                if (
                    config('wk-site.onoff.morph-image')
                    && !empty(config('wk-core.class.morph-image.image'))
                ) {
                    $layout->images()->withTrashed()->forceDelete();
                }
                if (
                    config('wk-site.onoff.morph-tag')
                    && !empty(config('wk-core.class.morph-tag.tag'))
                    && is_iterable($entity->tags())
                ) {
                    $layout->tags()->detach();
                }
            }

            if (
                config('wk-site.onoff.coupons')
                && !empty(config('wk-core.class.coupon.coupon'))
            ) {
                $entity->coupons()->withTrashed()->delete();
            }
            if (
                config('wk-site.onoff.currency')
                && !empty(config('wk-core.class.currency.currency'))
            ) {
                $entity->currencies()->withTrashed()->delete();
            }
            if (
                config('wk-site.onoff.firewall')
                && !empty(config('wk-core.class.firewall.setting'))
            ) {
                $entity->firewalls()->withTrashed()->delete();
            }
            if (
                config('wk-site.onoff.mall-stock')
                && !empty(config('wk-core.class.mall-stock.stock'))
            ) {
                $entity->stocks()->withTrashed()->delete();
            }
            if (
                config('wk-site.onoff.morph-address')
                && !empty(config('wk-core.class.morph-address.address'))
            ) {
                $entity->addresses()->withTrashed()->forceDelete();
            }
            if (
                config('wk-site.onoff.morph-board')
                && !empty(config('wk-core.class.morph-board.board'))
            ) {
                $entity->boards()->withTrashed()->forceDelete();
            }
            if (
                config('wk-site.onoff.morph-category')
                && !empty(config('wk-core.class.morph-category.category'))
            ) {
                $entity->categories()->detach();
            }
            if (
                config('wk-site.onoff.morph-comment')
                && !empty(config('wk-core.class.morph-comment.comment'))
            ) {
                $entity->comments()->withTrashed()->forceDelete();
            }
            if (
                config('wk-site.onoff.morph-image')
                && !empty(config('wk-core.class.morph-image.image'))
            ) {
                $entity->images()->withTrashed()->forceDelete();
            }
            if (
                config('wk-site.onoff.morph-registration')
                && !empty(config('wk-core.class.morph-registration.registration'))
            ) {
                $entity->registrations()->withTrashed()->forceDelete();
            }
            if (
                config('wk-site.onoff.morph-tag')
                && !empty(config('wk-core.class.morph-tag.tag'))
                && is_iterable($entity->tags())
            ) {
                $entity->tags()->detach();
            }
            if (
                config('wk-site.onoff.morph-link')
                && !empty(config('wk-core.class.morph-link.link'))
            ) {
                $entity->links()->withTrashed()->forceDelete();
            }
            if (
                config('wk-site.onoff.newsletter')
                && !empty(config('wk-core.class.newsletter.article'))
            ) {
                $entity->newsletters()->withTrashed()->forceDelete();
            }
            if (
                config('wk-site.onoff.payment')
                && !empty(config('wk-core.class.payment.payment'))
            ) {
                $entity->payments()->withTrashed()->forceDelete();
            }
            if (
                config('wk-site.onoff.point')
                && !empty(config('wk-core.class.point.setting'))
            ) {
                $entity->points()->withTrashed()->forceDelete();
            }
            if (
                config('wk-site.onoff.role')
                && !empty(config('wk-core.class.role.role'))
            ) {
                $entity->roles()->withTrashed()->forceDelete();
            }
            if (
                config('wk-site.onoff.shipment')
                && !empty(config('wk-core.class.shipment.shipment'))
            ) {
                $entity->shipments()->withTrashed()->forceDelete();
            }
        }
    }

    /**
     * Handle the entity "restoring" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function restoring($entity)
    {
        if (
            config('wk-core.class.site.site')
                ::where('id', '<>', $entity->id)
                ->where('identifier', $entity->identifier)
                ->exists()
        )
            return false;
    }

    /**
     * Handle the entity "restored" event.
     *
     * @param Entity  $entity
     * @return void
     */
    public function restored($entity)
    {
        //
    }
}

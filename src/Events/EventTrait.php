<?php

namespace WalkerChiu\Site\Events;

use WalkerChiu\Site\Models\Services\SiteService;

trait EventTrait
{
    /**
     * Get Site.
     *
     * @return void
     */
    public function getSite()
    {
        $service = new SiteService();
        $service->rememberSite();

        return $service->getSite();
    }
}

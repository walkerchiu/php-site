<?php

namespace WalkerChiu\Site\Models\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use WalkerChiu\Core\Models\Exceptions\NotFoundEntityException;
use WalkerChiu\Core\Models\Services\CheckExistTrait;
use WalkerChiu\Core\Models\Services\DomainTrait;

class SiteService
{
    use CheckExistTrait;
    use DomainTrait;

    protected $repository;



    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = App::make(config('wk-core.class.site.siteRepository'));
    }

    /**
     * Find site by doamin.
     *
     * @param String  $parameter
     * @return Site
     *
     * @throws NotFoundEntityException
     */
    public function getSite($parameter = null)
    {
        if (is_null($parameter))
            $parameter = $this->getDomain();

        $site = $this->repository->findByIdentifier($parameter);
        if (empty($site))
            throw new NotFoundEntityException($site);

        return $site;
    }

    /**
     * Remember site setting in session.
     *
     * @param String  $parameter
     * @return Bool
     *
     * @throws NotFoundEntityException
     */
    public function rememberSite($parameter = null): bool
    {
        $site = $this->getSite($parameter);

        if (empty($site))
            throw new NotFoundEntityException($site);

        Session::put('site_id', $site->id);
        Session::put('language', $site->language);
        Session::put('timezone', $site->timezone);
        Session::put('currency_id', $site->currency_id);

        $site_address = $site->addresses('site')->first();
        if ($site_address) {
            Session::put('site_address', [
                'type'          => $site_address->type,
                'phone'         => $site_address->phone,
                'email'         => $site_address->email,
                'area'          => $site_address->area,
                'address_line1' => $site_address->findLangByKey('address_line1'),
                'address_line2' => $site_address->findLangByKey('address_line2'),
                'guide'         => $site_address->findLangByKey('guide')
            ]);
        }

        return true;
    }

    /**
     * Check if the site is only for members.
     *
     * @param String  $parameter
     * @return Bool
     *
     * @throws NotFoundEntityException
     */
    public function checkIfOnlyMember($parameter = null): bool
    {
        $site = $this->getSite($parameter);

        if (empty($site))
            throw new NotFoundEntityException($site);

        return $site->is_onlyMember;
    }

    /**
     * Get Class and ID.
     *
     * @param String  $parameter
     * @return Array
     *
     * @throws NotFoundEntityException
     */
    public function getSiteIndex($parameter = null): array
    {
        $site = $this->getSite($parameter);

        if (empty($site))
            throw new NotFoundEntityException($site);

        return [
            'class' => get_class($site),
            'id'    => $site->id
        ];
    }

    /**
     * Get Supported Area.
     *
     * @param String  $parameter
     * @return Array
     *
     * @throws NotFoundEntityException
     */
    public function getSupportedArea($parameter = null): array
    {
        $site = $this->getSite($parameter);

        if (empty($site))
            throw new NotFoundEntityException($site);

        return [
            'supported' => $site->area_supported,
            'constant'  => config('wk-core.class.core.countryZone')::options()
        ];
    }

    /**
     * Get Language.
     *
     * @return String
     */
    public function getLanguage(): string
    {
        if (Session::has('language'))
            return Session::get('language');

        if ($this->rememberSite())
            return $this->getLanguage();

        return config('wk-core.language');
    }

    /**
     * Get Timezone.
     *
     * @return String
     */
    public function getTimezone(): string
    {
        if (Session::has('timezone'))
            return Session::get('timezone');

        if ($this->rememberSite())
            return $this->getTimezone();

        return config('wk-core.timezone');
    }

    /**
     * Get Currency.
     *
     * @return String
     */
    public function getCurrency(): string
    {
        if (Session::has('currency_id'))
            return Session::get('currency_id');

        if ($this->rememberSite())
            return $this->getCurrency();

        return config('wk-currency.currency_id');
    }

    /**
     * Get Site Address.
     *
     * @return Array
     */
    public function getSiteAddress(): array
    {
        if (Session::has('site_address'))
            return Session::get('site_address');

        if ($this->rememberSite())
            return $this->getSiteAddress();

        return [];
    }

    /**
     * @param Site    $site
     * @param String  $type
     * @param String  $id
     * @return Bool
     */
    public function enableSetting($site, string $type, string $id): bool
    {
        if (empty($id))
            return $site->emails($type)->update(['is_enabled' => 0]);

        return (bool) $site->emails->where('id', $id)
                                   ->each( function ($item, $key) {
                                        $item->update(['is_enabled' => 1]);
                                    });
    }

    /**
     * @param String  $vat
     * @param String  $id
     * @return Bool
     */
    public function checkExistVAT(string $vat, $id = null): bool
    {
        return $this->repository->where('vat', '=', $vat)
                                ->when($id, function ($query, $id) {
                                    return $query->where('id', '<>', $id);
                                  })
                                ->exists();
    }
}

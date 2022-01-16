<?php

namespace WalkerChiu\Site\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use WalkerChiu\Currency\Models\Services\CurrencyService;
use WalkerChiu\Site\Models\Services\EmailTemplateService;

class SiteInitializer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var String
     */
    protected $signature = 'command:SiteInitializer
        {--path-lang-category=php-site::category}
        {--path-views-email=php-site::emails}
        {--template-email=0}';

    /**
     * The console command description.
     *
     * @var String
     */
    protected $description = 'Initialize';



    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return Mixed
     */
    public function handle()
    {
        $this->call('command:SiteCleaner');

        $this->info('Initializing...');


        // Create Main Site
        $data = [
            'identifier'         => config('wk-site.initializer.site.identifier'),
            'language'           => config('wk-site.initializer.site.language'),
            'language_supported' => config('wk-site.initializer.site.language_supported'),
            'timezone'           => config('wk-site.initializer.site.timezone'),
            'area_supported'     => config('wk-site.initializer.site.area_supported'),
            'smtp_host'          => config('wk-site.initializer.site.smtp_host'),
            'smtp_port'          => config('wk-site.initializer.site.smtp_port'),
            'smtp_encryption'    => config('wk-site.initializer.site.smtp_encryption'),
            'smtp_username'      => config('wk-site.initializer.site.smtp_username'),
            'smtp_password'      => config('wk-site.initializer.site.smtp_password'),
            'is_main'            => 1
        ];

        $this->initializeCurrency();
        if (
            config('wk-site.onoff.currency')
            && !empty(config('wk-core.class.currency.currency'))
        ) {
            $service = new CurrencyService();
            $data = array_merge($data, [
                'currency_id'        => config('wk-site.initializer.site.currency_id'),
                'currency_supported' => $service->getEnabledSettingId()
            ]);
        }

        $site = App::make(config('wk-core.class.site.site'))::create($data);
        $siteLang = App::make(config('wk-core.class.site.siteLang'))::create([
            'morph_type' => get_class($site),
            'morph_id'   => $site->id,
            'code'       => config('wk-site.initializer.site.language'),
            'key'        => 'name',
            'value'      => config('wk-site.initializer.site.name'),
            'is_current' => 1
        ]);
        $this->info(config('wk-core.table.site.sites') .' have been affected.');
        $this->info(config('wk-core.table.site.sites_lang') .' have been affected.');
        $this->info(config('wk-core.table.morph-image.images') .' have been affected.');
        $this->info(config('wk-core.table.morph-image.images_lang') .' have been affected.');

        $this->initializeAddress('site', $site->id);
        $this->initializeEmails($site->id, $this->option('path-views-email'), $this->option('template-email'));
        $this->initializeCategories($site->id, $this->option('path-lang-category'));
        $this->initializeCart($site->id);
        $this->initializeAccount();

        $this->info('Done!');
    }

    /**
     * Initialize Address.
     *
     * @param String  $type
     * @param String  $id
     * @return Mixed
     */
    public function initializeAddress(string $type, string $id)
    {
        if (
            config('wk-site.onoff.morph-address')
            && !empty(config('wk-core.class.morph-address.address'))
        ) {
            if ($type == 'site') {
                $address = App::make(config('wk-core.class.morph-address.address'))::create([
                    'morph_type' => config('wk-core.class.site.site'),
                    'morph_id'   => $id,
                    'type'       => config('wk-site.initializer.site.address.type'),
                    'phone'      => config('wk-site.initializer.site.address.phone'),
                    'email'      => config('wk-site.initializer.site.address.email'),
                    'area'       => config('wk-site.initializer.site.address.area'),
                    'is_main'    => 1
                ]);
                $items = ['name', 'address_line1', 'address_line2', 'guide'];
                foreach ($items as $item) {
                    if (config('wk-site.initializer.site.address.'.$item)) {
                        $addressLang = App::make(config('wk-core.class.morph-address.addressLang'))::create([
                            'morph_type' => get_class($address),
                            'morph_id'   => $address->id,
                            'code'       => config('wk-site.initializer.site.language'),
                            'key'        => $item,
                            'value'      => config('wk-site.initializer.site.address.'.$item),
                            'is_current' => 1
                        ]);
                    }
                }
            } else {
                $morph_type = config('wk-core.class.user');
                if ($type == 'profile')
                    $morph_type = config('wk-core.class.account.profile');
                $address = App::make(config('wk-core.class.morph-address.address'))::create([
                    'morph_type' => $morph_type,
                    'morph_id'   => $id,
                    'type'       => config('wk-site.initializer.admin.address.type'),
                    'phone'      => config('wk-site.initializer.admin.address.phone'),
                    'email'      => config('wk-site.initializer.admin.address.email'),
                    'area'       => config('wk-site.initializer.admin.address.area'),
                    'is_main'    => 1
                ]);
                $items = ['name', 'address_line1', 'address_line2'];
                foreach ($items as $item) {
                    if (config('wk-site.initializer.admin.address.'.$item)) {
                        $addressLang = App::make(config('wk-core.class.morph-address.addressLang'))::create([
                            'morph_type' => get_class($address),
                            'morph_id'   => $address->id,
                            'code'       => config('wk-site.initializer.site.language'),
                            'key'        => $item,
                            'value'      => config('wk-site.initializer.admin.address.'.$item),
                            'is_current' => 1
                        ]);
                    }
                }
            }

            $this->info(config('wk-core.table.morph-address.addresses') .' have been affected.');
            $this->info(config('wk-core.table.morph-address.addresses_lang') .' have been affected.');
        } else {
            $this->line(config('wk-core.table.morph-address.addresses') .' have not been affected.');
            $this->line(config('wk-core.table.morph-address.addresses_lang') .' have not been affected.');
        }
    }

    /**
     * Initialize Emails.
     *
     * @param String  $site_id
     * @param String  $path
     * @param Mixed   $email_template
     * @return Mixed
     */
    public function initializeEmails(string $site_id, string $path, $email_template = 0)
    {
        $types  = config('wk-core.class.site.emailType')::getCodes();
        $items = ['name', 'subject', 'style', 'header', 'content', 'footer'];

        foreach ($types as $type) {
            if (config('wk-site.initializer.email.'.$type.'.onoff')) {
                $email = App::make(config('wk-core.class.site.email'))::create([
                    'site_id'    => $site_id,
                    'type'       => $type,
                    'serial'     => config('wk-site.initializer.email.'.$type.'.serial'),
                    'is_enabled' => 1
                ]);
                foreach ($items as $item) {
                    if (in_array($item, ['style', 'header', 'content', 'footer'])) {
                        $service = new EmailTemplateService($type);
                        $value = $service->loadTemplate($item, $path, $email_template);
                        if (empty($value))
                            continue;
                    } else {
                        $value = config('wk-site.initializer.email.'.$type.'.'.$item);
                    }

                    $emailLang = App::make(config('wk-core.class.site.emailLang'))::create([
                        'morph_type' => get_class($email),
                        'morph_id'   => $email->id,
                        'code'       => config('wk-site.initializer.site.language'),
                        'key'        => $item,
                        'value'      => $value,
                        'is_current' => 1
                    ]);
                }
            }
        }

        $this->info(config('wk-core.table.site.emails') .' have been affected.');
        $this->info(config('wk-core.table.site.emails_lang') .' have been affected.');
    }

    /**
     * Initialize Categories.
     *
     * @param String  $site_id
     * @param String  $path
     * @return Mixed
     */
    public function initializeCategories(string $site_id, string $path)
    {
        if (
            config('wk-site.onoff.morph-category')
            && !empty(config('wk-core.class.morph-category.category'))
        ) {
            $items = config('wk-site.initializer.categories');
            $langs = config('wk-site.initializer.site.language_supported');
            foreach ($items as $key1=>$item) {
                $category = App::make(config('wk-core.class.morph-category.category'))::create([
                    'host_type'  => config('wk-core.class.site.site'),
                    'host_id'    => $site_id,
                    'type'       => 'admin',
                    'identifier' => $key1,
                    'icon'       => $item['icon'],
                    'order'      => array_search($key1, array_keys($items)),
                    'is_enabled' => 1
                ]);
                foreach ($langs as $lang) {
                    App::setLocale($lang);
                    App::make(config('wk-core.class.morph-category.categoryLang'))::create([
                        'morph_type' => get_class($category),
                        'morph_id'   => $category->id,
                        'code'       => $lang,
                        'key'        => 'name',
                        'value'      => trans($path.'.'.$key1),
                        'is_current' => 1
                    ]);
                }

                $i = 0;
                foreach ($item['data'] as $key2 => $value2) {
                    $category2 = App::make(config('wk-core.class.morph-category.category'))::create([
                        'host_type'  => config('wk-core.class.site.site'),
                        'host_id'    => $site_id,
                        'ref_id'     => $category->id,
                        'type'       => 'admin',
                        'identifier' => $key1 .'-'. $key2,
                        'icon'       => $value2['icon'],
                        'order'      => $i++,
                        'is_enabled' => 1
                    ]);
                    foreach ($langs as $lang) {
                        App::setLocale($lang);
                        App::make(config('wk-core.class.morph-category.categoryLang'))::create([
                            'morph_type' => get_class($category2),
                            'morph_id'   => $category2->id,
                            'code'       => $lang,
                            'key'        => 'name',
                            'value'      => trans($path.'.'.$key1 .'-'. $key2),
                            'is_current' => 1
                        ]);
                    }

                    $j = 0;
                    foreach ($value2['data'] as $key3 => $value3) {
                        $category3 = App::make(config('wk-core.class.morph-category.category'))::create([
                            'host_type'  => config('wk-core.class.site.site'),
                            'host_id'    => $site_id,
                            'ref_id'     => $category2->id,
                            'type'       => 'admin',
                            'identifier' => $key1 .'-'. $key2 .'-'. $key3,
                            'icon'       => $value3['icon'],
                            'order'      => $j++,
                            'is_enabled' => 1
                        ]);
                        foreach ($langs as $lang) {
                            App::setLocale($lang);
                            App::make(config('wk-core.class.morph-category.categoryLang'))::create([
                                'morph_type' => get_class($category3),
                                'morph_id'   => $category3->id,
                                'code'       => $lang,
                                'key'        => 'name',
                                'value'      => trans($path.'.'.$key1 .'-'. $key2 .'-'. $key3),
                                'is_current' => 1
                            ]);
                        }
                    }
                }
            }
            $this->info(config('wk-core.table.morph-category.categories') .' have been affected.');
            $this->info(config('wk-core.table.morph-category.categories_lang') .' have been affected.');
        } else {
            $this->line(config('wk-core.table.morph-category.categories') .' have not been affected.');
            $this->line(config('wk-core.table.morph-category.categories_lang') .' have not been affected.');
        }
    }

    /**
     * Initialize Cart.
     *
     * @param String  $site_id
     * @return Mixed
     */
    public function initializeCart(string $site_id)
    {
        if (
            config('wk-site.onoff.mall-cart')
            && !empty(config('wk-core.class.mall-cart.channel'))
        ) {
            $items = config('wk-site.initializer.channels');
            $langs = config('wk-site.initializer.site.language_supported');
            foreach ($items as $key1=>$item) {
                $channel = App::make(config('wk-core.class.mall-cart.channel'))::create([
                    'host_type'  => config('wk-core.class.site.site'),
                    'host_id'    => $site_id,
                    'serial'     => $item['serial'],
                    'identifier' => $key1,
                    'order'      => $item['order'],
                    'is_enabled' => $item['is_enabled']
                ]);
                foreach ($langs as $lang) {
                    App::setLocale($lang);
                    App::make(config('wk-core.class.mall-cart.channelLang'))::create([
                        'morph_type' => get_class($channel),
                        'morph_id'   => $channel->id,
                        'code'       => $lang,
                        'key'        => 'name',
                        'value'      => $item['name'],
                        'is_current' => 1
                    ]);
                }
            }
            $this->info(config('wk-core.table.mall-cart.channels') .' have been affected.');
            $this->info(config('wk-core.table.mall-cart.channels_lang') .' have been affected.');
        } else {
            $this->line(config('wk-core.table.mall-cart.channels') .' have not been affected.');
            $this->line(config('wk-core.table.mall-cart.channels_lang') .' have not been affected.');
        }
    }

    /**
     * Initialize Account.
     *
     * @return Mixed
     */
    public function initializeAccount()
    {
        if (
            !empty(config('wk-site.initializer.admin.password'))
            && config('wk-site.onoff.user')
            && !empty(config('wk-core.class.user'))
            && config('wk-site.onoff.account')
            && !empty(config('wk-core.class.account.profile'))
            && config('wk-site.onoff.role')
            && !empty(config('wk-core.class.role.role'))
            && !empty(config('wk-core.class.role.permission'))
        ) {
            $user = App::make(config('wk-core.class.user'))::create([
                'name'     => config('wk-site.initializer.admin.name'),
                'email'    => config('wk-site.initializer.admin.email'),
                'password' => \Hash::make(config('wk-site.initializer.admin.password'))
            ]);
            $profile = App::make(config('wk-core.class.account.profile'))::create([
                'user_id'     => $user->id,
                'language'    => config('wk-site.initializer.site.language'),
                'timezone'    => config('wk-site.initializer.site.timezone'),
                'currency_id' => config('wk-site.initializer.site.currency_id')
            ]);
            if (method_exists($user, 'addresses'))
                $this->initializeAddress('user', $user->id);
            else
                $this->initializeAddress('profile', $profile->id);


            // Create Roles
            $items = ['Admin', 'Manager', 'Staff', 'VIP'];
            $roles = [];
            foreach ($items as $item) {
                $role = App::make(config('wk-core.class.role.role'))::create([
                    'identifier' => strtolower($item),
                    'is_enabled' => 1
                ]);
                array_push($roles, $role);
                App::make(config('wk-core.class.role.roleLang'))::create([
                    'morph_type' => get_class($role),
                    'morph_id'   => $role->id,
                    'code'       => config('wk-site.initializer.site.language'),
                    'key'        => 'name',
                    'value'      => $item,
                    'is_current' => 1
                ]);
            }
            $this->info(config('wk-core.table.user') .' have been affected.');
            $this->info(config('wk-core.table.account.profiles') .' have been affected.');
            $this->info(config('wk-core.table.role.roles') .' have been affected.');
            $this->info(config('wk-core.table.role.roles_lang') .' have been affected.');

            if (method_exists($user, 'attachRole')) {
                $user->attachRole($roles[0]);
                $this->info(config('wk-core.table.role.users_roles') .' have been affected.');
            } else {
                $this->line(config('wk-core.table.role.users_roles') .'  have not been affected.');
            }
        } else {
            $this->line(config('wk-core.table.user') .' have not been affected.');
            $this->line(config('wk-core.table.account.profiles') .' have not been affected.');
            $this->line(config('wk-core.table.role.roles') .' have not been affected.');
            $this->line(config('wk-core.table.role.roles_lang') .' have not been affected.');
            $this->line(config('wk-core.table.role.users_roles') .' have not been affected.');
        }
    }

    /**
     * Initialize Currency.
     *
     * @return Mixed
     */
    public function initializeCurrency()
    {
        if (
            config('wk-site.onoff.currency')
            && !empty(config('wk-core.class.currency.currency'))
        ) {
            $items = config('wk-currency.initializer');
            foreach ($items as $item) {
                $currency = App::make(config('wk-core.class.currency.currency'))::create([
                    'host_type'     => 'WalkerChiu\Site\Models\Entities\Site',
                    'host_id'       => 1,
                    'abbreviation'  => $item['abbreviation'],
                    'mark'          => $item['mark'],
                    'exchange_rate' => $item['exchange_rate'],
                    'is_base'       => $item['is_base'],
                    'is_enabled'    => $item['is_enabled']
                ]);
                $currencyLang = App::make(config('wk-core.class.currency.currencyLang'))::create([
                    'morph_type' => get_class($currency),
                    'morph_id'   => $currency->id,
                    'code'       => 'en_us',
                    'key'        => 'name',
                    'value'      => $item['abbreviation'],
                    'is_current' => 1
                ]);
                $currencyLang = App::make(config('wk-core.class.currency.currencyLang'))::create([
                    'morph_type' => get_class($currency),
                    'morph_id'   => $currency->id,
                    'code'       => 'zh_tw',
                    'key'        => 'name',
                    'value'      => $item['name'],
                    'is_current' => 1
                ]);
            }
            $this->info(config('wk-core.table.currency.currencies') .' have been affected.');
            $this->info(config('wk-core.table.currency.currencies_lang') .' have been affected.');
        } else {
            $this->info(config('wk-core.table.currency.currencies') .' have not been affected.');
            $this->info(config('wk-core.table.currency.currencies_lang') .' have not been affected.');
        }
    }
}

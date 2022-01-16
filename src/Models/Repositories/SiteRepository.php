<?php

namespace WalkerChiu\Site\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryTrait;
use WalkerChiu\Core\Models\Services\PackagingFactory;
use WalkerChiu\Currency\Models\Services\CurrencyService;
use WalkerChiu\MorphComment\Models\Repositories\CommentRepositoryTrait;
use WalkerChiu\MorphImage\Models\Repositories\ImageRepositoryTrait;

class SiteRepository extends Repository
{
    use FormTrait;
    use RepositoryTrait;
    use CommentRepositoryTrait;
    use ImageRepositoryTrait;

    protected $instance;



    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->instance = App::make(config('wk-core.class.site.site'));
    }

    /**
     * @return Array
     */
    public function getAllLanguageSupported(): array
    {
        $records = $this->instance->get();

        $data = [];
        foreach ($records as $record) {
            $data = array_merge($data, $record->language_supported);
        }

        return array_unique($data);
    }

    /**
     * @return Array
     */
    public function getAllCurrencySupported(): array
    {
        $records = $this->instance->get();

        $data = [];
        foreach ($records as $record) {
            $data = array_merge($data, $record->currency_supported);
        }

        return array_unique($data);
    }

    /**
     * @param String  $code
     * @param Array   $data
     * @param Bool    $is_enabled
     * @param Bool    $auto_packing
     * @return Array|Collection|Eloquent
     */
    public function list(string $code, array $data, $is_enabled = null, $auto_packing = false)
    {
        $instance = $this->instance;
        if ($is_enabled === true)      $instance = $instance->ofEnabled();
        elseif ($is_enabled === false) $instance = $instance->ofDisabled();

        $data = array_map('trim', $data);
        $repository = $instance->with(['langs' => function ($query) use ($code) {
                                    $query->ofCurrent()
                                          ->ofCode($code);
                                }])
                                ->whereHas('langs', function ($query) use ($code) {
                                    return $query->ofCurrent()
                                                 ->ofCode($code);
                                })
                                ->when(
                                    config('wk-site.onoff.morph-tag')
                                    && !empty(config('wk-core.class.morph-tag.tag'))
                                , function ($query) {
                                    return $query->with(['tags', 'tags.langs']);
                                })
                                ->when(
                                    config('wk-site.onoff.morph-address')
                                    && !empty(config('wk-core.class.morph-address.address'))
                                , function ($query) {
                                    return $query->with(['addresses', 'addresses.langs']);
                                })
                                ->when($data, function ($query, $data) {
                                    return $query->unless(empty($data['id']), function ($query) use ($data) {
                                                return $query->where('id', $data['id']);
                                            })
                                            ->unless(empty($data['type']), function ($query) use ($data) {
                                                return $query->where('type', $data['type']);
                                            })
                                            ->unless(empty($data['serial']), function ($query) use ($data) {
                                                return $query->where('serial', $data['serial']);
                                            })
                                            ->unless(empty($data['vat']), function ($query) use ($data) {
                                                return $query->where('vat', $data['vat']);
                                            })
                                            ->unless(empty($data['identifier']), function ($query) use ($data) {
                                                return $query->where('identifier', $data['identifier']);
                                            })
                                            ->unless(empty($data['language']), function ($query) use ($data) {
                                                return $query->where('language', $data['language']);
                                            })
                                            ->unless(empty($data['language_supported']), function ($query) use ($data) {
                                                return $query->whereJsonContains('language_supported', $data['language_supported']);
                                            })
                                            ->unless(empty($data['timezone']), function ($query) use ($data) {
                                                return $query->where('timezone', $data['timezone']);
                                            })
                                            ->unless(empty($data['area_supported']), function ($query) use ($data) {
                                                return $query->whereJsonContains('area_supported', $data['area_supported']);
                                            })
                                            ->unless(empty($data['currency_id']), function ($query) use ($data) {
                                                return $query->where('currency_id', $data['currency_id']);
                                            })
                                            ->unless(empty($data['currency_supported']), function ($query) use ($data) {
                                                return $query->whereJsonContains('currency_supported', $data['currency_supported']);
                                            })
                                            ->unless(empty($data['template']), function ($query) use ($data) {
                                                return $query->where('template', $data['template']);
                                            })
                                            ->unless(empty($data['skin']), function ($query) use ($data) {
                                                return $query->where('skin', $data['skin']);
                                            })
                                            ->unless(empty($data['script_head']), function ($query) use ($data) {
                                                return $query->where('script_head', 'LIKE', "%".$data['script_head']."%");
                                            })
                                            ->unless(empty($data['script_footer']), function ($query) use ($data) {
                                                return $query->where('script_footer', 'LIKE', "%".$data['script_footer']."%");
                                            })
                                            ->unless(empty($data['smtp_host']), function ($query) use ($data) {
                                                return $query->where('smtp_host', $data['smtp_host']);
                                            })
                                            ->unless(empty($data['smtp_port']), function ($query) use ($data) {
                                                return $query->where('smtp_port', $data['smtp_port']);
                                            })
                                            ->unless(empty($data['smtp_encryption']), function ($query) use ($data) {
                                                return $query->where('smtp_encryption', $data['smtp_encryption']);
                                            })
                                            ->when(isset($data['can_guestOrder']), function ($query) use ($data) {
                                                return $query->where('can_guestOrder', $data['can_guestOrder']);
                                            })
                                            ->when(isset($data['can_guestComment']), function ($query) use ($data) {
                                                return $query->where('can_guestComment', $data['can_guestComment']);
                                            })
                                            ->unless(empty($data['name']), function ($query) use ($data) {
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'name')
                                                          ->where('value', 'LIKE', "%".$data['name']."%");
                                                });
                                            })
                                            ->unless(empty($data['description']), function ($query) use ($data) {
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'description')
                                                          ->where('value', 'LIKE', "%".$data['description']."%");
                                                });
                                            })
                                            ->unless(empty($data['keywords']), function ($query) use ($data) {
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'keywords')
                                                          ->where('value', 'LIKE', "%".$data['keywords']."%");
                                                });
                                            })
                                            ->unless(empty($data['remarks']), function ($query) use ($data) {
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'remarks')
                                                          ->where('value', 'LIKE', "%".$data['remarks']."%");
                                                });
                                            })
                                            ->unless(empty($data['categories']), function ($query) use ($data) {
                                                return $query->whereHas('categories', function ($query) use ($data) {
                                                    $query->ofEnabled()
                                                          ->whereIn('id', $data['categories']);
                                                });
                                            })
                                            ->unless(empty($data['tags']), function ($query) use ($data) {
                                                return $query->whereHas('tags', function ($query) use ($data) {
                                                    $query->ofEnabled()
                                                          ->whereIn('id', $data['tags']);
                                                });
                                            });
                                })
                                ->orderBy('updated_at', 'DESC');

        if ($auto_packing) {
            $factory = new PackagingFactory(config('wk-site.output_format'), config('wk-site.pagination.pageName'), config('wk-site.pagination.perPage'));
            $factory->setFieldsLang(['name', 'description', 'keywords', 'remarks']);

            if (in_array(config('wk-site.output_format'), ['array', 'array_pagination'])) {
                switch (config('wk-site.output_format')) {
                    case "array":
                        $entities = $this->toCollection($repository);
                        // no break
                    case "array_pagination":
                        $entities = $this->toCollectionWithPagination($repository);
                        // no break
                    default:
                        $output = [];
                        foreach ($entities as $instance) {
                            $data = $instance->toArray();
                            array_push($output,
                                array_merge($data, [
                                    'icons' => $this->getlistOfIcons($code),
                                    'logos' => $this->getlistOfLogos($code)
                                ])
                            );
                        }
                }
                return $output;
            } else {
                return $factory->output($repository);
            }
        }

        return $repository;
    }

    /**
     * @param String  $code
     * @return Site
     */
    public function findMain(string $code)
    {
        return $this->instance->with(['langs' => function ($query) {
                                        $query->ofCurrent();
                                    }, 'layouts'])
                                ->when(
                                    config('wk-site.onoff.morph-tag')
                                    && !empty(config('wk-core.class.morph-tag.tag'))
                                , function ($query) {
                                    return $query->with(['tags', 'tags.langs']);
                                })
                                ->when(
                                    config('wk-site.onoff.morph-address')
                                    && !empty(config('wk-core.class.morph-address.address'))
                                , function ($query) {
                                    return $query->with(['addresses', 'addresses.langs']);
                                })
                                ->ofMain()
                                ->first();
    }

    /**
     * @param String  $code
     * @return Array
     */
    public function showByMain(string $code): array
    {
        $site = $this->findMain($code);

        return $this->show($site, $code);
    }

    /**
     * @param Site          $instance
     * @param Array|String  $code
     * @return Array
     */
    public function show($instance, $code): array
    {
        $service = new CurrencyService();
        $currencies = $service->getEnabledSetting(config('wk-core.class.site.site'), $instance->id, $code);

        $data = [
            'id' => $instance->id,
            'constant'     => [
                'language' => config('wk-core.class.core.language')::options(),
                'area'     => config('wk-core.class.core.countryZone')::options(),
                'timezone' => config('wk-core.class.core.timeZone')::all(),
                'currency' => $currencies
            ],
            'basic'        => [],
            'addresses'    => [],
            'smtp'         => [],
            'emailSetting' => $this->getEmailSetting($instance, $code),
            'emailShared'  => [
                'theme' => [],
                'email' => []
            ],
            'icons'        => [],
            'logos'        => [],
            'layoutShared' => [
                'theme' => []
            ],
            'comments'     => []
        ];

        if (empty($instance))
            return $data;

        foreach ($instance->language_supported as $item) {
            $this->setEntity($instance);
            $data['basic'][$item] = [
                'type'               => $instance->type,
                'serial'             => $instance->serial,
                'vat'                => $instance->vat,
                'identifier'         => $instance->identifier,
                'language'           => $instance->language,
                'language_supported' => $instance->language_supported,
                'timezone'           => $instance->timezone,
                'area_supported'     => $instance->area_supported,
                'currency_id'        => $instance->currency_id,
                'currency_supported' => $instance->currency_supported,
                'view_template'      => $instance->view_template,
                'email_template'     => $instance->email_template,
                'skin'               => $instance->skin,
                'script_head'        => $instance->script_head,
                'script_footer'      => $instance->script_footer,
                'can_guestOrder'     => $instance->can_guestOrder,
                'can_guestComment'   => $instance->can_guestComment,
                'is_main'            => $instance->is_main,
                'is_enabled'         => $instance->is_enabled,
                'options'            => $instance->options,
                'images'             => $instance->images,
                'name'               => $instance->findLang($item, 'name'),
                'description'        => $instance->findLang($item, 'description'),
                'keywords'           => $instance->findLang($item, 'keywords'),
                'remarks'            => $instance->findLang($item, 'remarks')
            ];

            $address = $instance->addresses('site')->first();
            if (!empty($address)) {
                $data['addresses'][$item] = [
                    'id'            => $address->id,
                    'name'          => $address->findLang($item, 'name'),
                    'phone'         => $address->phone,
                    'email'         => $address->email,
                    'area'          => $address->area,
                    'address_line1' => $address->findLang($item, 'address_line1'),
                    'address_line2' => $address->findLang($item, 'address_line2'),
                    'guide'         => $address->findLang($item, 'guide')
                ];
            }
            $data['emailShared']['theme'][$item] = [
                'email_theme' => $instance->email_theme,
                'email_style' => $instance->findLang($item, 'email_style')
            ];
            $data['emailShared']['email'][$item] = [
                'email_header' => $instance->findLang($item, 'email_header'),
                'email_footer' => $instance->findLang($item, 'email_footer')
            ];
        }
        $data['icons'] = $this->getlistOfIcons($instance->language_supported);
        $data['logos'] = $this->getlistOfLogos($instance->language_supported);
        $data['smtp'] = [
            'host'                 => $instance->smtp_host,
            'port'                 => $instance->smtp_port,
            'encryption'           => $instance->smtp_encryption,
            'encryption_supported' => config('wk-site.smtp.encryption_supported'),
            'username'             => $instance->smtp_username,
            'password'             => null
        ];

        if (config('wk-site.onoff.morph-comment'))
            $data['comments'] = $this->getlistOfComments($instance);

        return $data;
    }

    /**
     * @param Site    $instance
     * @param String  $language
     * @return Array
     */
    public function getEmailSetting($instance, string $language): array
    {
        $data = [];
        foreach (config('wk-site.initializer.email') as $key => $type) {
            if ($type['onoff']) {
                $data = array_merge($data, [$key => [
                    'now'       => $instance->email($key) ? $instance->email($key)->id : '',
                    'supported' => $this->getSupportEmails($instance, $key, $language)
                ]]);
            }
        }

        return $data;
    }

    /**
     * @param Site    $instance
     * @param String  $type
     * @param String  $language
     * @return Array
     */
    public function getSupportEmails($instance, string $type, string $language): array
    {
        $records = $instance->emails($type)->get();

        $list = [];
        foreach ($records as $record) {
            array_push($list, [
                'id'          => $record->id,
                'serial'      => $record->serial,
                'name'        => $record->findLang($language, 'name') ?? $record->findLang('en_us', 'name'),
                'description' => $record->findLang($language, 'description'),
            ]);
        }

        return $list;
    }

    /**
     * @param Site    $instance
     * @param String  $language
     * @param Bool    $is_frontend
     * @return Array
     */
    public function getLanguageMenu($instance, string $language, $is_frontend = false): array
    {
        $languages = config('wk-core.class.core.language')::options();
        $items = [
            'menu' => [
                'language' => $languages[$language],
                'language_supported' => []
            ]
        ];

        foreach ($instance->language_supported as $item) {
            $items['menu']['language_supported'][$item] = $languages[$item];
        }
        unset($items['menu']['language_supported'][$language]);

        if ($is_frontend)
            return $items;


        $items['switcher'] = [
            'language' => $language,
            'language_supported' => []
        ];
        $allLanguageSupported = $this->getAllLanguageSupported();
        foreach ($allLanguageSupported as $item) {
            $items['switcher']['language_supported'][$item] = $languages[$item];
        }

        return $items;
    }

    /**
     * @param Site    $instance
     * @param String  $language
     * @return Array
     */
    public function getCurrencyMenu($instance, string $language): array
    {
        $service = new CurrencyService();
        $items = [
            'currency_id'        => $instance->currency_id,
            'currency_supported' => $service->getEnabledSetting(config('wk-core.class.site.site'),
                                                                $instance->id,
                                                                $language)
        ];

        return $items;
    }

    /**
     * @param Site    $instance
     * @param String  $language
     * @param Bool    $is_frontend
     * @return Array
     */
    public function getPartOfLayout($instance, string $language, $is_frontend = false): array
    {
        $data = [];
        $items = $instance->layouts('block', true)->get();
        foreach ($items as $item) {
            $layout = [
                'id'      => $item->id,
                'order'   => $item->order,
                'content' => $item->findLang($language, 'content')
            ];
            if (isset($data[$item->identifier])) {
                array_push($data[$item->identifier], $layout);
            } else {
                $data[$item->identifier] = [$layout];
            }
        }

        return $data;
    }
}

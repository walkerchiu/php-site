<?php

namespace WalkerChiu\Site\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryTrait;
use WalkerChiu\Core\Models\Services\PackagingFactory;
use WalkerChiu\MorphComment\Models\Repositories\CommentRepositoryTrait;

class LayoutRepository extends Repository
{
    use FormTrait;
    use RepositoryTrait;
    use CommentRepositoryTrait;

    protected $instance;



    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->instance = App::make(config('wk-core.class.site.layout'));
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
                                ->when($data, function ($query, $data) {
                                    return $query->unless(empty($data['id']), function ($query) use ($data) {
                                                return $query->where('id', $data['id']);
                                            })
                                            ->unless(empty($data['site_id']), function ($query) use ($data) {
                                                return $query->where('site_id', $data['site_id']);
                                            })
                                            ->unless(empty($data['serial']), function ($query) use ($data) {
                                                return $query->where('serial', $data['serial']);
                                            })
                                            ->unless(empty($data['identifier']), function ($query) use ($data) {
                                                return $query->where('identifier', $data['identifier']);
                                            })
                                            ->unless(empty($data['script_head']), function ($query) use ($data) {
                                                return $query->where('script_head', 'LIKE', "%".$data['script_head']."%");
                                            })
                                            ->unless(empty($data['script_footer']), function ($query) use ($data) {
                                                return $query->where('script_footer', 'LIKE', "%".$data['script_footer']."%");
                                            })
                                            ->unless(empty($data['order']), function ($query) use ($data) {
                                                return $query->where('order', $data['order']);
                                            })
                                            ->when(isset($data['is_highlighted']), function ($query) use ($data) {
                                                return $query->where('is_highlighted', $data['is_highlighted']);
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
            $factory->setFieldsLang(['name', 'description', 'keywords', 'content']);

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
                                    'site_name' => $record->site->findLang($code, 'name')
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
     * @param Layout        $instance
     * @param Array|String  $code
     * @return Array
     */
    public function show($instance, $code): array
    {
        $data = [
            'id' => $instance ? $instance->id : '',
            'constant' => [
                'layoutType' => config('wk-core.class.site.layoutType')::options(true)
            ],
            'basic'    => [],
            'options'  => $instance ? $instance->options : null,
            'comments' => []
        ];

        if (empty($instance))
            return $data;

        $this->setEntity($instance);

        if (is_string($code)) {
            $data['basic'] = [
                'site_id'        => $instance->site_id,
                'type'           => $instance->type,
                'type_text'      => trans('php-site::layout.layoutType.'. $instance->type),
                'serial'         => $instance->serial,
                'identifier'     => $instance->identifier,
                'script_head'    => $instance->script_head,
                'script_footer'  => $instance->script_footer,
                'order'          => $instance->order,
                'is_highlighted' => $instance->is_highlighted,
                'is_enabled'     => $instance->is_enabled,
                'name'           => $instance->findLang($code, 'name'),
                'description'    => $instance->findLang($code, 'description'),
                'keywords'       => $instance->findLang($code, 'keywords'),
                'content'        => $instance->findLang($code, 'content')
            ];
        } elseif (is_array($code)) {
            foreach ($code as $language) {
                $data['basic'][$language] = [
                    'site_id'        => $instance->site_id,
                    'type'           => $instance->type,
                    'type_text'      => trans('php-site::layout.layoutType.'. $instance->type),
                    'serial'         => $instance->serial,
                    'identifier'     => $instance->identifier,
                    'script_head'    => $instance->script_head,
                    'script_footer'  => $instance->script_footer,
                    'order'          => $instance->order,
                    'is_highlighted' => $instance->is_highlighted,
                    'is_enabled'     => $instance->is_enabled,
                    'name'           => $instance->findLang($language, 'name'),
                    'description'    => $instance->findLang($language, 'description'),
                    'keywords'       => $instance->findLang($language, 'keywords'),
                    'content'        => $instance->findLang($language, 'content')
                ];
            }
        }

        if (config('wk-site.onoff.morph-comment'))
            $data['comments'] = $this->getlistOfComments($instance);

        return $data;
    }
}

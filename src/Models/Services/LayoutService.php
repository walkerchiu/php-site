<?php

namespace WalkerChiu\Site\Models\Services;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Services\CheckExistTrait;

class LayoutService
{
    use CheckExistTrait;

    protected $repository;



    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = App::make(config('wk-core.class.site.layoutRepository'));
    }

    /**
     * Insert default layout
     *
     * @param String  $code
     * @param String  $site_id
     * @param String  $type
     * @param String  $identifier
     * @param String  $content
     * @return Layout
     */
    public function insertDefaultLayout(string $code, string $site_id, string $type, string $identifier, string $content)
    {
        $layout = $this->repository->save([
            'site_id'    => $site_id,
            'type'       => $type,
            'identifier' => $identifier,
            'is_enabled' => 1
        ]);
        $this->repository->createLangWithoutCheck([
            'morph_type' => get_class($layout),
            'morph_id'   => $layout->id,
            'code'       => $code,
            'key'        => 'name',
            'value'      => $identifier
        ]);
        $this->repository->createLangWithoutCheck([
            'morph_type' => get_class($layout),
            'morph_id'   => $layout->id,
            'code'       => $code,
            'key'        => 'content',
            'value'      => $content
        ]);

        return $layout;
    }
}

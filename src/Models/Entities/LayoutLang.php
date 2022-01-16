<?php

namespace WalkerChiu\Site\Models\Entities;

use WalkerChiu\Core\Models\Entities\Lang;

class LayoutLang extends Lang
{
    /**
     * Create a new instance.
     *
     * @param Array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('wk-core.table.site.layouts_lang');

        parent::__construct($attributes);
    }
}

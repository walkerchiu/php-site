<?php

namespace WalkerChiu\Site\Models\Constants;

/**
 * @license MIT
 * @package WalkerChiu\Site
 *
 *
 */

class LayoutType
{
    /**
     * @return Array
     */
    public static function getCodes(): array
    {
        $items = [];
        $types = self::all();
        foreach ($types as $code => $type) {
            array_push($items, $code);
        }

        return $items;
    }

    /**
     * @param Bool  $onlyVaild
     * @return Array
     */
    public static function options($onlyVaild = false): array
    {
        $items = $onlyVaild ? [] : ['' => trans('php-core::system.null')];
        $types = self::all();
        foreach ($types as $key => $value) {
            $lang = trans('php-site::layout.layoutType.'.$key);
            $items = array_merge($items, [$key => $lang]);
        }

        return $items;
    }

    /**
     * @return Array
     */
    public static function all(): array
    {
        return [
            'block'   => 'Part of page',
            'content' => 'Page'
        ];
    }
}

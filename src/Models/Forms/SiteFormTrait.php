<?php

namespace WalkerChiu\MallShelf\Models\Forms;

trait SiteFormTrait
{
    /*
    |--------------------------------------------------------------------------
    | Check Exist on VAT
    |--------------------------------------------------------------------------
    */

    /**
     * @param String  $host_type
     * @param String  $host_id
     * @param String  $id
     * @param Mixed   $value
     * @return Bool
     */
    public function checkExistVAT($host_type, $host_id, $id, $value): bool
    {
        return $this->baseQueryForForm($host_type, $host_id, $id)
                    ->where('vat', $value)
                    ->exists();
    }

    /**
     * @param String  $host_type
     * @param String  $host_id
     * @param String  $id
     * @param Mixed   $value
     * @return Bool
     */
    public function checkExistVATOfEnabled($host_type, $host_id, $id, $value): bool
    {
        return $this->baseQueryForForm($host_type, $host_id, $id)
                    ->where('vat', $value)
                    ->ofEnabled()
                    ->exists();
    }
}

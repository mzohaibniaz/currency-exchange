<?php
namespace SoftEase\BtcHelper;

use Illuminate\Support\Facades\Facade;

class CurrencyFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */

    protected static function getFacadeAccessor()
    {
        return 'currency';
    }
}
<?php
namespace Kfirba\Directo\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Directo extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'directo';
    }
}
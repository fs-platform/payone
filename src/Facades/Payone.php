<?php

namespace Smbear\Payone\Facades;

use Illuminate\Support\Facades\Facade;

class Payone extends Facade
{
    protected static function getFacadeAccessor() : string
    {
        return 'payone';
    }
}
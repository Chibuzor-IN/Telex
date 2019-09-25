<?php

namespace Telex\Facades;

use Illuminate\Support\Facades\Facade;

class TelexService extends Facade{

    protected static function getFacadeAccessor() { return 'Telex'; }

}
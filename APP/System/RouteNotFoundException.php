<?php declare(strict_types=1);

namespace System;

use Exception;

class RouteNotFoundException extends Exception
{
    protected $message = 'No route found for the requested URI.';
}
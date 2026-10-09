<?php
declare(strict_types=1);

require_once APPPATH . 'Libraries/Env.php';

// Automatically load the environment variables on helper load
if (defined('APPPATH')) {
    \App\Libraries\Env::load(APPPATH . '.env');
}

if (!function_exists('env')) {
    /**
     * Retrieve environmental variables with optional fallback.
     *
     * @param string $key Environmental key.
     * @param mixed $default Fallback value.
     * @return mixed
     */
    function env(string $key, mixed $default = null): mixed
    {
        return \App\Libraries\Env::get($key, $default);
    }
}

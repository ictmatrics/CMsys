<?php declare(strict_types=1);

namespace Config;

use Exception;

/**
 * Class Autoload
 *
 * A PSR-4 compliant autoloader with additional support
 * for loading helpers, libraries, and filters.
 */
class Autoload
{
    /**
     * An array of registered namespace prefixes and their base directories.
     * @var array<string, array<string>>
     */
    protected array $prefixes = [];

    /**
     * Paths for helpers, libraries, and filters.
     */
    protected string $helperPath;
    protected string $libraryPath;
    protected string $filterPath;

    /**
     * Constructor.
     *
     * @param array $helpers   List of helpers to load.
     * @param array $libraries List of libraries to load.
     * @param array $filters   List of filters to load.
     */
    public function __construct(
        array $helpers = [],
        array $libraries = [],
        array $filters = []
    ) {
        // Define default namespace mappings
        $this->prefixes = [
            'App\\'     => [APPPATH],
            'System\\'  => [SYSTEMPATH],
            'Modules\\' => [APPPATH . 'Modules' . DIRECTORY_SEPARATOR],
            'Config\\'  => [APPPATH . 'Config' . DIRECTORY_SEPARATOR],
            'Vendors\\'  => [APPPATH . 'Vendors' . DIRECTORY_SEPARATOR],
        ];

        // Define default paths (can be customized if needed)
        $this->helperPath  = APPPATH . 'Helpers' . DIRECTORY_SEPARATOR;
        $this->libraryPath = APPPATH . 'Libraries' . DIRECTORY_SEPARATOR;
        $this->filterPath  = APPPATH . 'Filters' . DIRECTORY_SEPARATOR;

        // Load extra components
        $this->loadHelpers($helpers);
        $this->loadLibraries($libraries);
        $this->loadFilters($filters);

        // Register autoloader
        //spl_autoload_register([$this, 'loadClass']);
    }
  public function register(): void
    {
        spl_autoload_register([$this, 'loadClass']);
    }
    /**
     * Adds a new namespace prefix and base directory to the autoloader.
     */
    public function addNamespace(string $prefix, string $base_dir, bool $prepend = false): void
    {
        $prefix   = trim($prefix, '\\') . '\\';
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $this->prefixes[$prefix] ??= [];

        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $base_dir);
        } else {
            $this->prefixes[$prefix][] = $base_dir;
        }
    }

    /**
     * Core autoloader logic.
     */
    public function loadClass(string $class): void
    {
        foreach ($this->prefixes as $prefix => $base_dirs) {
            if (str_starts_with($class, $prefix)) {
                foreach ($base_dirs as $base_dir) {
                    if ($this->loadFile($base_dir, $class, $prefix)) {
                        return;
                    }
                }
            }
        }
    }

    /**
     * Load helpers.
     */
    private function loadHelpers(array $helpers): void
    {
        foreach ($helpers as $helper) {
            if (file_exists($this->helperPath . $helper . '.php')){
            $this->requireFile($this->helperPath . $helper . '.php');}
        }
    }

    /**
     * Load libraries.
     */
    private function loadLibraries(array $libraries): void
    {
        foreach ($libraries as $library) {
            if (file_exists($this->libraryPath . $library . '.php')){
            $this->requireFile($this->libraryPath . $library . '.php');}
        }
    }

    /**
     * Load filters.
     */
    private function loadFilters(array $filters): void
    {
        foreach ($filters as $filter) {
            if(file_exists($this->filterPath . $filter . '.php')){
            $this->requireFile($this->filterPath . $filter . '.php');}
        }
    }

    /**
     * Require a file safely.
     */
    private function requireFile(string $file): void
    {
        if (is_file($file)) {
            require_once $file;
        } else {
            throw new Exception("Required file not found: {$file}");
        }
    }

    /**
     * Find and include a class file.
     */
    protected function loadFile(string $base_dir, string $class, string $prefix): bool
    {
        $relative_class = substr($class, strlen($prefix));
        $file = $base_dir . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';

        if (is_file($file)) {
            require_once $file;
            return true;
        }

        return false;
    }
}

/**
 * ---------------------------------------------------------------
 * Auto-instantiate Autoload with global helpers/libraries/filters
 * ---------------------------------------------------------------
 */
$helpers   = ['format_helper', 'flash_helper', 'url_helper', 'hook_helper', 'db_helper', 'csrf_helper'];
$libraries = [''];
$filters   = [''];

new Autoload($helpers, $libraries, $filters);

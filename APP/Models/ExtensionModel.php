<?php
declare(strict_types=1);

namespace App\Models;

use System\Config\Model;
use System\Config\QueryBuilder;
use ZipArchive;
use App\Models\OptionModel;

class ExtensionModel extends Model
{
    private string $modulesTable = 'modules';
    private string $themesTable = 'themes';

    public function __construct()
    {
        parent::__construct();
    }

    public function installExtension(string $zipPath, string $type): array
    {
        if (!class_exists('ZipArchive')) {
            return ['status' => 'error', 'message' => 'ZipArchive class not found in PHP.'];
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            return ['status' => 'error', 'message' => 'Failed to open ZIP file.'];
        }

        // 1. Locate manifest.json
        $manifestIndex = -1;
        $manifestPath = '';
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (basename($name) === 'manifest.json') {
                $manifestIndex = $i;
                $manifestPath = $name;
                break;
            }
        }

        if ($manifestIndex === -1) {
            $zip->close();
            return ['status' => 'error', 'message' => 'manifest.json not found in the ZIP archive.'];
        }

        // 2. Read and Validate manifest
        $manifestContent = $zip->getFromIndex($manifestIndex);
        $manifest = json_decode($manifestContent, true);
        if (!$manifest || empty($manifest['name']) || empty($manifest['version'])) {
            $zip->close();
            return ['status' => 'error', 'message' => 'Invalid manifest.json content.'];
        }

        $name = validate_data($manifest['name']);
        $version = validate_data($manifest['version']);
        $author = validate_data($manifest['author'] ?? 'Unknown');
        $sqlScript = $manifest['sql_script'] ?? ''; // schema.sql name

        // Enforce strict name format to prevent directory traversal
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $name)) {
            $zip->close();
            return ['status' => 'error', 'message' => 'Extension name contains invalid characters. Use alphanumeric, hyphens, and underscores only.'];
        }

        // Core system templates in APP/Views/Themes are strictly protected
        if ($type === 'theme' && in_array(strtolower($name), ['classic', 'admin'], true)) {
            $zip->close();
            return ['status' => 'error', 'message' => 'The ' . $name . ' theme is a core system template and cannot be overwritten.'];
        }

        // Target directories: /app/vendors/themes/{theme_name} and /app/vendors/modules/{module_name}
        $vendorsDir = is_dir(APPPATH . 'vendors') ? APPPATH . 'vendors/' : APPPATH . 'Vendors/';
        if ($type === 'theme') {
            $destDir = $vendorsDir . 'themes/' . $name . '/';
        } else {
            $destDir = $vendorsDir . 'modules/' . $name . '/';
        }

        // Ensure parent directory exists
        if (!is_dir(dirname($destDir))) {
            @mkdir(dirname($destDir), 0755, true);
        }

        // Create destination directory
        if (!is_dir($destDir)) {
            @mkdir($destDir, 0755, true);
        }

        // Determine if Zip has a nested subdirectory
        $prefix = '';
        $parts = explode('/', $manifestPath);
        if (count($parts) > 1) {
            $prefix = $parts[0] . '/';
        }

        // Extract files with strict directory traversal prevention
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            // Skip directory entries themselves
            if (str_ends_with($filename, '/')) {
                continue;
            }

            // Strip the prefix if files are inside a nested subdirectory
            $relativePath = $filename;
            if ($prefix !== '' && str_starts_with($filename, $prefix)) {
                $relativePath = substr($filename, strlen($prefix));
            }

            $normalizedRelative = str_replace('\\', '/', $relativePath);
            if (str_contains($normalizedRelative, '../') || str_starts_with($normalizedRelative, '/')) {
                $zip->close();
                return ['status' => 'error', 'message' => 'Archive contains invalid or unsafe file paths.'];
            }

            $targetFile = $destDir . $normalizedRelative;
            if (!is_dir(dirname($targetFile))) {
                @mkdir(dirname($targetFile), 0755, true);
            }

            copy("zip://{$zipPath}#{$filename}", $targetFile);
        }
        $zip->close();

        // 3. Execute database scripts if provided
        if (!empty($sqlScript)) {
            $sqlFile = $destDir . $sqlScript;
            if (file_exists($sqlFile)) {
                $this->runSqlFile($sqlFile);
            }
        }

        // 4. Update Database Registry
        $db = new QueryBuilder();
        if ($type === 'theme') {
            $existing = $this->find_single($this->themesTable, null, '', [['name', '=', $name]]);
            if ($existing) {
                $this->update($this->themesTable, ['version' => $version], (int)$existing->id);
            } else {
                $this->insert($this->themesTable, [
                    'name' => $name,
                    'version' => $version,
                    'is_active' => 0,
                    'scope' => $manifest['scope'] ?? 'frontend'
                ]);
            }
        } else {
            $existing = $this->find_single($this->modulesTable, null, '', [['name', '=', $name]]);
            if ($existing) {
                $this->update($this->modulesTable, ['version' => $version], (int)$existing->id);
            } else {
                $this->insert($this->modulesTable, [
                    'name' => $name,
                    'version' => $version,
                    'is_active' => 0
                ]);
            }
        }

        return ['status' => 'success', 'message' => ucwords($type) . ' installed successfully into target vendor directory!'];
    }

    public function activateTheme(string $name, string $scope): bool
    {
        $optionModel = new OptionModel();
        
        // Atomic activation constraint
        if ($scope === 'frontend') {
            $optionModel->updateOption('frontend_theme', $name);
        } else {
            $optionModel->updateOption('backend_theme', $name);
        }

        // Deactivate other themes in registry of the same scope
        $this->db->query("UPDATE {$this->themesTable} SET is_active = 0 WHERE scope = ?", [$scope]);
        $this->db->query("UPDATE {$this->themesTable} SET is_active = 1 WHERE name = ? AND scope = ?", [$name, $scope]);

        return true;
    }

    public function toggleModule(string $name): bool
    {
        $module = $this->find_single($this->modulesTable, null, '', [['name', '=', $name]]);
        if ($module) {
            $newStatus = $module->is_active === 1 ? 0 : 1;
            return $this->update($this->modulesTable, ['is_active' => $newStatus], (int)$module->id);
        }
        return false;
    }

    public function deleteExtension(string $name, string $type): bool
    {
        $name = trim($name);
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $name)) {
            return false;
        }

        // Core system templates in APP/Views/Themes are immutable and strictly protected
        if ($type === 'theme' && in_array(strtolower($name), ['classic', 'admin'], true)) {
            return false;
        }

        $vendorsDir = is_dir(APPPATH . 'vendors') ? APPPATH . 'vendors/' : APPPATH . 'Vendors/';
        if ($type === 'theme') {
            $destDir = $vendorsDir . 'themes/' . $name . '/';
            // Allow cleaning up non-core legacy themes if existing in Views/Themes
            if (!is_dir($destDir) && is_dir(APPPATH . 'Views/Themes/' . $name . '/')) {
                $destDir = APPPATH . 'Views/Themes/' . $name . '/';
            }
            $registryTable = $this->themesTable;
        } else {
            $destDir = $vendorsDir . 'modules/' . $name . '/';
            if (!is_dir($destDir) && is_dir(APPPATH . 'Modules/' . $name . '/')) {
                $destDir = APPPATH . 'Modules/' . $name . '/';
            }
            $registryTable = $this->modulesTable;
        }

        // 1. Cleanup module-specific DB tables if uninstall.sql exists
        $uninstallSql = $destDir . 'uninstall.sql';
        if (file_exists($uninstallSql)) {
            $this->runSqlFile($uninstallSql);
        }

        // 2. Recursively delete files & directories
        $this->recursiveRmdir($destDir);

        // 3. Remove registry record
        $existing = $this->find_single($registryTable, null, '', [['name', '=', $name]]);
        if ($existing) {
            return $this->delete($registryTable, (int)$existing->id);
        }

        return true;
    }

    public function bootActiveModules(): void
    {
        static $booted = false;
        if ($booted) {
            return;
        }
        $booted = true;

        try {
            $activeModules = $this->find_all($this->modulesTable, '', [['is_active', '=', 1]]);
            if ($activeModules) {
                $vendorsDir = is_dir(APPPATH . 'vendors') ? APPPATH . 'vendors/' : APPPATH . 'Vendors/';
                foreach ($activeModules as $module) {
                    $moduleName = is_array($module) ? ($module['name'] ?? '') : ($module->name ?? '');
                    if (!empty($moduleName)) {
                        $initFile = $vendorsDir . 'modules/' . $moduleName . '/init.php';
                        if (file_exists($initFile)) {
                            require_once $initFile;
                        } elseif (file_exists(APPPATH . 'Modules/' . $moduleName . '/init.php')) {
                            require_once APPPATH . 'Modules/' . $moduleName . '/init.php';
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore during installation or if table missing
        }
    }

    private function runSqlFile(string $filePath): void
    {
        $sql = file_get_contents($filePath);
        $queries = array_filter(array_map('trim', explode(';', $sql)));
        $db = new QueryBuilder();
        foreach ($queries as $query) {
            if (!empty($query)) {
                $db->query($query);
            }
        }
    }

    private function recursiveRmdir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->recursiveRmdir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
}

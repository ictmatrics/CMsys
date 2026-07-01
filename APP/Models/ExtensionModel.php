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

        // Destination directories
        if ($type === 'theme') {
            $destDir = APPPATH . 'Views/Themes/' . $name . '/';
        } else {
            $destDir = APPPATH . 'Modules/' . $name . '/';
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

        // Extract files
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

            $targetFile = $destDir . $relativePath;
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

        return ['status' => 'success', 'message' => ucwords($type) . ' installed successfully!'];
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
        if ($type === 'theme') {
            $destDir = APPPATH . 'Views/Themes/' . $name . '/';
            $registryTable = $this->themesTable;
        } else {
            $destDir = APPPATH . 'Modules/' . $name . '/';
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

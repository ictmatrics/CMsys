<?php
declare(strict_types=1);

namespace App\Models;

use System\Config\Model;

class OptionModel extends Model
{
    private string $table = 'site_options';

    public function __construct()
    {
        parent::__construct();
    }

    public function getOption(string $name, mixed $default = ''): mixed
    {
        $row = $this->find_single($this->table, null, '', [['option_name', '=', $name]]);
        return $row ? $row->option_value : $default;
    }

    public function updateOption(string $name, mixed $value): bool
    {
        $existing = $this->find_single($this->table, null, '', [['option_name', '=', $name]]);
        
        if ($existing) {
            return $this->update($this->table, ['option_value' => (string)$value], (int)$existing->id);
        } else {
            return $this->insert($this->table, [
                'option_name' => $name,
                'option_value' => (string)$value
            ]) !== false;
        }
    }

    public function deleteOption(string $name): bool
    {
        $existing = $this->find_single($this->table, null, '', [['option_name', '=', $name]]);
        if ($existing) {
            return $this->delete($this->table, (int)$existing->id);
        }
        return false;
    }
}

<?php
/**
 * Setting Model
 */

namespace App\Models;

use App\Core\Model;

class Setting extends Model
{
    protected string $table = 'settings';
    
    protected array $fillable = ['key', 'value', 'type', 'group'];
    
    private static array $cache = [];
    
    /**
     * Clear settings cache
     */
    public function clearCache(): void
    {
        self::$cache = [];
    }
    
    /**
     * Get setting value
     */
    public function get(string $key, $default = null)
    {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }
        
        $setting = $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE `key` = ? LIMIT 1",
            [$key]
        );
        
        if (!$setting) {
            return $default;
        }
        
        $value = $this->castValue($setting['value'], $setting['type']);
        self::$cache[$key] = $value;
        
        return $value;
    }
    
    /**
     * Set setting value
     */
    public function set(string $key, $value, string $type = 'string', string $group = 'general'): bool
    {
        $existing = $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE `key` = ? LIMIT 1",
            [$key]
        );
        
        if ($existing) {
            $updated = $this->db->update(
                $this->table,
                ['value' => $value, 'type' => $type],
                'id = ?',
                [$existing['id']]
            ) > 0;
            if ($updated) {
                self::$cache[$key] = $this->castValue($value, $type);
            }
            return $updated;
        }
        
        $this->db->query(
            "INSERT INTO {$this->table} (`key`, `value`, `type`, `group`) VALUES (?, ?, ?, ?)",
            [$key, $value, $type, $group]
        );
        $id = (int) $this->db->lastInsertId();
        
        if ($id) {
            self::$cache[$key] = $this->castValue($value, $type);
        }
        
        return $id > 0;
    }
    
    /**
     * Get settings by group
     */
    public function getByGroup(string $group): array
    {
        $settings = $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE `group` = ?",
            [$group]
        );
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting['key']] = $this->castValue($setting['value'], $setting['type']);
        }
        
        return $result;
    }
    
    /**
     * Get all settings
     */
    public function getAllSettings(): array
    {
        $settings = $this->all();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting['key']] = $this->castValue($setting['value'], $setting['type']);
        }
        
        return $result;
    }
    
    /**
     * Cast value to type
     */
    private function castValue($value, string $type)
    {
        switch ($type) {
            case 'integer':
            case 'int':
                return (int) $value;
            case 'float':
            case 'double':
                return (float) $value;
            case 'boolean':
            case 'bool':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'array':
            case 'json':
                return json_decode($value, true) ?? [];
            default:
                return $value;
        }
    }
    
    /**
     * Get institute info
     */
    public function getInstituteInfo(): array
    {
        return [
            'name' => $this->get('institute_name', 'ITHM'),
            'short_name' => $this->get('institute_short_name', 'ITHM'),
            'logo' => $this->get('institute_logo'),
            'email' => $this->get('institute_email'),
            'phone' => $this->get('institute_phone'),
            'address' => $this->get('institute_address')
        ];
    }
}


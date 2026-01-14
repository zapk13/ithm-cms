<?php
/**
 * Validation Helper Class
 */

namespace App\Helpers;

class ValidationHelper
{
    private array $errors = [];
    private array $data = [];
    
    /**
     * Validate data against rules
     */
    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];
        $this->data = $data;
        
        foreach ($rules as $field => $ruleString) {
            $rulesArray = explode('|', $ruleString);
            
            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $rule);
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Apply a single rule
     */
    private function applyRule(string $field, string $rule): void
    {
        $params = [];
        if (strpos($rule, ':') !== false) {
            [$rule, $paramString] = explode(':', $rule, 2);
            $params = explode(',', $paramString);
        }
        
        $value = $this->data[$field] ?? null;
        $fieldLabel = ucwords(str_replace('_', ' ', $field));
        
        switch ($rule) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->errors[$field][] = "{$fieldLabel} is required.";
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "{$fieldLabel} must be a valid email.";
                }
                break;
                
            case 'min':
                $min = (int) $params[0];
                if (!empty($value) && strlen($value) < $min) {
                    $this->errors[$field][] = "{$fieldLabel} must be at least {$min} characters.";
                }
                break;
                
            case 'max':
                $max = (int) $params[0];
                if (!empty($value) && strlen($value) > $max) {
                    $this->errors[$field][] = "{$fieldLabel} must not exceed {$max} characters.";
                }
                break;
                
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->errors[$field][] = "{$fieldLabel} must be a number.";
                }
                break;
                
            case 'integer':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->errors[$field][] = "{$fieldLabel} must be an integer.";
                }
                break;
                
            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if ($value !== ($this->data[$confirmField] ?? null)) {
                    $this->errors[$field][] = "{$fieldLabel} confirmation does not match.";
                }
                break;
                
            case 'unique':
                // Format: unique:table,column
                if (!empty($value) && count($params) >= 2) {
                    $table = $params[0];
                    $column = $params[1];
                    $exceptId = $params[2] ?? null;
                    
                    $db = \App\Core\Database::getInstance();
                    $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
                    $sqlParams = [$value];
                    
                    if ($exceptId) {
                        $sql .= " AND id != ?";
                        $sqlParams[] = $exceptId;
                    }
                    
                    $count = $db->fetchColumn($sql, $sqlParams);
                    if ($count > 0) {
                        $this->errors[$field][] = "{$fieldLabel} already exists.";
                    }
                }
                break;
                
            case 'exists':
                // Format: exists:table,column
                if (!empty($value) && count($params) >= 2) {
                    $table = $params[0];
                    $column = $params[1];
                    
                    $db = \App\Core\Database::getInstance();
                    $count = $db->fetchColumn(
                        "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?",
                        [$value]
                    );
                    if ($count == 0) {
                        $this->errors[$field][] = "{$fieldLabel} is invalid.";
                    }
                }
                break;
                
            case 'in':
                if (!empty($value) && !in_array($value, $params)) {
                    $options = implode(', ', $params);
                    $this->errors[$field][] = "{$fieldLabel} must be one of: {$options}.";
                }
                break;
                
            case 'date':
                if (!empty($value) && !strtotime($value)) {
                    $this->errors[$field][] = "{$fieldLabel} must be a valid date.";
                }
                break;
                
            case 'phone':
                if (!empty($value) && !preg_match('/^[\d\s\-\+\(\)]{10,20}$/', $value)) {
                    $this->errors[$field][] = "{$fieldLabel} must be a valid phone number.";
                }
                break;
                
            case 'cnic':
                if (!empty($value) && !preg_match('/^\d{5}-\d{7}-\d{1}$/', $value)) {
                    $this->errors[$field][] = "{$fieldLabel} must be in format XXXXX-XXXXXXX-X.";
                }
                break;
                
            case 'alpha':
                if (!empty($value) && !preg_match('/^[\pL\s]+$/u', $value)) {
                    $this->errors[$field][] = "{$fieldLabel} must contain only letters.";
                }
                break;
                
            case 'alpha_num':
                if (!empty($value) && !preg_match('/^[\pL\pN\s]+$/u', $value)) {
                    $this->errors[$field][] = "{$fieldLabel} must contain only letters and numbers.";
                }
                break;
        }
    }
    
    /**
     * Get all errors
     */
    public function errors(): array
    {
        return $this->errors;
    }
    
    /**
     * Get first error for a field
     */
    public function error(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }
    
    /**
     * Check if field has error
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }
    
    /**
     * Get all error messages as flat array
     */
    public function allErrors(): array
    {
        $messages = [];
        foreach ($this->errors as $fieldErrors) {
            $messages = array_merge($messages, $fieldErrors);
        }
        return $messages;
    }
    
    /**
     * Sanitize input
     */
    public static function sanitize($value): string
    {
        if (is_array($value)) {
            return array_map([self::class, 'sanitize'], $value);
        }
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitize array of inputs
     */
    public static function sanitizeArray(array $data): array
    {
        return array_map([self::class, 'sanitize'], $data);
    }
}


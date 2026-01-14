<?php
/**
 * Base Model Class
 */

namespace App\Core;

class Model
{
    protected Database $db;
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = ['password'];
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Find by ID
     */
    public function find(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }
    
    /**
     * Find by ID or throw exception
     */
    public function findOrFail(int $id): array
    {
        $result = $this->find($id);
        if (!$result) {
            throw new \Exception("Record not found in {$this->table}");
        }
        return $result;
    }
    
    /**
     * Get all records
     */
    public function all(): array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table}");
    }
    
    /**
     * Get records with conditions
     */
    public function where(string $column, $value, string $operator = '='): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE {$column} {$operator} ?",
            [$value]
        );
    }
    
    /**
     * Get first record with condition
     */
    public function firstWhere(string $column, $value, string $operator = '='): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE {$column} {$operator} ? LIMIT 1",
            [$value]
        );
    }
    
    /**
     * Create a new record
     */
    public function create(array $data): int
    {
        // Filter only fillable fields
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        
        return $this->db->insert($this->table, $data);
    }
    
    /**
     * Update a record
     */
    public function update(int $id, array $data): bool
    {
        // Filter only fillable fields
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        
        return $this->db->update(
            $this->table,
            $data,
            "{$this->primaryKey} = ?",
            [$id]
        ) > 0;
    }
    
    /**
     * Delete a record
     */
    public function delete(int $id): bool
    {
        return $this->db->delete(
            $this->table,
            "{$this->primaryKey} = ?",
            [$id]
        ) > 0;
    }
    
    /**
     * Count records
     */
    public function count(string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        return (int) $this->db->fetchColumn($sql, $params);
    }
    
    /**
     * Check if record exists
     */
    public function exists(string $column, $value): bool
    {
        return $this->count("{$column} = ?", [$value]) > 0;
    }
    
    /**
     * Paginate results
     */
    public function paginate(int $page = 1, int $perPage = ITEMS_PER_PAGE, string $where = '', array $params = []): array
    {
        $offset = ($page - 1) * $perPage;
        $total = $this->count($where, $params);
        
        $sql = "SELECT * FROM {$this->table}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        $sql .= " ORDER BY {$this->primaryKey} DESC LIMIT {$perPage} OFFSET {$offset}";
        
        $data = $this->db->fetchAll($sql, $params);
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
    
    /**
     * Execute raw query
     */
    public function raw(string $sql, array $params = []): array
    {
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Execute raw query and get first result
     */
    public function rawFirst(string $sql, array $params = []): ?array
    {
        return $this->db->fetch($sql, $params);
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit(): void
    {
        $this->db->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback(): void
    {
        $this->db->rollback();
    }
}


<?php
/**
 * Session Helper Functions
 */

namespace App\Helpers;

class SessionHelper
{
    /**
     * Start session if not started
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Set session value
     */
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get session value
     */
    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Check if session key exists
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove session key
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }
    
    /**
     * Clear all session data
     */
    public static function clear(): void
    {
        session_unset();
    }
    
    /**
     * Destroy session
     */
    public static function destroy(): void
    {
        session_unset();
        session_destroy();
    }
    
    /**
     * Regenerate session ID
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }
    
    /**
     * Set flash message
     */
    public static function flash(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }
    
    /**
     * Get flash message
     */
    public static function getFlash(string $key): ?string
    {
        $message = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $message;
    }
    
    /**
     * Get all flash messages
     */
    public static function getAllFlash(): array
    {
        $flash = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flash;
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Get logged in user ID
     */
    public static function userId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get user role
     */
    public static function userRole(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }
    
    /**
     * Get user campus ID
     */
    public static function userCampusId(): ?int
    {
        return $_SESSION['user_campus_id'] ?? null;
    }
}


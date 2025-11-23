<?php

/**
 * Auth Helper Functions
 * 
 * Provides convenient functions for accessing current user session data
 */

if (!function_exists('current_user')) {
    /**
     * Get current logged-in user data
     * 
     * @return array|null User data array or null if not logged in
     */
    function current_user(): ?array
    {
        $user = session()->get('user');
        return is_array($user) ? $user : null;
    }
}

if (!function_exists('current_role')) {
    /**
     * Get current user's role
     * 
     * @return string|null Role name or null if not logged in
     */
    function current_role(): ?string
    {
        $user = current_user();
        return $user['role'] ?? null;
    }
}

if (!function_exists('current_user_id')) {
    /**
     * Get current user's ID
     * 
     * @return int|null User ID or null if not logged in
     */
    function current_user_id(): ?int
    {
        $user = current_user();
        return isset($user['id_user']) ? (int)$user['id_user'] : null;
    }
}

if (!function_exists('current_user_name')) {
    /**
     * Get current user's name
     * 
     * @return string Display name or 'Guest' if not logged in
     */
    function current_user_name(): string
    {
        $user = current_user();
        return $user['nama'] ?? 'Guest';
    }
}

if (!function_exists('is_logged_in')) {
    /**
     * Check if user is currently logged in
     * 
     * @return bool True if logged in
     */
    function is_logged_in(): bool
    {
        return (bool)session()->get('isLoggedIn');
    }
}

if (!function_exists('has_role')) {
    /**
     * Check if current user has one of the specified roles
     * 
     * @param string|array $roles Role name(s) to check
     * @return bool True if user has one of the roles
     */
    function has_role($roles): bool
    {
        $userRole = current_role();
        if (!$userRole) {
            return false;
        }
        
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($userRole, $roles, true);
    }
}

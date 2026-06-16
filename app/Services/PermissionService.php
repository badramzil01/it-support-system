<?php

namespace App\Services;

class PermissionService
{
    /**
     * Permission definitions grouped by category.
     */
    public static array $permissions = [
        'Ticket Permissions' => [
            'create_ticket',
            'view_ticket',
            'update_ticket',
            'close_ticket',
            'escalate_ticket',
        ],
        'Customer Permissions' => [
            'contact_customer',
            'view_customer_info',
            'reply_to_customer',
        ],
        'Conversation Permissions' => [
            'view_conversations',
            'reply_to_conversations',
            'manage_conversations',
        ],
        'System Permissions' => [
            'dashboard_access',
            'analytics_access',
            'notifications_access',
        ],
        'Integration Permissions' => [
            'jira_access',
            'gmail_access',
        ],
        'User Management' => [
            'manage_users',
            'create_users',
            'edit_users',
            'delete_users',
            'assign_roles',
            'manage_permissions',
        ],
        'Administration' => [
            'manage_settings',
            'manage_api_keys',
            'manage_openai_config',
            'manage_jira_integration',
            'manage_gmail_integration',
            'manage_monitoring',
            'manage_ai_settings',
            'manage_categories',
            'manage_priorities',
            'access_audit_logs',
            'access_reports',
            'export_data',
        ],
    ];

    /**
     * Get all permissions grouped by category.
     */
    public static function getGrouped(): array
    {
        return self::$permissions;
    }

    /**
     * Get all permission names as a flat array.
     */
    public static function getAll(): array
    {
        return array_merge(...array_values(self::$permissions));
    }
}
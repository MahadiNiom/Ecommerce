<?php

namespace App\Support;

use App\Models\User;

final class Permissions
{
    public const VIEW_PRODUCTS = 'view products';

    public const CREATE_PRODUCTS = 'create products';

    public const EDIT_PRODUCTS = 'edit products';

    public const DELETE_PRODUCTS = 'delete products';

    public const VIEW_CATEGORIES = 'view categories';

    public const CREATE_CATEGORIES = 'create categories';

    public const EDIT_CATEGORIES = 'edit categories';

    public const DELETE_CATEGORIES = 'delete categories';

    public const VIEW_BRANDS = 'view brands';

    public const CREATE_BRANDS = 'create brands';

    public const EDIT_BRANDS = 'edit brands';

    public const DELETE_BRANDS = 'delete brands';

    public const VIEW_TAGS = 'view tags';

    public const CREATE_TAGS = 'create tags';

    public const EDIT_TAGS = 'edit tags';

    public const DELETE_TAGS = 'delete tags';

    public const VIEW_ORDERS = 'view orders';

    public const UPDATE_ORDER_STATUS = 'update order status';

    public const VIEW_ROLES = 'view roles';

    public const CREATE_ROLES = 'create roles';

    public const EDIT_ROLES = 'edit roles';

    public const DELETE_ROLES = 'delete roles';

    public const VIEW_PERMISSIONS = 'view permissions';

    public const CREATE_PERMISSIONS = 'create permissions';

    public const EDIT_PERMISSIONS = 'edit permissions';

    public const DELETE_PERMISSIONS = 'delete permissions';

    public const VIEW_USERS = 'view users';

    public const ASSIGN_ROLES = 'assign roles';

    /**
     * @return array<int, string>
     */
    public static function products(): array
    {
        return [
            self::VIEW_PRODUCTS,
            self::CREATE_PRODUCTS,
            self::EDIT_PRODUCTS,
            self::DELETE_PRODUCTS,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function categories(): array
    {
        return [
            self::VIEW_CATEGORIES,
            self::CREATE_CATEGORIES,
            self::EDIT_CATEGORIES,
            self::DELETE_CATEGORIES,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function brands(): array
    {
        return [
            self::VIEW_BRANDS,
            self::CREATE_BRANDS,
            self::EDIT_BRANDS,
            self::DELETE_BRANDS,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function tags(): array
    {
        return [
            self::VIEW_TAGS,
            self::CREATE_TAGS,
            self::EDIT_TAGS,
            self::DELETE_TAGS,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function orders(): array
    {
        return [
            self::VIEW_ORDERS,
            self::UPDATE_ORDER_STATUS,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function roles(): array
    {
        return [
            self::VIEW_ROLES,
            self::CREATE_ROLES,
            self::EDIT_ROLES,
            self::DELETE_ROLES,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function permissions(): array
    {
        return [
            self::VIEW_PERMISSIONS,
            self::CREATE_PERMISSIONS,
            self::EDIT_PERMISSIONS,
            self::DELETE_PERMISSIONS,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function users(): array
    {
        return [
            self::VIEW_USERS,
            self::ASSIGN_ROLES,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function catalog(): array
    {
        return [
            ...self::products(),
            ...self::categories(),
            ...self::brands(),
            ...self::tags(),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function access(): array
    {
        return [
            ...self::roles(),
            ...self::permissions(),
            ...self::users(),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            ...self::catalog(),
            ...self::orders(),
            ...self::access(),
        ];
    }

    /**
     * @param  array<int, string>  $names
     */
    public static function canAny(?User $user, array $names): bool
    {
        return $user !== null && $user->hasAnyPermission($names);
    }
}

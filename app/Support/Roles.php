<?php

namespace App\Support;

use App\Models\AdminUser;

/**
 * Admin user roles. Access is decided by admin ROUTE NAME (middleware App\Http\Middleware\AdminRole), and the sidebar only
 * shows what a role may open. When adding an admin route, add its name prefix to AREAS (unlisted admin routes are Admin-only).
 */
class Roles
{
    public const ROLES = [
        'admin' => ['Admin', 'Everything: website content, enquiries, users, settings, integrations and tracking.'],
        'editor' => ['Editor', 'Website content only: pages, home page, packages, tour types, destinations, quiz, journal, testimonials, FAQs, media, menus and design. No enquiries, users or settings.'],
        'sales' => ['Sales', 'Dashboard and enquiries, to follow up leads (no deleting or exporting). No website editing, users or settings.'],
    ];

    /** Route name prefix => roles allowed (the most specific prefix wins). */
    private const AREAS = [
        'admin.logout' => ['admin', 'editor', 'sales'],
        'admin.dashboard' => ['admin', 'sales'],
        'admin.enquiries' => ['admin', 'sales'],
        'admin.enquiries.destroy' => ['admin'],
        'admin.enquiries.export' => ['admin'],
        'admin.packages' => ['admin', 'editor'],
        'admin.package-images' => ['admin', 'editor'],
        'admin.tour-types' => ['admin', 'editor'],
        'admin.destinations' => ['admin', 'editor'],
        'admin.quiz' => ['admin', 'editor'],
        'admin.pages' => ['admin', 'editor'],
        'admin.testimonials' => ['admin', 'editor'],
        'admin.faqs' => ['admin', 'editor'],
        'admin.posts' => ['admin', 'editor'],
        'admin.media' => ['admin', 'editor'],
        'admin.menus' => ['admin', 'editor'],
        'admin.design' => ['admin', 'editor'],
        // Everything else (users, settings, trust & tracking, GoHighLevel, Google Sheet) is Admin only.
    ];

    public static function options(): array
    {
        return array_map(fn ($r) => $r[0], self::ROLES);
    }

    public static function label(?string $role): string
    {
        return self::ROLES[$role ?? 'admin'][0] ?? 'Admin';
    }

    public static function can(?AdminUser $user, ?string $routeName): bool
    {
        if (! $user || ! $routeName) {
            return false;
        }
        $role = $user->role ?: 'admin';
        if ($role === 'admin') {
            return true;
        }
        $best = null;
        foreach (self::AREAS as $prefix => $roles) {
            if (($routeName === $prefix || str_starts_with($routeName, $prefix.'.')) && ($best === null || strlen($prefix) > strlen($best))) {
                $best = $prefix;
            }
        }

        return $best !== null && in_array($role, self::AREAS[$best], true);
    }

    /** Where a user lands after signing in (their first allowed screen). */
    public static function home(AdminUser $user): string
    {
        return match ($user->role ?: 'admin') {
            'editor' => route('admin.pages.index'),
            default => route('admin.dashboard'),
        };
    }
}

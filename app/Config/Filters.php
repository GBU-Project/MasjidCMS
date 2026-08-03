<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseFilters
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array<string, class-string|list<class-string>>
     *
     * [filter_name => classname]
     * or [filter_name => [classname1, classname2, ...]]
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,
        'auth'          => \App\Filters\AuthenticationFilter::class,
        'rbac'          => \App\Filters\AuthorizationFilter::class,
    ];

    /**
     * List of special required filters.
     *
     * The filters listed here are special. They are applied before and after
     * other kinds of filters, and always applied even if a route does not exist.
     *
     * Filters set by default provide framework functionality. If removed,
     * those functions will no longer work.
     *
     * @see https://codeigniter.com/user_guide/incoming/filters.html#provided-filters
     *
     * @var array{before: list<string>, after: list<string>}
     */
    public array $required = [
        'before' => [
            'forcehttps', // Force Global Secure Requests
        ],
        'after' => [
            'performance', // Performance Metrics
            'toolbar',     // Debug Toolbar
        ],
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array{
     *     before: array<string, array{except: list<string>|string}>|list<string>,
     *     after: array<string, array{except: list<string>|string}>|list<string>
     * }
     */
    public array $globals = [
        'before' => [
            'csrf' => ['except' => ['api/*', 'install/*', 'install']],
            // Fix: 'pagecache' was previously a *required* filter (applied to
            // every single route with no exceptions), which full-page-caches
            // the rendered HTML response -- including any embedded CSRF
            // hidden field -- for Config\Cache::$ttl (60s), keyed generically
            // per URI rather than per session. That meant an admin loading
            // e.g. admin/hero-slides/edit/5 could be served an HTML page
            // cached from an earlier render (their own or another admin's),
            // whose embedded CSRF token no longer matched their current
            // session's token, causing the very next form submit to fail
            // with "The action you requested is not allowed." (only
            // succeeding on a second attempt, after a fresh, uncached
            // render happened to be served). Login and any other
            // CSRF-protected form page are equally exposed. Page caching
            // should only ever apply to genuinely public, session-agnostic
            // pages, so it's scoped here to exclude 'admin/*', 'login', and
            // 'install/*'.
            'pagecache' => ['except' => ['admin/*', 'login', 'install/*', 'install']],
        ],
        'after' => [
            'secureheaders',
            'pagecache' => ['except' => ['admin/*', 'login', 'install/*', 'install']],
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'POST' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don't expect could bypass the filter.
     *
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [
        'csrf' => [
            'before' => [
                'api/financial/*',
            ],
        ],
    ];
}
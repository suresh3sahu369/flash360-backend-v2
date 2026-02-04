/**
 * The application's global HTTP middleware stack.
 * These middleware are run during every request to your application.
 */
protected $middleware = [
    // ✅ Sabse upar CORS middleware jodein (Agar missing hai)
    \Illuminate\Http\Middleware\HandleCors::class, 
    \App\Http\Middleware\TrustProxies::class,
    \Illuminate\Http\Middleware\ValidatePostSize::class,
    \App\Http\Middleware\TrimStrings::class,
    \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
];

/**
 * The application's route middleware aliases.
 */
protected $middlewareAliases = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

    // ✅ Aapka custom middleware
    'check.admin' => \App\Http\Middleware\CheckAdmin::class,
    'check.creator' => \App\Http\Middleware\CheckCreator::class,
];
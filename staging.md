# 📝 Spec-Driven Development Plan: Staging Access Middleware Package

## 🎯 Goal
Create a reusable Laravel package containing the `StagingAccess` Middleware, which restricts access to the application via a shared secret code, **only when the $\text{APP\_ENV}$ is set to $\text{staging}$**.

---

## 1. Package Setup and Structure

| Step | Detail | Output |
| :--- | :--- | :--- |
| **1.1. Create Package** | Initialize a new Composer package (e.g., `acme/laravel-staging-security`). | New package structure with $\text{composer.json}$. |
| **1.2. Define Service Provider** | Create a service provider ($\text{StagingSecurityServiceProvider}$) to handle registration and view publishing. | $\text{src/StagingSecurityServiceProvider.php}$ |
| **1.3. Define Middleware** | Create the core middleware class in the package's $\text{src}$ directory. | $\text{src/Middleware/StagingAccess.php}$ |
| **1.4. Register Middleware** | In the service provider's $\text{boot}$ method, use $\text{Router::aliasMiddleware}$ to register the middleware with the key **$\text{staging.access}$**. | Middleware alias available for use in applications. |

---

## 2. Specification and Testing (Test-Driven Approach)

We will use $\text{Pest}$ or $\text{PHPUnit}$ to define and test the required behavior.

### 2.1. Feature Specification: Middleware Behavior

The middleware must satisfy the following specifications (specs) in order of precedence:

| Spec ID | Condition ($\text{APP\_ENV}$) | Input ($\text{access\_code}$ Query Param) | Expected Behavior | HTTP Status |
| :--- | :--- | :--- | :--- | :--- |
| **$\text{S-1.0}$** | $\text{staging}$ | **Correct Code** (Matches $\text{DEV\_ACCESS\_CODE}$) | **Proceed** | 200 |
| **$\text{S-2.0}$** | $\text{staging}$ | **Incorrect Code** (or Missing) | **Deny Access** (Custom View) | 403 |
| **$\text{S-3.0}$** | **$\text{production}$** | Any Value | **Proceed** (Bypass Check) | 200 |
| **$\text{S-4.0}$** | **$\text{local}$** | Any Value | **Proceed** (Bypass Check) | 200 |

### 2.2. Test Plan Implementation

Create tests mirroring the four specifications above to confirm the environment-specific routing logic works correctly.

---

## 3. Implementation Details

### 3.1. StagingSecurityServiceProvider.php

```php
// src/StagingSecurityServiceProvider.php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Acme\StagingSecurity\Middleware\StagingAccess;

class StagingSecurityServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 1. Register the Middleware Alias
        Route::aliasMiddleware('staging.access', StagingAccess::class);

        // 2. Publish the Unauthorized View (Optional)
        $this->publishes([
            __DIR__.'/../resources/views/errors/dev_unauthorized.blade.php' => 
                resource_path('views/errors/dev_unauthorized.blade.php'),
        ], 'staging-security-views');

        // 3. Load the default denial view (if not published)
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'staging-security');
    }
}


3.2. StagingAccess.php (Middleware)
// src/Middleware/StagingAccess.php

namespace Acme\StagingSecurity\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StagingAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // Specs S-3.0 & S-4.0: Bypass check for 'local' and 'production' environments.
        if (! app()->environment('staging')) {
            return $next($request);
        }

        // Specs S-1.0 & S-2.0: Check for the access code on staging.
        $accessCode = $request->get('access_code');
        $requiredCode = env('DEV_ACCESS_CODE');

        // Check 1: Ensures DEV_ACCESS_CODE is actually set AND 
        // Check 2: The provided access_code matches the required code.
        if (! empty($requiredCode) && $accessCode === $requiredCode) {
            // S-1.0: Success - Proceed
            return $next($request);
        }

        // S-2.0: Denial - Return a custom 403 response
        // Uses the package view name 'staging-security::errors.dev_unauthorized'
        return response()->view('staging-security::errors.dev_unauthorized', [], 403);
    }
}
```

3.3. Denial View File
Create the default denial view at \text{resources/views/errors/dev\_unauthorized.blade.php} inside the package's folder structure.

4. Documentation and Consumption

4.1. Application Configuration
Document that package consumers must:
Install the package: composer require acme/laravel-staging-security
Apply the middleware in \text{routes/web.php}:

Route::middleware('staging.access')->group(function () { 
    // All your application routes
});

Configure the staging deployment .env file:

APP_ENV=staging
DEV_ACCESS_CODE=your_secret_dev_code_here

Publish the denial view (optional for customization):

php artisan vendor:publish --tag=staging-security-views

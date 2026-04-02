<?php
/**
 * Router – URL parser & dispatcher with tenant support.
 */
class Router
{
    private array $routes = [];

    /**
     * Register a GET route.
     */
    public function get(string $path, string $action, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $action, $middleware);
    }

    /**
     * Register a POST route.
     */
    public function post(string $path, string $action, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $action, $middleware);
    }

    private function addRoute(string $method, string $path, string $action, array $middleware): void
    {
        // Convert route params like {id} to regex
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method'     => $method,
            'pattern'    => $pattern,
            'path'       => $path,
            'action'     => $action,
            'middleware'  => $middleware,
        ];
    }

    /**
     * Dispatch the current request URL.
     */
    public function dispatch(string $url): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $url = $url ?: '';

        // ── Try to resolve tenant from first segment ──
        $segments = $url ? explode('/', $url) : [];
        $tenantSlug = null;
        $tenantResolved = false;

        if (!empty($segments)) {
            $potentialSlug = $segments[0];
            // Check if it's a known slug in the database
            if (!$this->isReservedRoute($potentialSlug)) {
                try {
                    $school = Database::fetch(
                        "SELECT id, name, slug, logo_url, currency, academic_session FROM schools WHERE slug = ? AND status = 'active' LIMIT 1",
                        [$potentialSlug]
                    );
                    if ($school) {
                        $tenantSlug = $potentialSlug;
                        $tenantResolved = true;
                        // Set tenant context in session
                        Session::set('tenant_school_id', (int) $school['id']);
                        Session::set('tenant_slug', $school['slug']);
                        Session::set('tenant_name', $school['name']);
                        Session::set('tenant_logo_url', $school['logo_url']);
                        Session::set('tenant_currency', $school['currency'] ?: DEFAULT_CURRENCY);
                        Session::set('tenant_academic_session', $school['academic_session']);

                        // Strip slug from URL for route matching
                        array_shift($segments);
                        $url = implode('/', $segments);
                    }
                } catch (Exception $e) {
                    // DB not ready yet, continue without tenant
                }
            }
        }

        // ── Match route ──
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            $routePath = $route['path'];
            $isTenantRoute = strpos($routePath, '{slug}') !== false;

            // For tenant routes, we need a resolved tenant
            if ($isTenantRoute && !$tenantResolved) continue;

            // For tenant routes, match against URL without the slug prefix
            $matchUrl = $isTenantRoute ? $url : ($tenantResolved ? ($tenantSlug . '/' . $url) : $url);

            // Adjust pattern for tenant routes: remove {slug}/ prefix
            $matchPattern = $route['pattern'];
            if ($isTenantRoute) {
                // Remove the slug capture from pattern since we already resolved it
                $matchPattern = str_replace('(?P<slug>[^/]+)/', '', $matchPattern);
                // Fix double slashes
                $matchPattern = str_replace('//', '/', $matchPattern);
                $matchUrl = $url;
            } else {
                $matchUrl = $tenantResolved ? ($tenantSlug . '/' . $url) : $url;
            }

            if (preg_match($matchPattern, $matchUrl, $matches)) {
                // Run middleware
                foreach ($route['middleware'] as $mw) {
                    Middleware::run($mw);
                }

                // Parse controller@method
                [$controllerName, $methodName] = explode('@', $route['action']);
                $controller = new $controllerName();

                // Collect named params (skip 'slug' – already resolved)
                $params = [];
                foreach ($matches as $key => $val) {
                    if (is_string($key) && $key !== 'slug') {
                        $params[] = $val;
                    }
                }

                // Call controller method with positional params
                call_user_func_array([$controller, $methodName], $params);
                return;
            }
        }

        // ── No route matched → 404 ──
        http_response_code(404);
        echo '<!DOCTYPE html><html><head><title>404 Not Found</title>
        <style>body{font-family:Inter,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#0f172a;color:#e2e8f0}
        .box{text-align:center}.box h1{font-size:6rem;margin:0;background:linear-gradient(135deg,#6366f1,#8b5cf6);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .box p{font-size:1.2rem;color:#94a3b8}.box a{color:#818cf8;text-decoration:none}</style></head>
        <body><div class="box"><h1>404</h1><p>Page not found</p><p><a href="' . APP_URL . '">← Back to Home</a></p></div></body></html>';
    }

    /**
     * Reserved root-level route segments (not school slugs).
     */
    private function isReservedRoute(string $segment): bool
    {
        return in_array($segment, [
            'login', 'logout', 'register', 'schools', 'public', 'api',
            'admin', 'assets', 'css', 'js', 'images', 'uploads', 'finance',
        ], true);
    }
}

<?php

namespace App\Providers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class ConsoleRequestServiceProvider extends ServiceProvider
{
    public function register()
    {
        if ($this->app->runningInConsole()) {
            // 1. Override the entire Gate system
            $this->app->singleton(Gate::class, function () {
                return new class implements Gate {
                    // Implement all Gate methods to always allow access
                    public function has($ability)
                    {
                        return true;
                    }
                    public function define($ability, $callback) {}
                    public function policy($class, $policy) {}
                    public function before(callable $callback) {}
                    public function after(callable $callback) {}
                    public function allows($ability, $arguments = [])
                    {
                        return true;
                    }
                    public function denies($ability, $arguments = [])
                    {
                        return false;
                    }
                    public function check($abilities, $arguments = [])
                    {
                        return true;
                    }
                    public function any($abilities, $arguments = [])
                    {
                        return true;
                    }
                    public function authorize($ability, $arguments = [])
                    {
                        return true;
                    }
                    public function inspect($ability, $arguments = [])
                    {
                        return \Illuminate\Auth\Access\Response::allow();
                    }
                    public function raw($ability, $arguments = [])
                    {
                        return true;
                    }
                    public function getPolicyFor($class)
                    {
                        return null;
                    }
                    public function forUser($user)
                    {
                        return $this;
                    }
                };
            });

            // 2. Force all FormRequest authorization to pass
            $this->app->afterResolving(FormRequest::class, function ($request) {
                $request->merge(['_consoleBypass' => true]);
                $request->authorize = function () {
                    return true;
                };
            });

            
        }
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // 4. Bypass all middleware authorization
            $this->app['router']->aliasMiddleware('auth', function ($request, $next) {
                return $next($request);
            });

            $this->app['router']->aliasMiddleware('can', function ($request, $next, $ability) {
                return $next($request);
            });
        }
    }
}

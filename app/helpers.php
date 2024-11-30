<?php

if (!function_exists('is_route_active')) {
    /**
     * Check if the current route matches the given route name pattern
     *
     * @param string|array $routeNames
     * @return bool
     */
    function is_route_active($routeNames): bool
    {
        if (is_string($routeNames)) {
            return request()->routeIs($routeNames);
        }

        foreach ($routeNames as $routeName) {
            if (request()->routeIs($routeName)) {
                return true;
            }
        }

        return false;
    }
} 
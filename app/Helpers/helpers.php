<?php

if (!function_exists('set_active')) {
    function set_active($routeName)
    {
        if (is_array($routeName)) {
            return in_array(request()->route()->getName(), $routeName) ? 'active' : '';
        }

        return request()->routeIs($routeName) ? 'active' : '';
    }
}

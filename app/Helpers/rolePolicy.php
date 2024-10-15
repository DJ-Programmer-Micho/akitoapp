<?php

if (!function_exists('hasRole')) {
    function hasRole($roles)
    {
        $userRoles = auth()->guard('admin')->user()->roles->pluck('id')->toArray();
        return count(array_intersect($roles, $userRoles)) > 0;
    }
}

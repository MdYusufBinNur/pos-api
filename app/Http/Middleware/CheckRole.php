<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param array $roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $roleIds =
            [
                'admin' => 'admin',
                'super' => 'super',
                'manager' => 'manager',
                'branch_manager' => 'branch_manager',
                'customer' => 'customer',
                'supplier' => 'supplier',
                'seller' => 'seller'
            ];
        $allowedRoleIds = [];
        foreach ($roles as $role) {
            if (isset($roleIds[$role])) {
                $allowedRoleIds[] = $roleIds[$role];
            }
        }

        $allowedRoleIds = array_unique($allowedRoleIds);

        if (Auth::check()) {
            if (in_array(Auth::user()->role, $allowedRoleIds)) {
                return $next($request);
            }
        }

        return response()->json(['error' => "You don't have enough permission for this."]);

    }
}

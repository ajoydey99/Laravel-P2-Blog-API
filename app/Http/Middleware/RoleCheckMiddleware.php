<?php
namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Traits\ApiResponses;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleCheckMiddleware
{
    use ApiResponses;

    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // current user role
        $userRole = $request->user()->role;
        // get user role string value
        $userRoleValue = $userRole instanceof UserRole ? $userRole->value : $userRole;

        if (! in_array($userRoleValue, $roles, true)) {
            return $this->errorResponse(
                null, "You are not authorized to perform this task.", 403
            );
        }

        return $next($request);
    }
}
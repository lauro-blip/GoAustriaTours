<?php

namespace App\Http\Middleware;

use App\Support\Roles;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Admin area: only lets a signed-in admin user open the screens their role allows (App\Support\Roles). */
class AdminRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('admin');
        $route = $request->route()?->getName();
        if ($user && ! Roles::can($user, $route)) {
            if ($request->isMethod('GET') && ! $request->expectsJson()) {
                return redirect(Roles::home($user))->with('status', 'Your role ('.Roles::label($user->role).') does not include that screen.');
            }
            abort(403, 'Your role does not allow this.');
        }

        return $next($request);
    }
}

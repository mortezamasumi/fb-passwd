<?php

namespace Mortezamasumi\FbPasswd\Middleware;

use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mortezamasumi\FbPasswd\Pages\ChangePassword;
use Closure;

class ForcePasswordChangeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->value('force_change_password')) {
            return redirect(ChangePassword::getRoutePath(Filament::getCurrentPanel()));
        }

        return $next($request);
    }
}

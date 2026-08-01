<?php

namespace Mortezamasumi\FbPasswd\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mortezamasumi\FbPasswd\Pages\ChangePassword;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChangeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $panel = Filament::getCurrentPanel();

        if ($user?->getAttribute('force_change_password') && $panel !== null) {
            return redirect(ChangePassword::getRoutePath($panel));
        }

        return $next($request);
    }
}

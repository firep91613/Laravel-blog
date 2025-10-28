<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function __construct(
        protected Redirector $redirect
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isAdmin()) {
            return $this->redirect->route('admin.login');
        }

        return $next($request);
    }
}

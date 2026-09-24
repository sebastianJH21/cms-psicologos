<?php

namespace App\Http\Middleware;

use App\Services\InstallerService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstalled
{
    public function __construct(private InstallerService $installer)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->installer->isInstalled()) {
            return redirect('/instalacion');
        }

        return $next($request);
    }
}

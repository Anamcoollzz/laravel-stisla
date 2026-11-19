<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    /**
     * Redirect root path based on configured admin template.
     */
    public function __invoke(): RedirectResponse
    {
        $template = strtolower((string) config('stisla.admin_template', 'stisla'));

        if ($template === 'spark') {
            return redirect('/pages-spark-admin/dashboard');
        }

        return redirect('/pages/index.html');
    }
}

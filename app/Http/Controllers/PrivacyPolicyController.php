<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Contracts\View\View;

class PrivacyPolicyController extends Controller
{
    /**
     * Display the privacy policy page.
     */
    public function __invoke(): View
    {
        $siteTitle = Setting::getValue('site_title', 'Stisla');
        $siteLogo = Setting::getValue('site_logo');
        $year = Setting::getValue('year', date('Y'));
        $city = Setting::getValue('city', 'Bali');
        $country = Setting::getValue('country', 'Indonesia');
        $content = Setting::getValue('privacy_policy');

        return view('pages.privacy-policy', [
            'siteTitle' => $siteTitle,
            'siteLogo' => $siteLogo,
            'year' => $year,
            'city' => $city,
            'country' => $country,
            'content' => $content,
        ]);
    }
}

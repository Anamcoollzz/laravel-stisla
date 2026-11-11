<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['group', 'key', 'value'])]
class Setting extends Model
{
    /**
     * Get the stored value of a setting, falling back to the config default.
     */
    public static function getValue(string $key, ?string $default = null): ?string
    {
        $aliases = [
            'app_name' => 'site_title',
            'site_title' => 'app_name',
            'app_year' => 'year',
            'year' => 'app_year',
            'app_city' => 'city',
            'city' => 'app_city',
            'app_country' => 'country',
            'country' => 'app_country',
        ];

        $keys = [$key];
        if (isset($aliases[$key])) {
            $keys[] = $aliases[$key];
        }

        $value = static::query()->whereIn('key', $keys)->value('value');

        if ($value !== null && $value !== '') {
            return $value;
        }

        if ($default !== null) {
            return $default;
        }

        foreach (config('settings.groups', []) as $group) {
            if (isset($group['fields'][$key]['default'])) {
                return (string) $group['fields'][$key]['default'];
            }
        }

        return null;
    }

    /**
     * Get resolved SEO metadata with intelligent fallbacks.
     *
     * @param  array<string, string|null>  $custom
     * @return array<string, string|null>
     */
    public static function getSeoMeta(array $custom = []): array
    {
        $appName = static::getValue('site_title') ?: config('app.name', 'Stisla');
        $siteDesc = static::getValue('site_description') ?: 'Best multipurpose ecommerce store and web application.';

        $title = $custom['title'] ?? (static::getValue('meta_title') ?: $appName);
        $description = $custom['description'] ?? (static::getValue('meta_description') ?: $siteDesc);
        $keywords = $custom['keywords'] ?? (static::getValue('meta_keywords') ?: 'ecommerce, store, mart, shopping, stisla, laravel');
        $author = $custom['author'] ?? (static::getValue('meta_author') ?: $appName);
        $robots = $custom['robots'] ?? (static::getValue('meta_robots') ?: 'index, follow');

        $canonical = $custom['canonical'] ?? (static::getValue('canonical_url') ?: url()->current());

        $ogTitle = $custom['og_title'] ?? (static::getValue('og_title') ?: $title);
        $ogDescription = $custom['og_description'] ?? (static::getValue('og_description') ?: $description);
        $ogType = $custom['og_type'] ?? 'website';

        $image = $custom['image'] ?? null;
        if (! $image) {
            $ogImage = static::getValue('og_image');
            $siteLogo = static::getValue('site_logo');

            if ($ogImage && Storage::disk('public')->exists($ogImage)) {
                $image = Storage::disk('public')->url($ogImage);
            } elseif ($siteLogo && Storage::disk('public')->exists($siteLogo)) {
                $image = Storage::disk('public')->url($siteLogo);
            } elseif (file_exists(public_path('mantu/assets/img/logo/logo.png'))) {
                $image = asset('mantu/assets/img/logo/logo.png');
            } else {
                $image = asset('assets/img/stisla-fill.svg');
            }
        }
        if ($image && ! str_starts_with($image, 'http://') && ! str_starts_with($image, 'https://')) {
            $image = asset($image);
        }

        $faviconPath = static::getValue('site_favicon');
        if ($faviconPath && Storage::disk('public')->exists($faviconPath)) {
            $favicon = Storage::disk('public')->url($faviconPath);
        } elseif (file_exists(public_path('mantu/assets/img/favicon/favicon.png'))) {
            $favicon = asset('mantu/assets/img/favicon/favicon.png');
        } else {
            $favicon = asset('favicon.ico');
        }

        $twitterCard = $custom['twitter_card'] ?? (static::getValue('twitter_card') ?: 'summary_large_image');
        $twitterSite = $custom['twitter_site'] ?? (static::getValue('twitter_site') ?: '@stisla');
        $googleVerification = static::getValue('google_site_verification');
        $bingVerification = static::getValue('bing_site_verification');
        $analyticsCode = static::getValue('google_analytics_code');

        return [
            'app_name' => $appName,
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'author' => $author,
            'robots' => $robots,
            'canonical' => $canonical,
            'og_title' => $ogTitle,
            'og_description' => $ogDescription,
            'og_type' => $ogType,
            'og_image' => $image,
            'twitter_card' => $twitterCard,
            'twitter_site' => $twitterSite,
            'google_site_verification' => $googleVerification,
            'bing_site_verification' => $bingVerification,
            'favicon' => $favicon,
            'google_analytics_code' => $analyticsCode,
        ];
    }

    /**
     * Get an about showcase image URL, with fallback to default template asset.
     */
    public static function getAboutImage(int $index): string
    {
        $key = 'about_image_'.$index;
        $path = static::getValue($key);

        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return asset('mantu/assets/img/common/about-'.$index.'.png');
    }

    /**
     * Get resolved About Us page settings with fallbacks.
     *
     * @return array<string, mixed>
     */
    public static function getAboutData(): array
    {
        return [
            'title' => static::getValue('about_title') ?: 'Who We <span>Are?</span>',
            'subtitle' => static::getValue('about_subtitle') ?: 'We’re here to serve only the best products for you. Enriching your homes with the best essentials.',
            'content' => static::getValue('about_content') ?: '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p><p>Lorem Ipsum has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p><p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>',
            'image_1' => static::getAboutImage(1),
            'image_2' => static::getAboutImage(2),
            'image_3' => static::getAboutImage(3),
            'image_4' => static::getAboutImage(4),
        ];
    }

    /**
     * Get resolved Contact & Social settings with fallbacks.
     *
     * @return array<string, string|null>
     */
    public static function getContactData(): array
    {
        return [
            'address' => static::getValue('contact_address') ?: '1234 Elm Street Springfield Avenue, Brooklyn den, IL 62704 USA.',
            'phone' => static::getValue('contact_phone') ?: '+00 9876543210',
            'email' => static::getValue('contact_email') ?: 'example@email.com',
            'facebook' => static::getValue('social_facebook'),
            'twitter' => static::getValue('social_twitter'),
            'linkedin' => static::getValue('social_linkedin'),
            'instagram' => static::getValue('social_instagram'),
        ];
    }

    /**
     * Get all stored values of a group keyed by setting key.
     *
     * @return array<string, string|null>
     */
    public static function valuesForGroup(string $group): array
    {
        return static::query()->where('group', $group)->pluck('value', 'key')->all();
    }

    /**
     * Store a setting value.
     */
    public static function setValue(string $group, string $key, ?string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['group' => $group, 'value' => $value]
        );
    }
}

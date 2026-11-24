<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\AppNotification;
use App\Models\ChatMessage;
use App\Models\FooterProductCategory;
use App\Models\LP\CompanyProfile;
use App\Models\Menu;
use App\Models\News\News;
use App\Models\PaymentBank;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductBanner;
use App\Models\ProductCart;
use App\Models\ProductCategory;
use App\Models\ProductWishlist;
use App\Models\QueueMonitor;
use App\Models\ScheduledTaskRun;
use App\Models\Setting;
use App\Models\StoreService;
use App\Models\Testimonial;
use App\Models\User;
use App\Services\ModelActivityTracker;
use App\Services\ScheduledTaskService;
use Illuminate\Auth\Events\Login;
use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Password::defaults(function () {
            $minLength = 8;
            $requireMixedCase = false;
            $requireNumbers = false;
            $requireSymbols = false;
            $uncompromised = false;

            if (Schema::hasTable('settings')) {
                $minLength = max(6, (int) (Setting::getValue('password_min_length', '8') ?: 8));
                $requireMixedCase = Setting::getValue('password_require_mixed_case', '0') === '1';
                $requireNumbers = Setting::getValue('password_require_numbers', '0') === '1';
                $requireSymbols = Setting::getValue('password_require_symbols', '0') === '1';
                $uncompromised = Setting::getValue('password_uncompromised', '0') === '1';
            }

            $rule = Password::min($minLength);

            if ($requireMixedCase) {
                $rule->mixedCase();
            }

            if ($requireNumbers) {
                $rule->numbers();
            }

            if ($requireSymbols) {
                $rule->symbols();
            }

            if ($uncompromised) {
                $rule->uncompromised();
            }

            return $rule;
        });

        Vite::useBuildDirectory('lp-build');
        Blade::anonymousComponentPath(resource_path('views/lp/components'));

        if (Schema::hasTable('company_profiles')) {
            $companyProfile = CompanyProfile::query()->first();

            if ($companyProfile) {
                config([
                    'company.name' => $companyProfile->name,
                    'company.legal_name' => $companyProfile->legal_name,
                    'company.tagline' => $companyProfile->tagline ?: config('company.tagline'),
                    'company.footer_description' => $companyProfile->footer_description ?: config('company.footer_description'),
                    'company.license_number' => $companyProfile->license_number ?: config('company.license_number'),
                    'company.whatsapp_number' => $companyProfile->whatsapp_number,
                    'company.phone_display' => $companyProfile->phone_display,
                    'company.secondary_phone' => $companyProfile->secondary_phone ?: config('company.secondary_phone'),
                    'company.email' => $companyProfile->email,
                    'company.address' => $companyProfile->address,
                    'company.short_address' => $companyProfile->short_address ?: config('company.short_address'),
                    'company.operating_hours.weekday' => $companyProfile->operating_hours_weekday ?: config('company.operating_hours.weekday'),
                    'company.operating_hours.saturday' => $companyProfile->operating_hours_saturday ?: config('company.operating_hours.saturday'),
                    'company.operating_hours.sunday' => $companyProfile->operating_hours_sunday ?: config('company.operating_hours.sunday'),
                    'company.map_embed_url' => $companyProfile->map_embed_url ?: config('company.map_embed_url'),
                    'company.social_links.instagram' => $companyProfile->instagram_url ?: config('company.social_links.instagram'),
                    'company.social_links.facebook' => $companyProfile->facebook_url ?: config('company.social_links.facebook'),
                    'company.social_links.youtube' => $companyProfile->youtube_url ?: config('company.social_links.youtube'),
                    'company.default_whatsapp_message' => $companyProfile->default_whatsapp_message ?: config('company.default_whatsapp_message'),
                ]);

                View::share('lpCompanyProfile', $companyProfile);
            }
        }

        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        Event::listen(Login::class, function (Login $event): void {
            if ($event->user instanceof User) {
                $event->user->forceFill(['last_logged_in' => now()])->saveQuietly();
            }
        });

        foreach (['created', 'updated', 'deleted'] as $eloquentEvent) {
            Event::listen("eloquent.{$eloquentEvent}: *", function (string $eventName, array $data) use ($eloquentEvent): void {
                $model = $data[0] ?? null;
                if ($model instanceof Model) {
                    ModelActivityTracker::track($eloquentEvent, $model);
                }
            });
        }

        View::composer('layouts.app', function ($view) {
            $authUser = auth()->user();

            if ($authUser instanceof User && $authUser->last_logged_in === null) {
                $authUser->forceFill(['last_logged_in' => now()])->saveQuietly();
            }

            if ($authUser === null || ! Schema::hasTable('chat_messages')) {
                $view->with('navMessages', collect())->with('navUnreadCount', 0);
            } else {
                $chatSummary = ChatMessage::navbarSummaryForUser((int) $authUser->id);
                $view->with('navMessages', $chatSummary['messages'])
                    ->with('navUnreadCount', $chatSummary['unread_count']);
            }

            if ($authUser === null || ! Schema::hasTable('app_notifications')) {
                $view->with('navNotifications', collect())->with('navNotificationUnreadCount', 0);
            } else {
                $notificationSummary = AppNotification::navbarSummaryForUser((int) $authUser->id);
                $view->with('navNotifications', $notificationSummary['notifications'])
                    ->with('navNotificationUnreadCount', $notificationSummary['unread_count']);
            }
        });

        View::composer(['dashboard', 'dashboard-ecommerce', 'dashboards.compro-workers', 'dashboards.user'], function ($view) {
            $announcements = Schema::hasTable('announcements')
                ? Announcement::query()->published()->limit(5)->get()
                : collect();

            $view->with('dashboardAnnouncements', $announcements);
        });

        View::composer(['layouts.app', 'partials.sidebar-pages'], function ($view) {
            if (Schema::hasTable('menus')) {
                $menuApp = (string) config('stisla.menu_app', 'online-shop');
                $sidebarMenus = Menu::sidebarForApp($menuApp);

                $view->with('sidebarMenus', $sidebarMenus);
            }
        });

        View::composer('ecommerce.layouts.app-ecommerce', function ($view) {
            if (Schema::hasTable('product_categories')) {
                $ecommerceCategories = ProductCategory::query()
                    ->with([
                        'children' => fn ($q) => $q->where('is_active', true)->orderBy('order')->with([
                            'children' => fn ($subQ) => $subQ->where('is_active', true)->orderBy('order'),
                        ]),
                    ])
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('order')
                    ->get()
                    ->unique('name')
                    ->values();

                $view->with('ecommerceCategories', $ecommerceCategories);
            } else {
                $view->with('ecommerceCategories', collect());
            }

            if (Schema::hasTable('footer_product_categories')) {
                $footerCategories = FooterProductCategory::query()
                    ->active()
                    ->whereHas('category', fn ($q) => $q->where('is_active', true))
                    ->with('category')
                    ->orderBy('order')
                    ->latest('id')
                    ->get();

                $view->with('footerCategories', $footerCategories);
            } else {
                $view->with('footerCategories', collect());
            }

            if (Schema::hasTable('products')) {
                $randomProducts = Product::query()
                    ->active()
                    ->with(['category', 'brand'])
                    ->inRandomOrder()
                    ->take(4)
                    ->get();

                $view->with('randomProducts', $randomProducts);

                $bestSellerProducts = Product::query()
                    ->active()
                    ->when(Schema::hasTable('order_items'), fn ($q) => $q->withSum('orderItems as total_sold', 'quantity')->orderByDesc('total_sold'))
                    ->orderByDesc('is_trending')
                    ->latest('id')
                    ->take(6)
                    ->get();

                $view->with('bestSellerProducts', $bestSellerProducts);
            } else {
                $view->with('randomProducts', collect())
                    ->with('bestSellerProducts', collect());
            }

            $authUser = auth()->user();
            if ($authUser !== null && Schema::hasTable('product_carts') && Schema::hasTable('product_wishlists')) {
                $cartRows = ProductCart::query()
                    ->where('user_id', $authUser->id)
                    ->with(['product' => fn ($q) => $q->active()])
                    ->get();

                $layoutCart = [];
                foreach ($cartRows as $row) {
                    if ($p = $row->product) {
                        $layoutCart[$p->id] = [
                            'id' => (int) $p->id,
                            'title' => $p->title,
                            'slug' => $p->slug,
                            'price' => (float) $p->final_price,
                            'original_price' => (float) $p->price,
                            'thumbnail' => $p->thumbnail_url,
                            'quantity' => (int) $row->quantity,
                            'stock' => (int) $p->stock,
                        ];
                    }
                }

                $layoutCartSubtotal = array_sum(array_map(fn ($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1), $layoutCart));
                $cartCount = array_sum(array_column($layoutCart, 'quantity'));

                $layoutWishlistProducts = ProductWishlist::query()
                    ->where('user_id', $authUser->id)
                    ->with(['product' => fn ($q) => $q->active()->with(['category', 'brand', 'size', 'color', 'images'])])
                    ->latest('id')
                    ->get()
                    ->pluck('product')
                    ->filter()
                    ->values();

                $userWishlistIds = $layoutWishlistProducts->pluck('id')->map(fn ($id) => (int) $id)->toArray();
                $wishlistCount = count($userWishlistIds);
            } else {
                $layoutCart = [];
                $layoutCartSubtotal = 0.0;
                $cartCount = 0;
                $layoutWishlistProducts = collect();
                $userWishlistIds = [];
                $wishlistCount = 0;
            }

            $siteContact = Schema::hasTable('settings')
                ? Setting::getContactData()
                : [];

            $storefrontBranding = Schema::hasTable('settings')
                ? Setting::getStorefrontData()
                : [];

            $footerPaymentLogos = collect();
            if (Schema::hasTable('payment_banks')) {
                $footerPaymentLogos = $footerPaymentLogos->merge(
                    PaymentBank::query()
                        ->active()
                        ->whereNotNull('logo')
                        ->where('logo', '!=', '')
                        ->orderBy('order')
                        ->get()
                        ->map(fn (PaymentBank $bank) => [
                            'name' => $bank->bank_name,
                            'logo_url' => $bank->logo_url,
                        ])
                );
            }
            if (Schema::hasTable('payment_methods')) {
                $footerPaymentLogos = $footerPaymentLogos->merge(
                    PaymentMethod::query()
                        ->active()
                        ->whereNotNull('logo')
                        ->where('logo', '!=', '')
                        ->orderBy('order')
                        ->get()
                        ->map(fn (PaymentMethod $method) => [
                            'name' => $method->name,
                            'logo_url' => $method->logo_url,
                        ])
                );
            }

            $view->with('layoutCart', $layoutCart)
                ->with('layoutCartSubtotal', $layoutCartSubtotal)
                ->with('cartCount', $cartCount)
                ->with('layoutWishlistProducts', $layoutWishlistProducts)
                ->with('userWishlistIds', $userWishlistIds)
                ->with('wishlistCount', $wishlistCount)
                ->with('siteContact', $siteContact)
                ->with('storefrontBranding', $storefrontBranding)
                ->with('footerPaymentLogos', $footerPaymentLogos);
        });

        View::composer('ecommerce.pages.index', function ($view) {
            if (Schema::hasTable('product_banners')) {
                $heroBanners = ProductBanner::query()
                    ->active()
                    ->hero()
                    ->with('product')
                    ->orderBy('order')
                    ->latest('id')
                    ->get();

                $modernBanners = ProductBanner::query()
                    ->active()
                    ->modern()
                    ->with('product.category')
                    ->orderBy('order')
                    ->latest('id')
                    ->get();

                if ($modernBanners->isEmpty()) {
                    $modernBanners = $heroBanners;
                }

                $view->with('heroBanners', $heroBanners)
                    ->with('modernBanners', $modernBanners);
            } else {
                $view->with('heroBanners', collect())
                    ->with('modernBanners', collect());
            }

            if (Schema::hasTable('product_categories')) {
                $featuredCategories = ProductCategory::query()
                    ->where('is_active', true)
                    ->whereHas('products', fn ($q) => $q->active())
                    ->with('parent')
                    ->withCount(['products' => fn ($q) => $q->active()])
                    ->withMax(['products' => fn ($q) => $q->active()], 'discount_percent')
                    ->with(['products' => fn ($q) => $q->active()->latest('id')])
                    ->orderByDesc('products_count')
                    ->take(8)
                    ->get()
                    ->each(function ($cat) {
                        $cat->setRelation('products', $cat->products->take(3));
                    });

                $view->with('featuredCategories', $featuredCategories);
            } else {
                $view->with('featuredCategories', collect());
            }

            if (Schema::hasTable('products')) {
                $newArrivalProducts = Product::query()
                    ->active()
                    ->with(['category', 'brand', 'size', 'color'])
                    ->latest('id')
                    ->take(12)
                    ->get();

                $view->with('newArrivalProducts', $newArrivalProducts);

                $dealProducts = Product::query()
                    ->active()
                    ->deal()
                    ->with(['category', 'brand', 'size', 'color'])
                    ->latest('id')
                    ->take(12)
                    ->get();

                if ($dealProducts->isEmpty()) {
                    $dealProducts = Product::query()
                        ->active()
                        ->where('discount_percent', '>', 0)
                        ->with(['category', 'brand', 'size', 'color'])
                        ->latest('id')
                        ->take(12)
                        ->get();
                }

                $view->with('dealProducts', $dealProducts);

                $instaProducts = Product::query()
                    ->active()
                    ->inRandomOrder()
                    ->take(8)
                    ->get();

                $view->with('instaProducts', $instaProducts);
            } else {
                $view->with('newArrivalProducts', collect())
                    ->with('dealProducts', collect())
                    ->with('instaProducts', collect());
            }

            if (Schema::hasTable('testimonials')) {
                $testimonials = Testimonial::query()
                    ->active()
                    ->orderBy('order')
                    ->latest('id')
                    ->get();

                $view->with('testimonials', $testimonials);
            } else {
                $view->with('testimonials', collect());
            }

            if (Schema::hasTable('store_services')) {
                $storeServices = StoreService::query()
                    ->active()
                    ->orderBy('order')
                    ->latest('id')
                    ->get();

                $view->with('storeServices', $storeServices);
            } else {
                $view->with('storeServices', collect());
            }
        });

        View::composer('news.partials.footer', function ($view) {
            if (Schema::hasTable('news')) {
                $popularNewsPosts = News::query()
                    ->published()
                    ->with(['publisher'])
                    ->orderByDesc('views_count')
                    ->orderByDesc('published_at')
                    ->latest('id')
                    ->limit(3)
                    ->get();

                $view->with('popularNewsPosts', $popularNewsPosts);
            } else {
                $view->with('popularNewsPosts', collect());
            }
        });

        $this->registerScheduledTaskListeners();
        $this->registerQueueMonitorListeners();
        $this->applyMailConfiguration();
    }

    /**
     * Automatically record Laravel scheduler runs into scheduled_task_runs.
     */
    private function registerScheduledTaskListeners(): void
    {
        $activeRuns = [];

        Event::listen(ScheduledTaskStarting::class, function (ScheduledTaskStarting $event) use (&$activeRuns): void {
            if (! Schema::hasTable('scheduled_task_runs')) {
                return;
            }

            $service = app(ScheduledTaskService::class);
            $taskKey = $service->taskKey($event->task);

            $run = ScheduledTaskRun::query()->create([
                'task_key' => $taskKey,
                'command' => $service->formatCommand($event->task),
                'description' => $event->task->description ?: null,
                'expression' => (string) $event->task->expression,
                'status' => 'running',
                'triggered_by' => 'scheduler',
                'started_at' => now(),
            ]);

            $activeRuns[$taskKey] = [
                'id' => $run->id,
                'start' => microtime(true),
            ];
        });

        Event::listen(ScheduledTaskFinished::class, function (ScheduledTaskFinished $event) use (&$activeRuns): void {
            if (! Schema::hasTable('scheduled_task_runs')) {
                return;
            }

            $service = app(ScheduledTaskService::class);
            $taskKey = $service->taskKey($event->task);
            $meta = $activeRuns[$taskKey] ?? null;
            unset($activeRuns[$taskKey]);

            $durationMs = $meta
                ? (int) round((microtime(true) - $meta['start']) * 1000)
                : (int) round($event->runtime * 1000);

            $exitCode = (int) ($event->task->exitCode ?? 0);

            if ($meta) {
                ScheduledTaskRun::query()->whereKey($meta['id'])->update([
                    'status' => $exitCode === 0 ? 'success' : 'failed',
                    'exit_code' => $exitCode,
                    'duration_ms' => $durationMs,
                    'finished_at' => now(),
                ]);
            }
        });

        Event::listen(ScheduledTaskFailed::class, function (ScheduledTaskFailed $event) use (&$activeRuns): void {
            if (! Schema::hasTable('scheduled_task_runs')) {
                return;
            }

            $service = app(ScheduledTaskService::class);
            $taskKey = $service->taskKey($event->task);
            $meta = $activeRuns[$taskKey] ?? null;
            unset($activeRuns[$taskKey]);

            $durationMs = $meta ? (int) round((microtime(true) - $meta['start']) * 1000) : null;

            if ($meta) {
                ScheduledTaskRun::query()->whereKey($meta['id'])->update([
                    'status' => 'failed',
                    'exit_code' => 1,
                    'duration_ms' => $durationMs,
                    'output' => $event->exception->getMessage(),
                    'finished_at' => now(),
                ]);
            }
        });
    }

    /**
     * Automatically record queue job lifecycle events into queue_monitors.
     */
    private function registerQueueMonitorListeners(): void
    {
        $activeJobs = [];

        Event::listen(JobProcessing::class, function (JobProcessing $event) use (&$activeJobs): void {
            if (! Schema::hasTable('queue_monitors')) {
                return;
            }

            $jobId = (string) ($event->job->getJobId() ?: spl_object_id($event->job));

            $monitor = QueueMonitor::query()->create([
                'job_id' => $jobId,
                'job_name' => $event->job->resolveName(),
                'connection' => $event->connectionName,
                'queue' => $event->job->getQueue() ?: 'default',
                'status' => 'running',
                'attempts' => $event->job->attempts(),
                'started_at' => now(),
            ]);

            $activeJobs[$jobId] = [
                'id' => $monitor->id,
                'start' => microtime(true),
            ];
        });

        Event::listen(JobProcessed::class, function (JobProcessed $event) use (&$activeJobs): void {
            if (! Schema::hasTable('queue_monitors')) {
                return;
            }

            $jobId = (string) ($event->job->getJobId() ?: spl_object_id($event->job));
            $meta = $activeJobs[$jobId] ?? null;
            unset($activeJobs[$jobId]);

            if ($meta) {
                QueueMonitor::query()->whereKey($meta['id'])->update([
                    'status' => 'succeeded',
                    'duration_ms' => (int) round((microtime(true) - $meta['start']) * 1000),
                    'finished_at' => now(),
                ]);
            }
        });

        Event::listen(JobFailed::class, function (JobFailed $event) use (&$activeJobs): void {
            if (! Schema::hasTable('queue_monitors')) {
                return;
            }

            $jobId = (string) ($event->job->getJobId() ?: spl_object_id($event->job));
            $meta = $activeJobs[$jobId] ?? null;
            unset($activeJobs[$jobId]);

            if ($meta) {
                QueueMonitor::query()->whereKey($meta['id'])->update([
                    'status' => 'failed',
                    'duration_ms' => (int) round((microtime(true) - $meta['start']) * 1000),
                    'exception' => (string) $event->exception,
                    'finished_at' => now(),
                ]);
            }
        });
    }

    /**
     * Apply stored SMTP and Email API settings to runtime mail configuration.
     */
    private function applyMailConfiguration(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $email = Setting::valuesForGroup('email');
        if ($email === []) {
            return;
        }

        if (! empty($email['mail_mailer'])) {
            config(['mail.default' => $email['mail_mailer']]);
        }
        if (! empty($email['mail_from_address'])) {
            config(['mail.from.address' => $email['mail_from_address']]);
        }
        if (! empty($email['mail_from_name'])) {
            config(['mail.from.name' => $email['mail_from_name']]);
        }
        if (! empty($email['mail_host'])) {
            config(['mail.mailers.smtp.host' => $email['mail_host']]);
        }
        if (! empty($email['mail_port'])) {
            config(['mail.mailers.smtp.port' => (int) $email['mail_port']]);
        }
        if (array_key_exists('mail_username', $email) && $email['mail_username'] !== null && $email['mail_username'] !== '') {
            config(['mail.mailers.smtp.username' => $email['mail_username']]);
        }
        if (array_key_exists('mail_password', $email) && $email['mail_password'] !== null && $email['mail_password'] !== '') {
            config(['mail.mailers.smtp.password' => $email['mail_password']]);
        }
        if (array_key_exists('mail_encryption', $email)) {
            $scheme = match ($email['mail_encryption']) {
                'ssl' => 'smtps',
                'tls' => 'smtp',
                default => null,
            };
            config(['mail.mailers.smtp.scheme' => $scheme]);
        }
        if (! empty($email['resend_api_key'])) {
            config(['services.resend.key' => $email['resend_api_key']]);
        }
        if (! empty($email['postmark_token'])) {
            config(['services.postmark.key' => $email['postmark_token']]);
        }
        if (! empty($email['ses_key'])) {
            config(['services.ses.key' => $email['ses_key']]);
        }
        if (! empty($email['ses_secret'])) {
            config(['services.ses.secret' => $email['ses_secret']]);
        }
        if (! empty($email['ses_region'])) {
            config(['services.ses.region' => $email['ses_region']]);
        }
    }
}

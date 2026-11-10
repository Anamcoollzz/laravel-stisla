<?php

/*
 | Definition of the application settings, grouped by section.
 | Supported field types: text, email, number, password, textarea, code, image, select, switch.
 */
return [
    'groups' => [
        'general' => [
            'label' => 'General',
            'icon' => 'fas fa-cog',
            'description' => 'General settings such as site title, description, year, city, country, logo and layout.',
            'fields' => [
                'site_title' => ['label' => 'Application Name', 'type' => 'text', 'default' => 'Stisla', 'rules' => ['string', 'max:255']],
                'site_description' => ['label' => 'Site Description', 'type' => 'textarea', 'rules' => ['string', 'max:1000']],
                'year' => ['label' => 'Year', 'type' => 'number', 'default' => '2026', 'help' => 'Year displayed on copyright, footer, and login page', 'rules' => ['nullable', 'integer', 'between:1900,2100']],
                'city' => ['label' => 'City', 'type' => 'text', 'default' => 'Bali', 'help' => 'City displayed on login cover and application info', 'rules' => ['nullable', 'string', 'max:255']],
                'country' => ['label' => 'Country', 'type' => 'text', 'default' => 'Indonesia', 'help' => 'Country displayed on login cover and application info', 'rules' => ['nullable', 'string', 'max:255']],
                'site_logo' => ['label' => 'Site Logo', 'type' => 'image', 'help' => 'The image must have a maximum size of 1MB'],
                'site_favicon' => ['label' => 'Favicon', 'type' => 'image', 'help' => 'The image must have a maximum size of 1MB'],
                'privacy_policy' => [
                    'label' => 'Privacy Policy Content',
                    'type' => 'textarea',
                    'height' => '240px',
                    'help' => 'Content for the privacy policy page. HTML or plain text is allowed. If left empty, default template will be displayed.',
                    'rules' => ['nullable', 'string', 'max:50000'],
                ],
                'login_layout' => [
                    'label' => 'Login Layout',
                    'type' => 'select',
                    'options' => [
                        'default' => 'Default (auth-login.html)',
                        'login-2' => 'Side Cover Split (auth-login-2.html)',
                    ],
                    'default' => 'default',
                    'help' => 'Choose between the default centered card layout or the side cover split layout for the login page.',
                    'rules' => ['in:default,login-2'],
                ],
                'google_analytics_code' => ['label' => 'Google Analytics Code', 'type' => 'code', 'rules' => ['string', 'max:5000']],
            ],
        ],
        'about' => [
            'label' => 'About Us',
            'icon' => 'fas fa-info-circle',
            'description' => 'Customize the ecommerce About Us section showcase images and HTML content.',
            'fields' => [
                'about_title' => [
                    'label' => 'Section Title',
                    'type' => 'text',
                    'default' => 'Who We <span>Are?</span>',
                    'help' => 'Heading displayed on the About Us section. Span tag can be used for highlighted accent color.',
                    'rules' => ['nullable', 'string', 'max:255'],
                ],
                'about_subtitle' => [
                    'label' => 'Section Subtitle',
                    'type' => 'textarea',
                    'default' => 'We’re here to serve only the best products for you. Enriching your homes with the best essentials.',
                    'height' => '80px',
                    'help' => 'Short introductory text below the title.',
                    'rules' => ['nullable', 'string', 'max:1000'],
                ],
                'about_content' => [
                    'label' => 'About Us Content (HTML)',
                    'type' => 'html',
                    'default' => '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p><p>Lorem Ipsum has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p><p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>',
                    'help' => 'Main content describing the store/company. HTML formatting is supported.',
                    'rules' => ['nullable', 'string', 'max:65535'],
                ],
                'about_image_1' => [
                    'label' => 'About Image 1 (Vertical Showcase - Left)',
                    'type' => 'image',
                    'help' => 'Main vertical showcase image on the left. The image must have a maximum size of 2MB.',
                    'rules' => ['image', 'max:2048'],
                ],
                'about_image_2' => [
                    'label' => 'About Image 2 (Horizontal - Top Right)',
                    'type' => 'image',
                    'help' => 'Horizontal showcase image (top right). The image must have a maximum size of 2MB.',
                    'rules' => ['image', 'max:2048'],
                ],
                'about_image_3' => [
                    'label' => 'About Image 3 (Horizontal - Middle Right)',
                    'type' => 'image',
                    'help' => 'Horizontal showcase image (middle right). The image must have a maximum size of 2MB.',
                    'rules' => ['image', 'max:2048'],
                ],
                'about_image_4' => [
                    'label' => 'About Image 4 (Horizontal - Bottom Right)',
                    'type' => 'image',
                    'help' => 'Horizontal showcase image (bottom right). The image must have a maximum size of 2MB.',
                    'rules' => ['image', 'max:2048'],
                ],
            ],
        ],
        'seo' => [
            'label' => 'SEO',
            'icon' => 'fas fa-search',
            'description' => 'Search engine optimization settings, such as meta tags, verification, and social media cards.',
            'fields' => [
                'meta_title' => [
                    'label' => 'Meta Title',
                    'type' => 'text',
                    'default' => 'Stisla - Multipurpose Admin & Ecommerce Dashboard',
                    'help' => 'Default meta title for search engines. Falls back to Application Name if empty.',
                    'rules' => ['nullable', 'string', 'max:255'],
                ],
                'meta_description' => [
                    'label' => 'Meta Description',
                    'type' => 'textarea',
                    'default' => 'Stisla is a clean, modern, and multipurpose admin dashboard and ecommerce template built with Laravel.',
                    'help' => 'Recommended length is between 150-160 characters.',
                    'rules' => ['nullable', 'string', 'max:500'],
                ],
                'meta_keywords' => [
                    'label' => 'Meta Keywords',
                    'type' => 'text',
                    'default' => 'ecommerce, laravel, stisla, bootstrap, store, dashboard, online shop',
                    'help' => 'Separate keywords with commas',
                    'rules' => ['nullable', 'string', 'max:500'],
                ],
                'meta_author' => [
                    'label' => 'Meta Author',
                    'type' => 'text',
                    'default' => 'Stisla',
                    'help' => 'Author or organization name',
                    'rules' => ['nullable', 'string', 'max:255'],
                ],
                'meta_robots' => [
                    'label' => 'Robots Directive',
                    'type' => 'select',
                    'options' => [
                        'index, follow' => 'Index, Follow (Recommended)',
                        'noindex, nofollow' => 'No Index, No Follow (Private)',
                        'index, nofollow' => 'Index, No Follow',
                        'noindex, follow' => 'No Index, Follow',
                    ],
                    'default' => 'index, follow',
                    'help' => 'Instruct search engines how to crawl and index your site',
                    'rules' => ['nullable', 'string', 'max:50'],
                ],
                'canonical_url' => [
                    'label' => 'Canonical URL',
                    'type' => 'text',
                    'help' => 'Base canonical URL (leave empty to automatically use current page URL)',
                    'rules' => ['nullable', 'url', 'max:255'],
                ],
                'og_title' => [
                    'label' => 'Open Graph Title',
                    'type' => 'text',
                    'help' => 'Title shown when shared on Facebook, LinkedIn, etc. Falls back to Meta Title if empty.',
                    'rules' => ['nullable', 'string', 'max:255'],
                ],
                'og_description' => [
                    'label' => 'Open Graph Description',
                    'type' => 'textarea',
                    'help' => 'Description shown when shared on social media. Falls back to Meta Description if empty.',
                    'rules' => ['nullable', 'string', 'max:500'],
                ],
                'og_image' => [
                    'label' => 'Social Media Image (OG Image)',
                    'type' => 'image',
                    'help' => 'Recommended size 1200x630px. Max size 1MB. Falls back to Site Logo if empty.',
                ],
                'twitter_card' => [
                    'label' => 'Twitter Card Type',
                    'type' => 'select',
                    'options' => [
                        'summary_large_image' => 'Summary with Large Image (Recommended)',
                        'summary' => 'Summary (Small Image)',
                    ],
                    'default' => 'summary_large_image',
                    'help' => 'Card layout style when shared on Twitter / X',
                    'rules' => ['nullable', 'in:summary_large_image,summary'],
                ],
                'twitter_site' => [
                    'label' => 'Twitter / X Handle',
                    'type' => 'text',
                    'default' => '@stisla',
                    'help' => 'Twitter username with @ prefix (e.g. @stisla)',
                    'rules' => ['nullable', 'string', 'max:100'],
                ],
                'google_site_verification' => [
                    'label' => 'Google Site Verification',
                    'type' => 'text',
                    'help' => 'Google Search Console verification code (content attribute of google-site-verification meta tag)',
                    'rules' => ['nullable', 'string', 'max:255'],
                ],
                'bing_site_verification' => [
                    'label' => 'Bing Site Verification',
                    'type' => 'text',
                    'help' => 'Bing Webmaster verification code (content attribute of msvalidate.01 meta tag)',
                    'rules' => ['nullable', 'string', 'max:255'],
                ],
            ],
        ],
        'email' => [
            'label' => 'Email',
            'icon' => 'fas fa-envelope',
            'description' => 'Email SMTP settings, notifications and others related to email.',
            'fields' => [
                'mail_from_name' => ['label' => 'Sender Name', 'type' => 'text', 'rules' => ['string', 'max:255']],
                'mail_from_address' => ['label' => 'Sender Email', 'type' => 'email', 'rules' => ['email', 'max:255']],
                'mail_host' => ['label' => 'SMTP Host', 'type' => 'text', 'rules' => ['string', 'max:255']],
                'mail_port' => ['label' => 'SMTP Port', 'type' => 'number', 'rules' => ['integer', 'between:1,65535']],
                'mail_username' => ['label' => 'SMTP Username', 'type' => 'text', 'rules' => ['string', 'max:255']],
                'mail_password' => ['label' => 'SMTP Password', 'type' => 'password', 'rules' => ['string', 'max:255']],
                'mail_encryption' => ['label' => 'Encryption', 'type' => 'select', 'options' => ['' => 'None', 'tls' => 'TLS', 'ssl' => 'SSL'], 'rules' => ['in:,tls,ssl']],
            ],
        ],
        'system' => [
            'label' => 'System',
            'icon' => 'fas fa-power-off',
            'description' => 'Time zones, locale and other environment settings.',
            'fields' => [
                'timezone' => ['label' => 'Timezone', 'type' => 'select', 'options' => [
                    'Asia/Jakarta' => 'Asia/Jakarta (WIB)',
                    'Asia/Makassar' => 'Asia/Makassar (WITA)',
                    'Asia/Jayapura' => 'Asia/Jayapura (WIT)',
                    'UTC' => 'UTC',
                ], 'default' => 'Asia/Jakarta', 'rules' => ['in:Asia/Jakarta,Asia/Makassar,Asia/Jayapura,UTC']],
                'default_locale' => ['label' => 'Default Language', 'type' => 'select', 'options' => ['id' => 'Bahasa Indonesia', 'en' => 'English'], 'default' => 'id', 'rules' => ['in:id,en']],
                'maintenance_mode' => ['label' => 'Maintenance Mode', 'type' => 'switch', 'help' => 'Show a maintenance notice to visitors'],
            ],
        ],
        'security' => [
            'label' => 'Security',
            'icon' => 'fas fa-lock',
            'description' => 'Security settings such as registration, password rules and session.',
            'fields' => [
                'allow_registration' => ['label' => 'Allow Registration', 'type' => 'switch', 'default' => '1', 'help' => 'Allow visitors to create an account'],
                'password_min_length' => ['label' => 'Minimum Password Length', 'type' => 'number', 'default' => '8', 'rules' => ['integer', 'between:6,64']],
                'session_lifetime' => ['label' => 'Session Lifetime (minutes)', 'type' => 'number', 'default' => '120', 'rules' => ['integer', 'between:5,10080']],
            ],
        ],
        'automation' => [
            'label' => 'Automation',
            'icon' => 'fas fa-stopwatch',
            'description' => 'Settings about automation such as cron job, backup automation and so on.',
            'fields' => [
                'auto_backup' => ['label' => 'Automatic Backup', 'type' => 'switch'],
                'backup_frequency' => ['label' => 'Backup Frequency', 'type' => 'select', 'options' => ['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'], 'default' => 'daily', 'rules' => ['in:daily,weekly,monthly']],
                'cron_notes' => ['label' => 'Cron Notes', 'type' => 'textarea', 'rules' => ['string', 'max:1000']],
            ],
        ],
    ],
];

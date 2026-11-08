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
            'description' => 'General settings such as, site title, site description, address and so on.',
            'fields' => [
                'site_title' => ['label' => 'Site Title', 'type' => 'text', 'default' => 'Stisla', 'rules' => ['string', 'max:255']],
                'site_description' => ['label' => 'Site Description', 'type' => 'textarea', 'rules' => ['string', 'max:1000']],
                'site_logo' => ['label' => 'Site Logo', 'type' => 'image', 'help' => 'The image must have a maximum size of 1MB'],
                'site_favicon' => ['label' => 'Favicon', 'type' => 'image', 'help' => 'The image must have a maximum size of 1MB'],
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
        'seo' => [
            'label' => 'SEO',
            'icon' => 'fas fa-search',
            'description' => 'Search engine optimization settings, such as meta tags and social media.',
            'fields' => [
                'meta_title' => ['label' => 'Meta Title', 'type' => 'text', 'rules' => ['string', 'max:255']],
                'meta_description' => ['label' => 'Meta Description', 'type' => 'textarea', 'rules' => ['string', 'max:500']],
                'meta_keywords' => ['label' => 'Meta Keywords', 'type' => 'text', 'help' => 'Separate keywords with commas', 'rules' => ['string', 'max:500']],
                'og_image' => ['label' => 'Social Media Image', 'type' => 'image', 'help' => 'The image must have a maximum size of 1MB'],
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

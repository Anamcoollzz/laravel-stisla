@extends('layouts.auth')

@section('title', __('Privacy Policy'))

@push('css')
  <style>
    .privacy-content h3 {
      font-size: 1.25rem;
      font-weight: 700;
      margin-top: 1.75rem;
      margin-bottom: 0.75rem;
      color: #34395e;
    }
    .privacy-content h4 {
      font-size: 1.1rem;
      font-weight: 600;
      margin-top: 1.25rem;
      margin-bottom: 0.5rem;
      color: #34395e;
    }
    .privacy-content p, .privacy-content li {
      color: #6c757d;
      line-height: 1.8;
      font-size: 0.95rem;
    }
    .privacy-content ul {
      padding-left: 1.25rem;
      margin-bottom: 1rem;
    }
    @media print {
      .no-print {
        display: none !important;
      }
      .card {
        border: none !important;
        box-shadow: none !important;
      }
    }
  </style>
@endpush

@section('content')
  @php
    $logoUrl = $siteLogo ? \Illuminate\Support\Facades\Storage::disk('public')->url($siteLogo) : asset('assets/img/stisla-fill.svg');
    $backRoute = auth()->check() ? route('dashboard') : route('login');
    $backLabel = auth()->check() ? __('Back to Dashboard') : __('Back to Login');
  @endphp

  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-10 col-xl-9">
        <!-- Top Navigation Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
          <a href="{{ $backRoute }}" class="d-flex align-items-center text-decoration-none">
            <img src="{{ $logoUrl }}" alt="{{ $siteTitle }}" width="50" class="shadow-light rounded-circle mr-3">
            <div>
              <h4 class="mb-0 text-dark font-weight-bold">{{ $siteTitle }}</h4>
              <small class="text-muted">{{ __('Privacy Policy') }}</small>
            </div>
          </a>

          <div class="d-flex align-items-center">
            <div class="dropdown mr-2">
              <a href="#" class="btn btn-sm btn-outline-primary dropdown-toggle" data-toggle="dropdown">
                <i class="fas fa-globe mr-1"></i> {{ strtoupper(app()->getLocale()) }}
              </a>
              <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item d-flex align-items-center justify-content-between {{ app()->getLocale() === 'id' ? 'active font-weight-bold text-primary' : '' }}" href="{{ route('lang.switch', 'id') }}">
                  <span><span class="mr-2">🇮🇩</span> Bahasa Indonesia</span>
                  @if (app()->getLocale() === 'id')
                    <i class="fas fa-check text-primary"></i>
                  @endif
                </a>
                <a class="dropdown-item d-flex align-items-center justify-content-between {{ app()->getLocale() === 'en' ? 'active font-weight-bold text-primary' : '' }}" href="{{ route('lang.switch', 'en') }}">
                  <span><span class="mr-2">🇬🇧</span> English</span>
                  @if (app()->getLocale() === 'en')
                    <i class="fas fa-check text-primary"></i>
                  @endif
                </a>
              </div>
            </div>

            <a href="{{ $backRoute }}" class="btn btn-sm btn-primary">
              <i class="fas fa-arrow-left mr-1"></i> {{ $backLabel }}
            </a>
          </div>
        </div>

        <!-- Privacy Policy Card -->
        <div class="card card-primary shadow-sm">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div>
              <h4 class="text-primary font-weight-bold mb-0">
                <i class="fas fa-shield-alt mr-2"></i> {{ __('Privacy Policy') }}
              </h4>
              <small class="text-muted">
                {{ __('Effective & Last Updated') }}: {{ $year }} &bull; {{ $city }}, {{ $country }}
              </small>
            </div>
            <div class="card-header-action no-print">
              <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="fas fa-print mr-1"></i> {{ __('Print') }}
              </button>
            </div>
          </div>

          <div class="card-body p-4 p-md-5 privacy-content">
            @if (! empty($content))
              {!! str_contains($content, '<') ? $content : nl2br(e($content)) !!}
            @else
              <p class="lead text-muted">
                {{ __('At :site, accessible from our application platform, one of our main priorities is the privacy of our visitors and users. This Privacy Policy document outlines the types of information that is collected and recorded by :site and how we use it.', ['site' => $siteTitle]) }}
              </p>

              <hr class="my-4">

              <h3>1. {{ __('Information We Collect') }}</h3>
              <p>
                {{ __('When you use or register on :site, we may collect the following categories of information:', ['site' => $siteTitle]) }}
              </p>
              <ul>
                <li><strong>{{ __('Account Information') }}:</strong> {{ __('Name, email address, password, profile photo, and role assignments.') }}</li>
                <li><strong>{{ __('Operational & Academic Data') }}:</strong> {{ __('Information submitted through modules such as classes, teachers, students, subjects, SPP payments, and support tickets.') }}</li>
                <li><strong>{{ __('Log and Device Data') }}:</strong> {{ __('IP addresses, browser types, timestamps, and pages visited within the system for security auditing and diagnostic purposes.') }}</li>
              </ul>

              <h3>2. {{ __('How We Use Your Information') }}</h3>
              <p>{{ __('We use the information we collect in various ways, including to:') }}</p>
              <ul>
                <li>{{ __('Provide, operate, and maintain our application services.') }}</li>
                <li>{{ __('Authenticate users and prevent unauthorized access or fraud.') }}</li>
                <li>{{ __('Process academic records, ticket resolutions, and real-time internal communications.') }}</li>
                <li>{{ __('Generate audit logs, data exports, and automated database backups.') }}</li>
                <li>{{ __('Comply with legal obligations and enforce platform access controls.') }}</li>
              </ul>

              <h3>3. {{ __('Data Storage & Security') }}</h3>
              <p>
                {{ __('We implement industry-standard technical and organizational security measures, including password hashing (bcrypt), role-based permission checks, encrypted sessions, and secure database backups stored on server storage. While we strive to protect your personal information, no method of transmission over the Internet or electronic storage is 100% secure.') }}
              </p>

              <h3>4. {{ __('Cookies and Session Storage') }}</h3>
              <p>
                {{ __(':site uses session cookies to authenticate user sessions, remember language preferences, and protect against Cross-Site Request Forgery (CSRF). You can manage cookie settings in your browser at any time.', ['site' => $siteTitle]) }}
              </p>

              <h3>5. {{ __('Your Rights & Choices') }}</h3>
              <p>
                {{ __('Depending on applicable data privacy laws, you have the right to request access to your personal data, request correction of inaccurate data, or request account deactivation. You may update your profile details anytime via the Profile Settings page.') }}
              </p>

              <h3>6. {{ __('Changes to This Privacy Policy') }}</h3>
              <p>
                {{ __('We may update our Privacy Policy from time to time. You are advised to review this page periodically for any changes. Any updates become effective immediately upon being published on this page.') }}
              </p>

              <h3>7. {{ __('Contact Us') }}</h3>
              <p>
                {{ __('If you have any questions or suggestions about our Privacy Policy, do not hesitate to contact the administrator of :site (:city, :country).', ['site' => $siteTitle, 'city' => $city, 'country' => $country]) }}
              </p>
            @endif
          </div>

          <div class="card-footer bg-light d-flex justify-content-between align-items-center no-print">
            <span class="text-muted small">
              &copy; {{ $year }} {{ $siteTitle }}. {{ __('All rights reserved.') }}
            </span>
            <a href="{{ $backRoute }}" class="btn btn-outline-primary btn-sm">
              <i class="fas fa-arrow-left mr-1"></i> {{ $backLabel }}
            </a>
          </div>
        </div>

        <div class="simple-footer text-center mt-4 text-muted small no-print">
          Copyright &copy; {{ $siteTitle }} {{ $year }} &bull; {{ __('Made with 💙 by Stisla') }}
        </div>
      </div>
    </div>
  </div>
@endsection


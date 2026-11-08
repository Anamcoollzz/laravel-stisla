@extends('layouts.app')

@section('title', __('Chat Box'))

@push('css')
  <style>
    .chat-contact-item {
      cursor: pointer;
      padding: 10px 12px;
      margin-bottom: 0 !important;
      border-bottom: 1px solid #f9f9f9;
      border-radius: 4px;
      transition: background-color 0.15s ease-in-out;
    }
    .chat-contact-item:last-child {
      border-bottom: none;
    }
    .chat-contact-item:hover {
      background-color: #f8f9fa;
    }
    .chat-contact-item.active-chat {
      background-color: #eef2f7;
    }
    .chat-contact-item img {
      width: 50px !important;
      height: 50px !important;
      min-width: 50px !important;
      min-height: 50px !important;
      max-width: 50px !important;
      padding: 0 !important;
      border-radius: 50% !important;
      object-fit: cover !important;
    }
    .chat-contact-item .media-body {
      text-align: left !important;
    }
    .chat-box .chat-content .chat-item > img {
      width: 50px !important;
      height: 50px !important;
      min-width: 50px !important;
      min-height: 50px !important;
      max-width: 50px !important;
      padding: 0 !important;
      object-fit: cover !important;
    }
  </style>
@endpush

@push('js')
  <script src="{{ asset('node_modules/sweetalert/dist/sweetalert.min.js') }}"></script>
@endpush

@push('page-js')
  <script>
    "use strict";

    function scrollToBottom() {
      var chatContent = document.getElementById('chat-content');
      if (chatContent) {
        chatContent.scrollTop = chatContent.scrollHeight;
      }
    }

    scrollToBottom();

    // Contact search
    $('#search-users').on('keyup', function() {
      var val = $(this).val().toLowerCase();
      $('#users-list .chat-contact-item').filter(function() {
        $(this).toggle($(this).find('.contact-name').text().toLowerCase().indexOf(val) > -1);
      });
    });

    var latestMessageId = 0;
    $('.chat-item[data-message-id]').each(function() {
      var id = parseInt($(this).attr('data-message-id'));
      if (id > latestMessageId) {
        latestMessageId = id;
      }
    });

    // Send message via AJAX
    $('#chat-form').on('submit', function(e) {
      e.preventDefault();
      var input = $('#chat-message-input');
      var text = input.val().trim();
      if (!text) {
        return;
      }

      var url = $(this).attr('action');
      var token = $('input[name="_token"]').val();

      input.val('');
      $('.empty-chat-placeholder').remove();

      var tempId = 'temp-' + Date.now();
      var myAvatar = @json(auth()->user()->avatar_url);
      var currentTime = moment().format('HH:mm');

      var myChatItem = $(
        '<div class="chat-item chat-right" id="' + tempId + '">' +
        '<img src="' + myAvatar + '">' +
        '<div class="chat-details">' +
        '<div class="chat-text">' + $('<div>').text(text).html().replace(/\n/g, '<br>') + '</div>' +
        '<div class="chat-time">' + currentTime + '</div>' +
        '</div>' +
        '</div>'
      );

      $('#chat-content').append(myChatItem);
      scrollToBottom();

      $.ajax({
        url: url,
        method: 'POST',
        data: {
          _token: token,
          message: text
        },
        headers: {
          'Accept': 'application/json'
        },
        success: function(response) {
          if (response && response.message) {
            $('#' + tempId).attr('data-message-id', response.message.id);
            if (response.message.id > latestMessageId) {
              latestMessageId = response.message.id;
            }
          }
        },
        error: function() {
          $('#' + tempId).find('.chat-time').append(' <span class="text-danger"><i class="fas fa-exclamation-circle"></i></span>');
        }
      });
    });

    // Poll for new messages every 3.5 seconds
    @if ($activeUser)
      var pollUrl = @json(route('chat.messages', $activeUser));
      setInterval(function() {
        $.ajax({
          url: pollUrl + '?after_id=' + latestMessageId,
          method: 'GET',
          headers: {
            'Accept': 'application/json'
          },
          success: function(res) {
            if (res && res.messages && res.messages.length > 0) {
              $('.empty-chat-placeholder').remove();
              var shouldScroll = false;
              res.messages.forEach(function(msg) {
                if (msg.id > latestMessageId) {
                  latestMessageId = msg.id;
                  if ($('.chat-item[data-message-id="' + msg.id + '"]').length === 0) {
                    var itemHtml =
                      '<div class="chat-item ' + msg.position + '" data-message-id="' + msg.id + '">' +
                      '<img src="' + msg.picture + '">' +
                      '<div class="chat-details">' +
                      '<div class="chat-text">' + msg.text + '</div>' +
                      '<div class="chat-time">' + msg.time + '</div>' +
                      '</div>' +
                      '</div>';
                    $('#chat-content').append($(itemHtml));
                    shouldScroll = true;
                  }
                }
              });
              if (shouldScroll) {
                scrollToBottom();
              }
            }
          }
        });
      }, 3500);
    @endif

    // Clear conversation
    $('#btn-clear-chat').on('click', function(e) {
      e.preventDefault();
      var form = $('#form-clear-chat');
      swal({
        title: @json(__('Are you sure?')),
        text: @json(__('This will permanently clear this conversation!')),
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then(function(willDelete) {
        if (willDelete) {
          form.submit();
        }
      });
    });

    @if (session('success'))
      swal(@json(__('Success')), @json(session('success')), 'success');
    @endif

    @if (session('error'))
      swal(@json(__('Error')), @json(session('error')), 'error');
    @endif
  </script>
@endpush

@section('content')
  <div class="section-header">
    <h1>{{ __('Chat Box') }}</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></div>
      <div class="breadcrumb-item">{{ __('Modules') }}</div>
      <div class="breadcrumb-item">{{ __('Chat Box') }}</div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">{{ __('Chat Boxes') }}</h2>
    <p class="section-lead">
      {{ __('Communicate directly in real-time with other registered users.') }}
    </p>

    <div class="row">
      <!-- Left Column: User / Contact List -->
      <div class="col-12 col-sm-6 col-lg-4 mb-4">
        <div class="card">
          <div class="card-header">
            <h4>{{ __("Who's Online?") }}</h4>
          </div>
          <div class="card-body">
            @if ($users->count() > 5)
              <div class="form-group mb-3">
                <input type="text" id="search-users" class="form-control form-control-sm" placeholder="{{ __('Search contact...') }}">
              </div>
            @endif

            <ul class="list-unstyled list-unstyled-border mb-0" id="users-list" style="max-height: 480px; overflow-y: auto;">
              @forelse ($users as $user)
                @php
                  $isActive = $activeUser && $activeUser->id === $user->id;
                @endphp
                <li class="media chat-contact-item {{ $isActive ? 'active-chat' : '' }}" onclick="window.location.href='{{ route('chat.index', ['user' => $user->id]) }}'">
                  <img alt="{{ $user->name }}" class="mr-3 rounded-circle" src="{{ $user->avatar_url }}">
                  <div class="media-body">
                    @if ($user->unread_count > 0)
                      <div class="float-right"><span class="badge badge-primary">{{ $user->unread_count }}</span></div>
                    @endif
                    <div class="mt-0 mb-1 font-weight-bold">
                      <a href="{{ route('chat.index', ['user' => $user->id]) }}" class="{{ $isActive ? 'text-primary' : 'text-dark' }} contact-name">{{ $user->name }}</a>
                    </div>
                    @if ($user->isOnline())
                      <div class="text-success text-small font-600-bold"><i class="fas fa-circle"></i> {{ __('Online') }}</div>
                    @else
                      <div class="text-small font-weight-600 text-muted"><i class="fas fa-circle"></i> {{ __('Offline') }}</div>
                    @endif
                  </div>
                </li>
              @empty
                <li class="media text-center text-muted">
                  <div class="media-body py-4">
                    <p class="mb-0">{{ __('No other users found.') }}</p>
                  </div>
                </li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>

      <!-- Right Column: Chat Box -->
      <div class="col-12 col-sm-6 col-lg-8 mb-4">
        @if ($activeUser)
          <div class="card chat-box card-primary" id="mychatbox">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h4>
                @if ($activeUser->isOnline())
                  <i class="fas fa-circle text-success mr-2" title="{{ __('Online') }}" data-toggle="tooltip"></i>
                @else
                  <i class="fas fa-circle text-muted mr-2" title="{{ __('Offline') }}" data-toggle="tooltip"></i>
                @endif
                {{ __('Chat with :name', ['name' => $activeUser->name]) }}
              </h4>
              @can('chat.delete')
                <div class="card-header-action">
                  <form action="{{ route('chat.clear', $activeUser) }}" method="POST" class="d-inline" id="form-clear-chat">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-outline-danger btn-sm" id="btn-clear-chat" data-toggle="tooltip" title="{{ __('Clear Conversation') }}">
                      <i class="fas fa-trash-alt mr-1"></i> {{ __('Clear') }}
                    </button>
                  </form>
                </div>
              @endcan
            </div>

            <div class="card-body chat-content" id="chat-content" style="height: 420px; overflow-y: auto;">
              @forelse ($messages as $msg)
                <div class="chat-item {{ $msg->sender_id === auth()->id() ? 'chat-right' : 'chat-left' }}" data-message-id="{{ $msg->id }}">
                  <img src="{{ $msg->sender?->avatar_url ?? asset('assets/img/avatar/avatar-1.png') }}" alt="{{ $msg->sender?->name }}">
                  <div class="chat-details">
                    <div class="chat-text">{!! nl2br(e($msg->message)) !!}</div>
                    <div class="chat-time">{{ $msg->created_at?->format('H:i') }}</div>
                  </div>
                </div>
              @empty
                <div class="text-center text-muted py-5 empty-chat-placeholder">
                  <i class="far fa-comments fa-3x mb-3 text-primary" style="opacity: 0.5;"></i>
                  <div class="font-weight-bold mb-1">{{ __('No messages yet') }}</div>
                  <p class="text-small text-muted">{{ __('Say hello to :name and start a conversation!', ['name' => $activeUser->name]) }}</p>
                </div>
              @endforelse
            </div>

            <div class="card-footer chat-form">
              @can('chat.create')
                <form id="chat-form" action="{{ route('chat.send', $activeUser) }}" method="POST">
                  @csrf
                  <input type="text" name="message" id="chat-message-input" class="form-control" placeholder="{{ __('Type a message') }}" autocomplete="off" required autofocus>
                  <button type="submit" class="btn btn-primary" id="btn-send-message">
                    <i class="far fa-paper-plane"></i>
                  </button>
                </form>
              @else
                <div class="text-center text-muted py-2 text-small">
                  {{ __('You do not have permission to send messages.') }}
                </div>
              @endcan
            </div>
          </div>
        @else
          <div class="card">
            <div class="card-body text-center py-5">
              <i class="far fa-comment-dots fa-3x mb-3 text-muted"></i>
              <h4>{{ __('No Active Chat') }}</h4>
              <p class="text-muted">{{ __('Select a contact on the left to start chatting.') }}</p>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection

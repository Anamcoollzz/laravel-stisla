<?php

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->actingAsAdmin());

test('tickets index page displays tickets layout matching features-tickets with active sidebar and translations', function () {
    $author = User::factory()->create(['name' => 'Farhan A. Mujib']);
    $ticket = Ticket::factory()->create([
        'user_id' => $author->id,
        'title' => 'Technical problem',
        'description' => 'Ada kendala pada sistem pembayaran SPP.',
        'status' => 'open',
        'priority' => 'high',
    ]);

    $this->get(route('tickets.index'))
        ->assertOk()
        ->assertViewIs('modules.tickets.index')
        ->assertSee('Bantu Pengguna Anda')
        ->assertSee('Technical problem')
        ->assertSee('Farhan A. Mujib')
        ->assertSee('Terbuka')
        ->assertSee('Tinggi')
        ->assertSee('Ada kendala pada sistem pembayaran SPP.')
        ->assertSee('btn-delete')
        ->assertSee('data-toggle="tooltip" title="Detail"><i class="fas fa-eye"></i>', false)
        ->assertSee('data-toggle="tooltip" title="Ubah"><i class="fas fa-pencil-alt"></i>', false)
        ->assertSee('data-toggle="tooltip" title="Hapus"><i class="fas fa-trash"></i>', false)
        ->assertSee('<li class="active"><a class="nav-link" href="'.route('tickets.index').'">Tiket</a></li>', false);
});

test('tickets index page selects active ticket via query parameter', function () {
    $ticket1 = Ticket::factory()->create(['title' => 'First Ticket']);
    $ticket2 = Ticket::factory()->create(['title' => 'Second Ticket', 'description' => 'Specific content for ticket 2']);

    $this->get(route('tickets.index', ['ticket' => $ticket2->id]))
        ->assertOk()
        ->assertSee('Specific content for ticket 2');
});

test('tickets index displays empty state when no tickets exist', function () {
    $this->get(route('tickets.index'))
        ->assertOk()
        ->assertSee('Tidak Ada Tiket Ditemukan')
        ->assertSee('Tambah Tiket');
});

test('tickets show page renders ticket details, replies, and back button with font awesome and tooltip', function () {
    $author = User::factory()->create(['name' => 'Amanda Aprilia']);
    $ticket = Ticket::factory()->create([
        'user_id' => $author->id,
        'title' => 'Cancel my order please!',
        'description' => 'Tolong batalkan pesanan saya.',
    ]);

    $replyUser = User::factory()->create(['name' => 'Support Agent']);
    TicketReply::factory()->create([
        'ticket_id' => $ticket->id,
        'user_id' => $replyUser->id,
        'message' => 'Baik, pesanan sedang kami proses pembatalannya.',
    ]);

    $this->get(route('tickets.show', $ticket))
        ->assertOk()
        ->assertViewIs('modules.tickets.show')
        ->assertSee('Cancel my order please!')
        ->assertSee('Tolong batalkan pesanan saya.')
        ->assertSee('Support Agent')
        ->assertSee('Baik, pesanan sedang kami proses pembatalannya.')
        ->assertSee('btn-sm btn-icon mr-1" data-toggle="tooltip" title="Kembali"><i class="fas fa-arrow-left"></i>', false)
        ->assertSee('btn-sm btn-icon mr-1" data-toggle="tooltip" title="Ubah"><i class="fas fa-pencil-alt"></i>', false)
        ->assertSee('btn-sm btn-icon btn-delete" data-toggle="tooltip" title="Hapus"><i class="fas fa-trash"></i>', false);
});

test('tickets create page renders form with required asterisks and summernote', function () {
    $this->get(route('tickets.create'))
        ->assertOk()
        ->assertViewIs('modules.tickets.create')
        ->assertSee('Tambah Tiket')
        ->assertSee('name="title"', false)
        ->assertSee('name="priority"', false)
        ->assertSee('name="status"', false)
        ->assertSee('name="description"', false)
        ->assertSee('summernote')
        ->assertSee('btn-sm btn-icon mr-1" data-toggle="tooltip" title="Kembali"><i class="fas fa-arrow-left"></i>', false);
});

test('user can store a new ticket and is redirected to index with active ticket', function () {
    $payload = [
        'title' => 'Cannot download report',
        'priority' => 'high',
        'status' => 'open',
        'description' => 'Saat klik tombol download muncul error 500.',
    ];

    $response = $this->post(route('tickets.store'), $payload);

    $ticket = Ticket::query()->where('title', 'Cannot download report')->first();

    expect($ticket)->not->toBeNull()
        ->and($ticket->priority)->toBe('high')
        ->and($ticket->status)->toBe('open');

    $response->assertRedirect(route('tickets.index', ['ticket' => $ticket->id]))
        ->assertSessionHas('success', 'Tiket berhasil ditambahkan.');
});

test('ticket store validation enforces required fields', function () {
    $this->post(route('tickets.store'), [
        'title' => '',
        'priority' => 'invalid_priority',
        'description' => '',
    ])
        ->assertSessionHasErrors(['title', 'priority', 'description']);
});

test('tickets edit page renders form with existing data', function () {
    $ticket = Ticket::factory()->create([
        'title' => 'Original Title',
        'priority' => 'medium',
        'status' => 'in_progress',
        'description' => 'Original Description',
    ]);

    $this->get(route('tickets.edit', $ticket))
        ->assertOk()
        ->assertViewIs('modules.tickets.edit')
        ->assertSee('Ubah Tiket')
        ->assertSee('Original Title')
        ->assertSee('Original Description')
        ->assertSee('value="in_progress" selected', false);
});

test('user can update ticket details successfully', function () {
    $ticket = Ticket::factory()->create([
        'title' => 'Old Title',
        'priority' => 'low',
        'status' => 'open',
    ]);

    $payload = [
        'title' => 'Updated Title',
        'priority' => 'high',
        'status' => 'closed',
        'description' => 'Updated description content.',
    ];

    $this->put(route('tickets.update', $ticket), $payload)
        ->assertRedirect(route('tickets.index', ['ticket' => $ticket->id]))
        ->assertSessionHas('success', 'Tiket berhasil diperbarui.');

    $ticket->refresh();
    expect($ticket->title)->toBe('Updated Title')
        ->and($ticket->priority)->toBe('high')
        ->and($ticket->status)->toBe('closed');
});

test('user can delete ticket successfully', function () {
    $ticket = Ticket::factory()->create();

    $this->delete(route('tickets.destroy', $ticket))
        ->assertRedirect(route('tickets.index'))
        ->assertSessionHas('success', 'Tiket berhasil dihapus.');

    expect(Ticket::query()->find($ticket->id))->toBeNull();
});

test('user can post a reply to a ticket', function () {
    $ticket = Ticket::factory()->create();

    $this->post(route('tickets.reply', $ticket), [
        'message' => 'Ini adalah balasan resmi dari tim kami.',
    ])
        ->assertRedirect(route('tickets.index', ['ticket' => $ticket->id]))
        ->assertSessionHas('success', 'Balasan berhasil dikirim.');

    expect($ticket->replies()->count())->toBe(1)
        ->and($ticket->replies()->first()->message)->toBe('Ini adalah balasan resmi dari tim kami.');
});

test('posting reply requires a message', function () {
    $ticket = Ticket::factory()->create();

    $this->post(route('tickets.reply', $ticket), [
        'message' => '',
    ])->assertSessionHasErrors(['message']);
});

test('user without delete permission cannot delete ticket', function () {
    $user = $this->actingAsUser();
    $ticket = Ticket::factory()->create();

    $this->delete(route('tickets.destroy', $ticket))
        ->assertForbidden();

    expect(Ticket::query()->find($ticket->id))->not->toBeNull();
});

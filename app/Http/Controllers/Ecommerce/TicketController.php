<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Display authenticated customer's ticket list.
     */
    public function index(Request $request): View|RedirectResponse
    {
        if (! Auth::check()) {
            session()->flash('warning', 'Silakan login terlebih dahulu untuk melihat daftar tiket!');

            return redirect()->guest(route('ecommerce.login'));
        }

        $tickets = Ticket::query()
            ->where('user_id', (int) Auth::id())
            ->with(['replies.user'])
            ->latest('id')
            ->get();

        $selectedTicketId = $request->query('ticket');
        $activeTicket = $selectedTicketId
            ? ($tickets->firstWhere('id', (int) $selectedTicketId) ?? $tickets->first())
            : $tickets->first();

        return view('ecommerce.pages.ticket-list', compact('tickets', 'activeTicket'));
    }

    /**
     * Create a new ticket from the Contact Us form.
     */
    public function storeFromContact(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            session()->flash('warning', 'Silakan login terlebih dahulu untuk mengirim pesan atau membuat tiket!');

            return redirect()->guest(route('ecommerce.login'));
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'message.required' => 'Pesan wajib diisi.',
        ]);

        $openTicket = Ticket::query()
            ->where('user_id', (int) Auth::id())
            ->where('status', '!=', 'closed')
            ->latest('id')
            ->first();

        if ($openTicket) {
            return redirect()
                ->route('ecommerce.ticket-list', ['ticket' => $openTicket->id])
                ->with('warning', 'Anda masih memiliki tiket aktif (#'.$openTicket->id.'). Tutup tiket tersebut terlebih dahulu sebelum membuat tiket baru.');
        }

        $ticket = Ticket::query()->create([
            'user_id' => (int) Auth::id(),
            'title' => Str::limit(trim($validated['message']), 60),
            'description' => trim($validated['message']),
            'status' => 'open',
            'priority' => 'medium',
        ]);

        return redirect()
            ->route('ecommerce.ticket-list', ['ticket' => $ticket->id])
            ->with('success', __('Your message has been sent as a new ticket! Our team will respond shortly.'));
    }

    /**
     * Add a customer reply to an existing ticket.
     */
    public function reply(Request $request, Ticket $ticket): RedirectResponse
    {
        if (! Auth::check()) {
            session()->flash('warning', 'Silakan login terlebih dahulu!');

            return redirect()->guest(route('ecommerce.login'));
        }

        abort_if((int) $ticket->user_id !== (int) Auth::id(), 403);

        if ($ticket->status === 'closed') {
            return redirect()
                ->route('ecommerce.ticket-list', ['ticket' => $ticket->id])
                ->with('warning', 'Tiket ini sudah ditutup dan tidak dapat dibalas lagi.');
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'message.required' => 'Balasan tidak boleh kosong.',
        ]);

        $ticket->replies()->create([
            'user_id' => (int) Auth::id(),
            'message' => trim($validated['message']),
        ]);

        return redirect()
            ->route('ecommerce.ticket-list', ['ticket' => $ticket->id])
            ->with('success', __('Reply sent successfully.'));
    }

    /**
     * Close an open ticket owned by the authenticated customer.
     */
    public function close(Ticket $ticket): RedirectResponse
    {
        if (! Auth::check()) {
            session()->flash('warning', 'Silakan login terlebih dahulu!');

            return redirect()->guest(route('ecommerce.login'));
        }

        abort_if((int) $ticket->user_id !== (int) Auth::id(), 403);

        if ($ticket->status !== 'closed') {
            $ticket->update(['status' => 'closed']);
        }

        return redirect()
            ->route('ecommerce.ticket-list', ['ticket' => $ticket->id])
            ->with('success', __('Ticket #:id closed successfully.', ['id' => $ticket->id]));
    }
}

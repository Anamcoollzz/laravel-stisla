<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketReplyRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        Gate::authorize('tickets.view');

        $tickets = Ticket::query()
            ->with(['user', 'replies.user'])
            ->latest('id')
            ->get();

        $selectedTicketId = $request->query('ticket');
        $activeTicket = $selectedTicketId
            ? $tickets->firstWhere('id', (int) $selectedTicketId) ?? $tickets->first()
            : $tickets->first();

        return view('modules.tickets.index', [
            'tickets' => $tickets,
            'activeTicket' => $activeTicket,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('tickets.create');

        return view('modules.tickets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request): RedirectResponse
    {
        Gate::authorize('tickets.create');

        $data = $request->safe()->only(['title', 'description', 'priority']);
        $data['status'] = $request->validated('status') ?? 'open';
        $data['user_id'] = $request->user()->id;

        $ticket = Ticket::query()->create($data);

        return redirect()
            ->route('tickets.index', ['ticket' => $ticket->id])
            ->with('success', __('Ticket created successfully.'));
    }

    /**
     * Duplicate the specified resource.
     */
    public function duplicate(Request $request, Ticket $ticket): RedirectResponse
    {
        Gate::authorize('tickets.create');

        $newTicket = Ticket::query()->create([
            'user_id' => $request->user()->id,
            'title' => $ticket->title.' (Copy)',
            'description' => $ticket->description,
            'priority' => $ticket->priority,
            'status' => 'open',
        ]);

        return redirect()
            ->route('tickets.index', ['ticket' => $newTicket->id])
            ->with('success', __('Ticket duplicated successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket): View
    {
        Gate::authorize('tickets.view');

        $ticket->load(['user', 'replies.user']);

        $tickets = Ticket::query()
            ->with(['user', 'replies.user'])
            ->latest('id')
            ->get();

        return view('modules.tickets.show', [
            'ticket' => $ticket,
            'activeTicket' => $ticket,
            'tickets' => $tickets,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket): View
    {
        Gate::authorize('tickets.edit');

        return view('modules.tickets.edit', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        Gate::authorize('tickets.edit');

        $ticket->update(
            $request->safe()->only(['title', 'description', 'priority', 'status'])
        );

        return redirect()
            ->route('tickets.index', ['ticket' => $ticket->id])
            ->with('success', __('Ticket updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket): RedirectResponse
    {
        Gate::authorize('tickets.delete');

        $ticket->delete();

        return redirect()
            ->route('tickets.index')
            ->with('success', __('Ticket deleted successfully.'));
    }

    /**
     * Add a reply to the specified ticket.
     */
    public function reply(StoreTicketReplyRequest $request, Ticket $ticket): RedirectResponse
    {
        Gate::authorize('tickets.view');

        $ticket->replies()->create([
            'user_id' => $request->user()->id,
            'message' => $request->validated('message'),
        ]);

        return redirect()
            ->route('tickets.index', ['ticket' => $ticket->id])
            ->with('success', __('Reply sent successfully.'));
    }
}

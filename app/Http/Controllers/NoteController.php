<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NoteController extends Controller
{
    /**
     * Display a listing of the user's notebooks.
     */
    public function index(): View
    {
        $notes = Auth::user()->notes()->latest()->get();

        return view('notes.index', compact('notes'));
    }

    /**
     * Store a newly created notebook in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $note = $user->notes()->create($validated);

        // Update UserStats: increment total_notes
        $stats = $user->stats;
        if ($stats) {
            $stats->increment('total_notes');
        }

        return redirect()->route('notes.show', $note)->with('status', 'notebook-created');
    }

    /**
     * Display the specified notebook workspace.
     */
    public function show(Note $note): View
    {
        // Ensure the note belongs to the authenticated user
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('notes.show', compact('note'));
    }

    /**
     * Remove the specified notebook from storage.
     */
    public function destroy(Note $note): RedirectResponse
    {
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $note->delete();

        // Update UserStats: decrement total_notes
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $stats = $user->stats;
        if ($stats && $stats->total_notes > 0) {
            $stats->decrement('total_notes');
        }

        return redirect()->route('notes.index')->with('status', 'notebook-deleted');
    }
}

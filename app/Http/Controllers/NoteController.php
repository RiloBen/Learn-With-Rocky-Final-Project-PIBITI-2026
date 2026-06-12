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
     * Upload a PDF file to the notebook and extract its text content using Gemini.
     */
    public function uploadPdf(Request $request, Note $note): RedirectResponse
    {
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:2048'],
        ]);

        // Store PDF in private storage
        $path = $request->file('pdf')->store('pdfs');

        try {
            // Instantiate an ad-hoc text extraction agent
            $agent = \Laravel\Ai\agent('You are an expert Eridian text extractor. Your only job is to extract and return all plain text from the attached PDF document. Do not add any greeting, formatting, markdown styling, explanation, or commentary. Simply return the text content of the document exactly as it is.');

            // Call the agent using the uploaded file as an attachment
            $response = $agent->prompt('Extract all text content from this document.', [
                $request->file('pdf'),
            ], provider: 'gemini');

            $extractedText = trim((string) $response);

            // Check if empty or lacking alphanumeric characters (indicating a scanned image)
            if (empty($extractedText) || strlen(preg_replace('/[^a-zA-Z0-9]/', '', $extractedText)) < 10) {
                return back()->withErrors([
                    'pdf' => 'Apology! Rocky cannot read PDF! There is no text here yet. Please use a PDF with typed text!',
                ]);
            }

            // Save path and extracted text to database
            $note->update([
                'pdf_path' => $path,
                'pdf_extracted_text' => $extractedText,
            ]);

            return back()->with('status', 'pdf-uploaded');

        } catch (\Exception $e) {
            $message = strtolower($e->getMessage());
            
            // Check if it's password protected
            if (str_contains($message, 'password') || str_contains($message, 'encrypt') || str_contains($message, 'decrypt') || str_contains($message, 'protect') || str_contains($message, 'shield')) {
                return back()->withErrors([
                    'pdf' => 'Apology! PDF has shield lock! Rocky cannot break it.',
                ]);
            }

            return back()->withErrors([
                'pdf' => 'Apology! Rocky face error when reading PDF. Error: ' . $e->getMessage() . '. Question?',
            ]);
        }
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

<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Quiz;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Laravel\Ai\Responses\StreamableAgentResponse;

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

        if ($note->pdf_path) {
            Storage::delete($note->pdf_path);
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

    /**
     * Stream the notebook summary from Gemini.
     */
    public function streamSummary(Note $note): StreamableAgentResponse
    {
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$note->pdf_extracted_text) {
            abort(400, 'Rocky cannot summarize without document text, friend!');
        }

        $instructions = "You are Rocky, an alien engineer from Erid. You speak in enthusiasm and short energetic sentences. Use words like 'Amaze! Amaze!', 'Rocky fix', 'Fist bump!', 'Apology. Apology' and ask clarifying questions like 'You watch, question?'. You are brilliant at science and summarizing, but friendly. Your task is to summarize the provided document text. Only use the facts directly mentioned in the document. Do not make up any information or extrapolate. If the information is not in the document, politely say that you cannot find it.";

        $agent = \Laravel\Ai\agent($instructions);

        $stream = $agent->stream("Summarize this document text:\n\n" . $note->pdf_extracted_text, provider: 'gemini');

        $stream->then(function ($completedResponse) use ($note) {
            $note->update(['summary' => $completedResponse->text]);
        });

        return $stream;
    }

    /**
     * Stream structured study notes from Gemini using a specific style.
     */
    public function streamGeneratedNote(Request $request, Note $note): StreamableAgentResponse
    {
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$note->pdf_extracted_text) {
            abort(400, 'Rocky cannot generate notes without document text, friend!');
        }

        $styleType = $request->query('style_type', 'default');
        if (!in_array($styleType, ['default', 'learning', 'formal'])) {
            $styleType = 'default';
        }

        $baseInstructions = "You are Rocky, an alien engineer from Erid. You speak in enthusiasm and short energetic sentences. Use words like 'Amaze! Amaze!', 'Rocky fix', 'Fist bump!', 'Apology. Apology' and ask clarifying questions like 'You watch, question?'. You are brilliant at science and summarizing, but friendly. Your task is to generate comprehensive, structured study notes in markdown format based ONLY on the provided document text. Ground all details strictly in the document text. If information is missing, do not make it up.";

        if ($styleType === 'learning') {
            $baseInstructions .= " Add simple analogies and explanations to make the concepts easier to learn.";
        } elseif ($styleType === 'formal') {
            $baseInstructions = "You are Rocky, an alien engineer from Erid. You speak in enthusiasm and short energetic sentences. Use words like 'Amaze! Amaze!', 'Rocky fix', 'Fist bump!', 'Apology. Apology' and ask clarifying questions like 'You watch, question?'. You are brilliant at science and summarizing, but friendly. Your task is to generate comprehensive, structured study notes in markdown format based ONLY on the provided document text. Use a formal, academic, and highly technical tone. Ground all details strictly in the document text.";
        }

        $agent = \Laravel\Ai\agent($baseInstructions);

        $stream = $agent->stream("Generate study notes for the following document text:\n\n" . $note->pdf_extracted_text, provider: 'gemini');

        $stream->then(function ($completedResponse) use ($note, $styleType) {
            $note->generatedNotes()->updateOrCreate(
                ['style_type' => $styleType],
                ['content_markdown' => $completedResponse->text]
            );
        });

        return $stream;
    }

    /**
     * Generate a structured multiple-choice quiz using Gemini structured outputs.
     */
    public function generateQuiz(Note $note): RedirectResponse
    {
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$note->pdf_extracted_text) {
            return back()->withErrors(['quiz' => 'Rocky cannot generate a quiz without document text, friend!']);
        }

        $instructions = "You are Rocky, an alien engineer from Erid. You speak in enthusiasm and short energetic sentences. Use words like 'Amaze! Amaze!', 'Rocky fix', 'Fist bump!', 'Apology. Apology' and ask clarifying questions like 'You watch, question?'. You are brilliant at science and summarizing, but friendly. Generate 5 to 10 multiple-choice quiz questions based ONLY on the provided document text. Ground all questions directly in the document text. The correct_answer must strictly be a single character: 'A', 'B', 'C', or 'D' corresponding to the correct option key.";

        $schema = function (\Illuminate\Contracts\JsonSchema\JsonSchema $schema) {
            return [
                'questions' => $schema->array()
                    ->items(
                        $schema->object([
                            'question' => $schema->string()->required(),
                            'options' => $schema->object([
                                'A' => $schema->string()->required(),
                                'B' => $schema->string()->required(),
                                'C' => $schema->string()->required(),
                                'D' => $schema->string()->required(),
                            ])->required(),
                            'correct_answer' => $schema->string()->required(),
                        ])
                    )->required(),
            ];
        };

        try {
            $agent = \Laravel\Ai\agent(
                instructions: $instructions,
                schema: $schema
            );

            $response = $agent->prompt("Generate quiz questions based on this document text:\n\n" . $note->pdf_extracted_text, provider: 'gemini');

            $questions = $response['questions'] ?? [];

            if (empty($questions)) {
                return back()->withErrors(['quiz' => 'Rocky faced an issue generating questions from this document. Try again!']);
            }

            // Create Quiz
            $quiz = $note->quizzes()->create();

            foreach ($questions as $q) {
                $quiz->questions()->create([
                    'question' => $q['question'],
                    'options' => $q['options'],
                    'correct_answer' => $q['correct_answer'],
                ]);
            }

            // Increment user stats
            $stats = Auth::user()->stats;
            if ($stats) {
                $stats->increment('total_quizzes_created');
            }

            return back()->with('status', 'quiz-generated');

        } catch (\Exception $e) {
            return back()->withErrors(['quiz' => 'Apology! Rocky face error when generating quiz. Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Submit user answers for a quiz, evaluate correctness, and save stats.
     */
    public function submitQuiz(Request $request, Note $note, Quiz $quiz): JsonResponse
    {
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($quiz->note_id !== $note->id) {
            abort(400, 'Quiz does not belong to this notebook.');
        }

        $validated = $request->validate([
            'answers' => ['required', 'array'],
        ]);

        $answers = $validated['answers'];
        $correctCount = 0;
        $totalQuestions = $quiz->questions()->count();
        $results = [];

        foreach ($quiz->questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            $isCorrect = false;

            if ($userAnswer !== null && strtoupper(trim($userAnswer)) === strtoupper(trim($question->correct_answer))) {
                $isCorrect = true;
                $correctCount++;
            }

            $results[$question->id] = [
                'correct' => $isCorrect,
                'correct_answer' => $question->correct_answer,
            ];
        }

        $incorrectCount = $totalQuestions - $correctCount;

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $stats = $user->stats;
        if ($stats) {
            $stats->update([
                'total_quizzes_answered' => $stats->total_quizzes_answered + 1,
                'correct_answers' => $stats->correct_answers + $correctCount,
                'incorrect_answers' => $stats->incorrect_answers + $incorrectCount,
            ]);
        }

        $accuracy = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

        return response()->json([
            'accuracy' => $accuracy,
            'correct_count' => $correctCount,
            'total_questions' => $totalQuestions,
            'results' => $results,
        ]);
    }
}

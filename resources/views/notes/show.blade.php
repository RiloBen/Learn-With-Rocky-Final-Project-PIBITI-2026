@extends('layouts.dashboard')

@section('title', $note->title . ' - Rocky Workspace')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Workspace Header info -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-(--border-color) pb-4 gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('notes.index') }}" class="text-(--text-muted) hover:text-(--primary) text-sm transition">← Notebooks</a>
                <span class="text-(--text-muted)">/</span>
                <h1 class="text-2xl font-bold font-['Orbitron'] text-(--text-main) truncate">{{ $note->title }}</h1>
            </div>
            <p class="text-xs text-(--text-muted) mt-1">{{ $note->description }}</p>
        </div>
        
        <div class="flex items-center space-x-2 bg-(--bg-card) px-3 py-1.5 border border-(--border-color) rounded-xl text-xs text-(--text-muted)">
            <span class="w-2.5 h-2.5 rounded-full bg-(--primary) animate-ping"></span>
            <span>Rocky connected</span>
        </div>
    </div>

    <!-- Main Workspace Split Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT PANEL: PDF Intake, Active Document & Quiz List (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Card 1: PDF Upload Intake Area -->
            <div class="bg-(--bg-card) border border-(--border-color) rounded-2xl p-6 space-y-4">
                <h2 class="text-md font-bold font-['Orbitron'] text-(--primary) flex items-center space-x-2">
                    <span>📁</span> <span>PDF Document Intake</span>
                </h2>
                
                @if($errors->has('pdf'))
                    <div class="p-3 bg-red-950/50 border border-red-500 text-red-200 text-xs rounded-xl">
                        {{ $errors->first('pdf') }}
                    </div>
                @endif

                @if($note->pdf_path)
                    <div class="p-4 bg-(--bg-main) border border-green-500/30 rounded-xl flex items-center justify-between">
                        <div class="flex items-center space-x-3 overflow-hidden">
                            <span class="text-2xl">📄</span>
                            <div class="overflow-hidden">
                                <p class="text-sm font-semibold truncate">{{ basename($note->pdf_path) }}</p>
                                <p class="text-[10px] text-green-400">Text successfully extracted!</p>
                            </div>
                        </div>
                        <label for="pdf-change-input" class="text-xs text-red-400 hover:underline cursor-pointer">Change</label>
                    </div>

                    <form action="{{ route('notes.pdf.upload', $note) }}" method="POST" enctype="multipart/form-data" class="hidden" id="pdf-change-form">
                        @csrf
                        <input type="file" name="pdf" id="pdf-change-input" accept="application/pdf" onchange="document.getElementById('pdf-change-form').submit()">
                    </form>
                @else
                    <!-- File Upload Form -->
                    <form action="{{ route('notes.pdf.upload', $note) }}" method="POST" enctype="multipart/form-data" id="pdf-upload-form" class="space-y-3">
                        @csrf
                        <label class="border-2 border-dashed border-(--border-color) rounded-xl p-8 text-center flex flex-col items-center justify-center space-y-3 hover:border-(--primary) transition duration-200 bg-(--bg-main)/50 cursor-pointer block">
                            <input type="file" name="pdf" accept="application/pdf" required class="hidden" onchange="document.getElementById('pdf-upload-form').submit()">
                            <span class="text-4xl">📥</span>
                            <div>
                                <p class="text-sm font-semibold text-(--text-main)">Choose or Drag PDF File</p>
                                <p class="text-[10px] text-(--text-muted) mt-1">Maximum file size: 2MB</p>
                            </div>
                        </label>
                    </form>
                @endif
            </div>

            <!-- Card 2: Document Summary (Grounding Preview) -->
            <div class="bg-(--bg-card) border border-(--border-color) rounded-2xl p-6 space-y-4">
                <h2 class="text-md font-bold font-['Orbitron'] text-(--primary) flex items-center space-x-2">
                    <span>📝</span> <span>Document Summary</span>
                </h2>

                <div class="p-4 bg-(--bg-main) border border-(--border-color) rounded-xl text-xs min-h-[120px] max-h-[200px] overflow-y-auto leading-relaxed" id="document-summary-text">
                    @if($note->summary)
                        <p class="whitespace-pre-line">{{ $note->summary }}</p>
                    @elseif($note->pdf_extracted_text)
                        <div class="space-y-1">
                            <p class="text-[10px] text-(--text-muted) italic">Document Text Preview (Grounding):</p>
                            <p class="line-clamp-6 text-(--text-main) font-mono">{{ Str::limit($note->pdf_extracted_text, 300) }}</p>
                        </div>
                    @else
                        <div class="text-center text-(--text-muted) py-8 space-y-2">
                            <p>"Rocky has not summarized this page yet, friend!"</p>
                            <p class="text-[10px]">Use the right control panel to summarize the document.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card 3: Generated Quizzes Board -->
            <div class="bg-(--bg-card) border border-(--border-color) rounded-2xl p-6 space-y-4">
                <h2 class="text-md font-bold font-['Orbitron'] text-(--primary) flex items-center space-x-2">
                    <span>🎯</span> <span>Quizzes</span>
                </h2>

                <div class="space-y-2 max-h-[250px] overflow-y-auto" id="quiz-list-container">
                    @forelse($note->quizzes as $index => $quiz)
                        <button onclick="selectQuiz({{ $quiz->id }})" class="w-full text-left p-3 bg-(--bg-main) hover:bg-(--bg-card) border border-(--border-color) rounded-xl flex items-center justify-between text-xs transition cursor-pointer">
                            <span>🎯 Quiz #{{ $index + 1 }} ({{ $quiz->questions->count() }} Questions)</span>
                            <span class="text-[10px] text-(--text-muted)">{{ $quiz->created_at->diffForHumans() }}</span>
                        </button>
                    @empty
                        <div class="p-4 bg-(--bg-main) border border-(--border-color) rounded-xl text-center text-xs text-(--text-muted) py-6 space-y-2">
                            <p>"No quizzes generated yet for this notebook!"</p>
                            <p class="text-[10px]">Rocky will create a quiz with 5-10 multiple-choice questions.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Rocky AI workspace & Control Panels (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Controller Panel -->
            <div class="bg-(--bg-card) border border-(--border-color) rounded-2xl p-6 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h2 class="text-md font-bold font-['Orbitron'] text-(--primary) flex items-center space-x-2">
                        <span>🤖</span> <span>Rocky Agent Workspace Control</span>
                    </h2>
                    
                    <!-- Style Toggle Switcher -->
                    <div class="flex bg-(--bg-main) p-1 rounded-xl border border-(--border-color) text-xs self-start sm:self-center">
                        <button id="style-btn-default" onclick="selectStyle('default')" class="px-3 py-1.5 rounded-lg bg-(--primary) text-black font-bold cursor-pointer">Default</button>
                        <button id="style-btn-learning" onclick="selectStyle('learning')" class="px-3 py-1.5 rounded-lg text-(--text-muted) hover:text-(--text-main) transition cursor-pointer">Learning</button>
                        <button id="style-btn-formal" onclick="selectStyle('formal')" class="px-3 py-1.5 rounded-lg text-(--text-muted) hover:text-(--text-main) transition cursor-pointer">Formal</button>
                    </div>
                </div>

                <!-- Generation Trigger Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <button id="generate-note-btn" onclick="generateNote()" class="py-3 px-4 rounded-xl border border-(--border-color) bg-(--bg-main) hover:border-(--primary) hover:text-(--primary) transition text-xs font-bold flex flex-col items-center justify-center space-y-2 {{ $note->pdf_extracted_text ? 'cursor-pointer' : 'cursor-not-allowed opacity-50' }}" {{ $note->pdf_extracted_text ? '' : 'disabled' }}>
                        <span class="text-xl">✍️</span>
                        <span>Create Notes</span>
                    </button>
                    
                    <button id="generate-summary-btn" onclick="generateSummary()" class="py-3 px-4 rounded-xl border border-(--border-color) bg-(--bg-main) hover:border-(--primary) hover:text-(--primary) transition text-xs font-bold flex flex-col items-center justify-center space-y-2 {{ $note->pdf_extracted_text ? 'cursor-pointer' : 'cursor-not-allowed opacity-50' }}" {{ $note->pdf_extracted_text ? '' : 'disabled' }}>
                        <span class="text-xl">📝</span>
                        <span>Summarize Document</span>
                    </button>

                    <form action="{{ route('notes.quiz.generate', $note) }}" method="POST" id="generate-quiz-form" class="hidden">
                        @csrf
                    </form>
                    <button type="button" onclick="generateQuizSubmit()" class="py-3 px-4 rounded-xl border border-(--border-color) bg-(--bg-main) hover:border-(--primary) hover:text-(--primary) transition text-xs font-bold flex flex-col items-center justify-center space-y-2 {{ $note->pdf_extracted_text ? 'cursor-pointer' : 'cursor-not-allowed opacity-50' }}" {{ $note->pdf_extracted_text ? '' : 'disabled' }}>
                        <span class="text-xl">🎯</span>
                        <span>Generate Quiz</span>
                    </button>
                </div>
                
                @if(!$note->pdf_extracted_text)
                    <p class="text-[10px] text-(--text-muted) text-center italic">
                        *Please upload a PDF file first to enable Rocky's AI control buttons.
                    </p>
                @endif
            </div>

            <!-- Workspace Output/Display Area -->
            <div class="bg-(--bg-card) border border-(--border-color) rounded-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-(--border-color) pb-3">
                    <h3 class="font-bold text-sm font-['Orbitron'] text-(--text-main) flex items-center space-x-2" id="workspace-title">
                        <span>📖</span> <span>Rocky's Study Notes</span>
                    </h3>
                    <span class="text-[10px] text-green-400 font-mono" id="workspace-status">Status: Ready</span>
                </div>

                <!-- Scrollable Display Board -->
                <div id="workspace-board" class="p-6 bg-(--bg-main) border border-(--border-color) rounded-xl min-h-[350px] max-h-[500px] overflow-y-auto space-y-4 leading-relaxed text-sm prose prose-invert max-w-none">
                    <!-- Loaded dynamically via JS -->
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Scripts for Markdown & SSE -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    // Configuration for marked.js
    marked.use({
        gfm: true,
        breaks: true
    });

    // Cache of existing generated notes from database
    const generatedNotes = {
        default: @json($note->generatedNotes->where('style_type', 'default')->first()?->content_markdown ?? ''),
        learning: @json($note->generatedNotes->where('style_type', 'learning')->first()?->content_markdown ?? ''),
        formal: @json($note->generatedNotes->where('style_type', 'formal')->first()?->content_markdown ?? ''),
    };

    // Cache of quizzes
    const quizzes = @json($note->quizzes()->with('questions')->get());

    let currentStyle = 'default';
    let activeMode = 'note'; // 'note' or 'quiz'

    // Initialize display board
    document.addEventListener('DOMContentLoaded', () => {
        selectStyle('default');
    });

    function selectStyle(style) {
        activeMode = 'note';
        currentStyle = style;

        // Reset and highlight active style button
        ['default', 'learning', 'formal'].forEach(s => {
            const btn = document.getElementById(`style-btn-${s}`);
            if (s === style) {
                btn.className = "px-3 py-1.5 rounded-lg bg-(--primary) text-black font-bold cursor-pointer";
            } else {
                btn.className = "px-3 py-1.5 rounded-lg text-(--text-muted) hover:text-(--text-main) transition cursor-pointer";
            }
        });

        const titleContainer = document.getElementById('workspace-title');
        titleContainer.innerHTML = `<span>📖</span> <span>Rocky's Study Notes (Style: ${style.toUpperCase()})</span>`;

        const statusBadge = document.getElementById('workspace-status');
        statusBadge.innerText = 'Status: Ready';
        statusBadge.className = 'text-[10px] text-green-400 font-mono';

        const board = document.getElementById('workspace-board');
        
        if (generatedNotes[style] && generatedNotes[style].trim() !== '') {
            board.innerHTML = marked.parse(generatedNotes[style]);
        } else {
            board.innerHTML = `
                <div class="flex flex-col items-center justify-center text-center py-20 space-y-4">
                    <img src="{{ asset('favicon_io/android-chrome-192x192.png') }}" class="w-20 h-20 mx-auto">
                    <div>
                        <p class="font-bold text-(--text-main)">"Workspace is empty for ${style} style, friend!"</p>
                        <p class="text-xs text-(--text-muted) max-w-xs mt-1 mx-auto">
                            Rocky hasn't written notes for this style yet. Click the "Create Notes" button to generate automatically!
                        </p>
                    </div>
                </div>
            `;
        }
    }

    function generateSummary() {
        const summaryText = document.getElementById('document-summary-text');
        const generateBtn = document.getElementById('generate-summary-btn');

        if (!{{ $note->pdf_extracted_text ? 'true' : 'false' }}) return;

        // Update state
        generateBtn.disabled = true;
        generateBtn.classList.add('opacity-50', 'cursor-not-allowed');
        
        summaryText.innerHTML = `
            <div class="space-y-2">
                <p class="text-xs font-semibold text-(--primary) animate-pulse">Rocky is summarizing... Amaze!</p>
                <div class="h-1.5 w-full bg-(--bg-card) rounded-full overflow-hidden">
                    <div class="h-full bg-(--primary) animate-pulse" style="width: 70%"></div>
                </div>
                <p id="summary-stream-output" class="text-xs text-(--text-main) whitespace-pre-line"></p>
            </div>
        `;

        const streamOutput = document.getElementById('summary-stream-output');
        let fullText = '';

        const eventSource = new EventSource("{{ route('notes.summary.stream', $note) }}");

        eventSource.onmessage = function(event) {
            if (event.data === '[DONE]') {
                eventSource.close();
                generateBtn.disabled = false;
                generateBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                return;
            }

            try {
                const data = JSON.parse(event.data);
                if (data.type === 'text_delta') {
                    fullText += data.delta;
                    streamOutput.innerText = fullText;
                }
            } catch (e) {
                console.error("Error parsing summary stream event:", e);
            }
        };

        eventSource.onerror = function(err) {
            console.error("EventSource failed:", err);
            eventSource.close();
            generateBtn.disabled = false;
            generateBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            summaryText.innerHTML = `<p class="text-red-400">Apology! Rocky faced network issue when summarizing documents, friend!</p>`;
        };
    }

    function generateNote() {
        const board = document.getElementById('workspace-board');
        const generateBtn = document.getElementById('generate-note-btn');
        const statusBadge = document.getElementById('workspace-status');

        if (!{{ $note->pdf_extracted_text ? 'true' : 'false' }}) return;

        // Update state
        generateBtn.disabled = true;
        generateBtn.classList.add('opacity-50', 'cursor-not-allowed');
        statusBadge.innerText = 'Status: Streaming...';
        statusBadge.className = 'text-[10px] text-(--primary) font-mono animate-pulse';
        
        board.innerHTML = `
            <div class="flex flex-col items-center justify-center text-center py-20 space-y-4" id="note-stream-loader">
                <span class="text-5xl animate-spin">🐾</span>
                <div>
                    <p class="font-bold text-(--text-main)">Rocky is writing notes! Amaze!</p>
                    <p class="text-xs text-(--text-muted)">Rocky is reading document and writing notes in ${currentStyle} style...</p>
                </div>
            </div>
            <div id="note-stream-output" class="space-y-4"></div>
        `;

        const streamOutput = document.getElementById('note-stream-output');
        let fullMarkdown = '';

        const eventSource = new EventSource(`{{ route('notes.generated-note.stream', $note) }}?style_type=${currentStyle}`);

        eventSource.onmessage = function(event) {
            if (event.data === '[DONE]') {
                eventSource.close();
                generateBtn.disabled = false;
                generateBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                statusBadge.innerText = 'Status: Ready';
                statusBadge.className = 'text-[10px] text-green-400 font-mono';
                
                // Cache final result
                generatedNotes[currentStyle] = fullMarkdown;
                
                // Final styled render
                board.innerHTML = marked.parse(fullMarkdown);
                return;
            }

            try {
                const data = JSON.parse(event.data);
                if (data.type === 'text_delta') {
                    // Remove loader on first data block
                    const loader = document.getElementById('note-stream-loader');
                    if (loader) loader.remove();

                    fullMarkdown += data.delta;
                    streamOutput.innerHTML = marked.parse(fullMarkdown);
                }
            } catch (e) {
                console.error("Error parsing note stream event:", e);
            }
        };

        eventSource.onerror = function(err) {
            console.error("EventSource failed:", err);
            eventSource.close();
            generateBtn.disabled = false;
            generateBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            statusBadge.innerText = 'Status: Error';
            statusBadge.className = 'text-[10px] text-red-400 font-mono';
            board.innerHTML = `<p class="text-red-400 font-bold text-center py-20">Apology! Rocky faced error when writing notes.</p>`;
        };
    }

    function generateQuizSubmit() {
        const statusBadge = document.getElementById('workspace-status');
        const board = document.getElementById('workspace-board');
        
        statusBadge.innerText = 'Status: Generating Quiz...';
        statusBadge.className = 'text-[10px] text-(--primary) font-mono animate-pulse';
        
        board.innerHTML = `
            <div class="flex flex-col items-center justify-center text-center py-20 space-y-4">
                <span class="text-5xl animate-bounce">🎯</span>
                <div>
                    <p class="font-bold text-(--text-main)">Rocky is generating a quiz! Fist bump!</p>
                    <p class="text-xs text-(--text-muted)">Rocky is formulating 5-10 multiple-choice questions from document...</p>
                </div>
            </div>
        `;
        
        document.getElementById('generate-quiz-form').submit();
    }

    function selectQuiz(quizId) {
        activeMode = 'quiz';
        const quiz = quizzes.find(q => q.id === quizId);
        if (!quiz) return;

        // Reset style button states since we are in quiz mode
        ['default', 'learning', 'formal'].forEach(s => {
            document.getElementById(`style-btn-${s}`).className = "px-3 py-1.5 rounded-lg text-(--text-muted) hover:text-(--text-main) transition cursor-pointer";
        });

        const titleContainer = document.getElementById('workspace-title');
        const quizIndex = quizzes.indexOf(quiz) + 1;
        titleContainer.innerHTML = `<span>🎯</span> <span>Rocky Quiz #${quizIndex} (${quiz.questions.length} Questions)</span>`;

        const statusBadge = document.getElementById('workspace-status');
        statusBadge.innerText = 'Status: Quiz Active';
        statusBadge.className = 'text-[10px] text-(--primary) font-mono';

        const board = document.getElementById('workspace-board');
        
        let html = `
            <div class="space-y-6">
                <div class="p-4 bg-(--bg-card) border border-(--border-color) rounded-xl">
                    <p class="text-xs font-semibold text-(--primary)">Fist bump! Let's test your knowledge. Words of encouragement!</p>
                    <p class="text-[10px] text-(--text-muted) mt-1">Choose the best answer for each question below.</p>
                </div>
                
                <form id="quiz-submission-form" onsubmit="submitQuiz(event, ${quiz.id})" class="space-y-6">
        `;

        quiz.questions.forEach((q, qIndex) => {
            html += `
                <div class="p-5 bg-(--bg-card) border border-(--border-color) rounded-xl space-y-3" id="q-block-${q.id}">
                    <p class="font-bold text-sm text-(--text-main)">${qIndex + 1}. ${escapeHtml(q.question)}</p>
                    <div class="grid grid-cols-1 gap-2 pl-2">
            `;

            for (const [key, val] of Object.entries(q.options)) {
                html += `
                    <label class="flex items-start space-x-3 p-3 bg-(--bg-main) hover:bg-(--bg-main)/80 border border-(--border-color) rounded-lg cursor-pointer transition" id="label-${q.id}-${key}">
                        <input type="radio" name="question_${q.id}" value="${key}" required class="mt-0.5 text-(--primary) focus:ring-(--primary)">
                        <span class="text-xs text-(--text-main)"><strong class="text-(--primary)">${key}.</strong> ${escapeHtml(val)}</span>
                    </label>
                `;
            }

            html += `
                    </div>
                    <div id="q-feedback-${q.id}" class="text-[11px] font-bold mt-2 hidden"></div>
                </div>
            `;
        });

        html += `
                    <button type="submit" class="w-full py-3 bg-(--primary) text-black hover:shadow-[0_0_15px_var(--border-glow)] transition rounded-xl font-bold text-xs uppercase cursor-pointer">
                        Submit Your Answers
                    </button>
                </form>
            </div>
        `;

        board.innerHTML = html;
    }

    function submitQuiz(event, quizId) {
        event.preventDefault();
        const quiz = quizzes.find(q => q.id === quizId);
        if (!quiz) return;

        const answers = {};
        quiz.questions.forEach((q) => {
            const selectedOption = document.querySelector(`input[name="question_${q.id}"]:checked`);
            answers[q.id] = selectedOption ? selectedOption.value : '';
        });

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const submitBtn = document.querySelector('#quiz-submission-form button[type="submit"]');

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerText = 'PROCESSING...';
        }

        fetch(`/notes/{{ $note->id }}/quizzes/${quizId}/submit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ answers })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to submit answers.');
            }
            return response.json();
        })
        .then(data => {
            quiz.questions.forEach((q) => {
                const result = data.results[q.id];
                const isCorrect = result ? result.correct : false;
                const correctAnswer = result ? result.correct_answer : q.correct_answer;

                const selectedOption = document.querySelector(`input[name="question_${q.id}"]:checked`);
                const answer = selectedOption ? selectedOption.value : '';

                const feedbackDiv = document.getElementById(`q-feedback-${q.id}`);
                if (feedbackDiv) {
                    feedbackDiv.classList.remove('hidden');
                }

                // Reset label styles
                for (const key of ['A', 'B', 'C', 'D']) {
                    const lbl = document.getElementById(`label-${q.id}-${key}`);
                    if (lbl) {
                        lbl.className = "flex items-start space-x-3 p-3 bg-(--bg-main) hover:bg-(--bg-main)/80 border border-(--border-color) rounded-lg cursor-pointer transition";
                    }
                }

                const selectedLabel = document.getElementById(`label-${q.id}-${answer}`);
                const correctLabel = document.getElementById(`label-${q.id}-${correctAnswer}`);

                if (isCorrect) {
                    if (selectedLabel) {
                        selectedLabel.className = "flex items-start space-x-3 p-3 bg-green-950/20 border border-green-500/50 rounded-lg cursor-pointer transition text-green-300";
                    }
                    if (feedbackDiv) {
                        feedbackDiv.innerHTML = `<span class="text-green-400 font-bold">✓ Correct! Rocky is proud!</span>`;
                    }
                } else {
                    if (selectedLabel) {
                        selectedLabel.className = "flex items-start space-x-3 p-3 bg-red-950/20 border border-red-500/50 rounded-lg cursor-pointer transition text-red-300";
                    }
                    if (correctLabel) {
                        correctLabel.className = "flex items-start space-x-3 p-3 bg-green-950/20 border border-green-500/50 rounded-lg cursor-pointer transition text-green-300";
                    }
                    if (feedbackDiv) {
                        feedbackDiv.innerHTML = `<span class="text-red-400 font-bold">✗ Incorrect. Correct answer: ${correctAnswer}. Rocky is sad.</span>`;
                    }
                }
            });

            // Show accuracy banner at the top of the form
            const board = document.getElementById('workspace-board');
            
            const accuracyBanner = document.createElement('div');
            accuracyBanner.className = `p-4 mb-6 rounded-xl border text-center font-bold text-sm ${data.accuracy >= 80 ? 'bg-green-950/40 border-green-500 text-green-200' : 'bg-red-950/40 border-red-500 text-red-200'}`;
            
            let message = `Your Score: ${data.correct_count}/${data.total_questions} (${data.accuracy}%). `;
            if (data.accuracy >= 80) {
                message += `Amaze! Rocky fix fist bump! 🐾`;
            } else {
                message += `Rocky suggests reading the document again! Question?`;
            }
            accuracyBanner.innerHTML = message;
            
            const form = document.getElementById('quiz-submission-form');
            form.insertBefore(accuracyBanner, form.firstChild);

            // Scroll to top of board
            board.scrollTop = 0;

            // Disable all inputs so they cannot submit again
            document.querySelectorAll('#quiz-submission-form input[type="radio"]').forEach(input => {
                input.disabled = true;
            });
            if (submitBtn) {
                submitBtn.remove();
            }
        })
        .catch(err => {
            console.error("Failed to submit quiz:", err);
            alert("An error occurred while processing the quiz. Please try again!");
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerText = 'SUBMIT YOUR ANSWERS';
            }
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>
@endsection

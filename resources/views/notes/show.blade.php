@extends('layouts.dashboard')

@section('title', $note->title . ' - Rocky Workspace')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Workspace Header info -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-[color:var(--border-color)] pb-4 gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('notes.index') }}" class="text-[color:var(--text-muted)] hover:text-[color:var(--primary)] text-sm transition">← Notebooks</a>
                <span class="text-[color:var(--text-muted)]">/</span>
                <h1 class="text-2xl font-bold font-['Orbitron'] text-[color:var(--text-main)] truncate">{{ $note->title }}</h1>
            </div>
            <p class="text-xs text-[color:var(--text-muted)] mt-1">{{ $note->description }}</p>
        </div>
        
        <div class="flex items-center space-x-2 bg-[color:var(--bg-card)] px-3 py-1.5 border border-[color:var(--border-color)] rounded-xl text-xs text-[color:var(--text-muted)]">
            <span class="w-2.5 h-2.5 rounded-full bg-[color:var(--primary)] animate-ping"></span>
            <span>Rocky connected</span>
        </div>
    </div>

    <!-- Main Workspace Split Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT PANEL: PDF Intake, Active Document & Quiz List (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Card 1: PDF Upload Intake Area -->
            <div class="bg-[color:var(--bg-card)] border border-[color:var(--border-color)] rounded-2xl p-6 space-y-4">
                <h2 class="text-md font-bold font-['Orbitron'] text-[color:var(--primary)] flex items-center space-x-2">
                    <span>📁</span> <span>PDF Document Intake</span>
                </h2>
                
                @if($errors->has('pdf'))
                    <div class="p-3 bg-red-950/50 border border-red-500 text-red-200 text-xs rounded-xl">
                        {{ $errors->first('pdf') }}
                    </div>
                @endif

                @if($note->pdf_path)
                    <div class="p-4 bg-[color:var(--bg-main)] border border-green-500/30 rounded-xl flex items-center justify-between">
                        <div class="flex items-center space-x-3 overflow-hidden">
                            <span class="text-2xl">📄</span>
                            <div class="overflow-hidden">
                                <p class="text-sm font-semibold truncate">{{ basename($note->pdf_path) }}</p>
                                <p class="text-[10px] text-green-400">Teks berhasil diekstrak!</p>
                            </div>
                        </div>
                        <label for="pdf-change-input" class="text-xs text-red-400 hover:underline cursor-pointer">Ganti</label>
                    </div>

                    <form action="{{ route('notes.pdf.upload', $note) }}" method="POST" enctype="multipart/form-data" class="hidden" id="pdf-change-form">
                        @csrf
                        <input type="file" name="pdf" id="pdf-change-input" accept="application/pdf" onchange="document.getElementById('pdf-change-form').submit()">
                    </form>
                @else
                    <!-- File Upload Form -->
                    <form action="{{ route('notes.pdf.upload', $note) }}" method="POST" enctype="multipart/form-data" id="pdf-upload-form" class="space-y-3">
                        @csrf
                        <label class="border-2 border-dashed border-[color:var(--border-color)] rounded-xl p-8 text-center flex flex-col items-center justify-center space-y-3 hover:border-[color:var(--primary)] transition duration-200 bg-[color:var(--bg-main)]/50 cursor-pointer block">
                            <input type="file" name="pdf" accept="application/pdf" required class="hidden" onchange="document.getElementById('pdf-upload-form').submit()">
                            <span class="text-4xl">📥</span>
                            <div>
                                <p class="text-sm font-semibold text-[color:var(--text-main)]">Pilih atau Seret Berkas PDF</p>
                                <p class="text-[10px] text-[color:var(--text-muted)] mt-1">Ukuran berkas maksimal: 2MB</p>
                            </div>
                        </label>
                    </form>
                @endif
            </div>

            <!-- Card 2: Document Summary (Grounding Preview) -->
            <div class="bg-[color:var(--bg-card)] border border-[color:var(--border-color)] rounded-2xl p-6 space-y-4">
                <h2 class="text-md font-bold font-['Orbitron'] text-[color:var(--primary)] flex items-center space-x-2">
                    <span>📝</span> <span>Ringkasan Dokumen</span>
                </h2>

                <div class="p-4 bg-[color:var(--bg-main)] border border-[color:var(--border-color)] rounded-xl text-xs min-h-[120px] max-h-[200px] overflow-y-auto leading-relaxed">
                    @if($note->summary)
                        <p class="whitespace-pre-line">{{ $note->summary }}</p>
                    @elseif($note->pdf_extracted_text)
                        <div class="space-y-1">
                            <p class="text-[10px] text-[color:var(--text-muted)] italic">Pratinjau Teks Dokumen (Grounding):</p>
                            <p class="line-clamp-6 text-[color:var(--text-main)] font-mono">{{ Str::limit($note->pdf_extracted_text, 300) }}</p>
                        </div>
                    @else
                        <div class="text-center text-[color:var(--text-muted)] py-8 space-y-2">
                            <p>"Rocky has not summarized this page yet, friend!"</p>
                            <p class="text-[10px]">Gunakan panel kendali kanan untuk merangkas dokumen.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card 3: Generated Quizzes Board -->
            <div class="bg-[color:var(--bg-card)] border border-[color:var(--border-color)] rounded-2xl p-6 space-y-4">
                <h2 class="text-md font-bold font-['Orbitron'] text-[color:var(--primary)] flex items-center space-x-2">
                    <span>🎯</span> <span>Kuis Pemahaman (Active Recall)</span>
                </h2>

                <div class="space-y-3">
                    <!-- For Milestone 2: Mock lists -->
                    <div class="p-4 bg-[color:var(--bg-main)] border border-[color:var(--border-color)] rounded-xl text-center text-xs text-[color:var(--text-muted)] py-6 space-y-2">
                        <p>"No quizzes generated yet for this notebook!"</p>
                        <p class="text-[10px]">Rocky akan membuat kuis berisikan 5-10 pilihan ganda.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Rocky AI workspace & Control Panels (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Controller Panel -->
            <div class="bg-[color:var(--bg-card)] border border-[color:var(--border-color)] rounded-2xl p-6 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h2 class="text-md font-bold font-['Orbitron'] text-[color:var(--primary)] flex items-center space-x-2">
                        <span>🤖</span> <span>Rocky Agent Workspace Control</span>
                    </h2>
                    
                    <!-- Style Toggle Switcher -->
                    <div class="flex bg-[color:var(--bg-main)] p-1 rounded-xl border border-[color:var(--border-color)] text-xs self-start sm:self-center">
                        <button class="px-3 py-1.5 rounded-lg bg-[color:var(--primary)] text-black font-bold">Default</button>
                        <button class="px-3 py-1.5 rounded-lg text-[color:var(--text-muted)] hover:text-[color:var(--text-main)] transition">Learning</button>
                        <button class="px-3 py-1.5 rounded-lg text-[color:var(--text-muted)] hover:text-[color:var(--text-main)] transition">Formal</button>
                    </div>
                </div>

                <!-- Generation Trigger Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <button class="py-3 px-4 rounded-xl border border-[color:var(--border-color)] bg-[color:var(--bg-main)] hover:border-[color:var(--primary)] hover:text-[color:var(--primary)] transition text-xs font-bold flex flex-col items-center justify-center space-y-2 {{ $note->pdf_extracted_text ? 'cursor-pointer' : 'cursor-not-allowed opacity-50' }}" {{ $note->pdf_extracted_text ? '' : 'disabled' }}>
                        <span class="text-xl">✍️</span>
                        <span>Buat Catatan</span>
                    </button>
                    
                    <button class="py-3 px-4 rounded-xl border border-[color:var(--border-color)] bg-[color:var(--bg-main)] hover:border-[color:var(--primary)] hover:text-[color:var(--primary)] transition text-xs font-bold flex flex-col items-center justify-center space-y-2 {{ $note->pdf_extracted_text ? 'cursor-pointer' : 'cursor-not-allowed opacity-50' }}" {{ $note->pdf_extracted_text ? '' : 'disabled' }}>
                        <span class="text-xl">📝</span>
                        <span>Ringkas Dokumen</span>
                    </button>

                    <button class="py-3 px-4 rounded-xl border border-[color:var(--border-color)] bg-[color:var(--bg-main)] hover:border-[color:var(--primary)] hover:text-[color:var(--primary)] transition text-xs font-bold flex flex-col items-center justify-center space-y-2 {{ $note->pdf_extracted_text ? 'cursor-pointer' : 'cursor-not-allowed opacity-50' }}" {{ $note->pdf_extracted_text ? '' : 'disabled' }}>
                        <span class="text-xl">🎯</span>
                        <span>Generate Quiz</span>
                    </button>
                </div>
                
                @if(!$note->pdf_extracted_text)
                    <p class="text-[10px] text-[color:var(--text-muted)] text-center italic">
                        *Harap unggah berkas PDF terlebih dahulu untuk mengaktifkan tombol kendali AI Rocky.
                    </p>
                @endif
            </div>

            <!-- Workspace Output/Display Area -->
            <div class="bg-[color:var(--bg-card)] border border-[color:var(--border-color)] rounded-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-[color:var(--border-color)] pb-3">
                    <h3 class="font-bold text-sm font-['Orbitron'] text-[color:var(--text-main)] flex items-center space-x-2">
                        <span>📖</span> <span>Catatan Belajar Rocky</span>
                    </h3>
                    <span class="text-[10px] text-green-400 font-mono">Status: Ready</span>
                </div>

                <!-- Scrollable Display Board -->
                <div class="p-6 bg-[color:var(--bg-main)] border border-[color:var(--border-color)] rounded-xl min-h-[350px] max-h-[500px] overflow-y-auto space-y-4 leading-relaxed text-sm">
                    <div class="flex flex-col items-center justify-center text-center py-20 space-y-4">
                        <span class="text-5xl animate-bounce">🕸️🕷️</span>
                        <div>
                            <p class="font-bold text-[color:var(--text-main)]">"Workspace is empty, friend!"</p>
                            <p class="text-xs text-[color:var(--text-muted)] max-w-xs mt-1 mx-auto">
                                Rocky belum menulis catatan di sini. Unggah berkas dokumen Anda dan klik tombol "Buat Catatan"!
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection

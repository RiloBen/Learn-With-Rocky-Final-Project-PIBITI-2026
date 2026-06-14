@extends('layouts.dashboard')

@section('title', 'Notebooks - Rocky Lab')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <div class="flex items-center justify-between border-b border-(--border-color) pb-4">
        <h1 class="text-3xl font-extrabold font-['Orbitron'] text-(--text-main)">
            Lab Notebooks
        </h1>
        <span class="text-sm text-(--text-muted) font-semibold">
            "Your notebooks, friend!"
        </span>
    </div>

    @if(session('status') === 'notebook-deleted')
        <div class="p-4 bg-red-950/30 border border-red-500/50 text-red-200 rounded-xl text-sm">
            "Rocky deleted notebook. Files destroyed! Apology!"
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- List of Notebooks -->
        <div class="lg:col-span-2 space-y-6">
            @if($notes->isEmpty())
                <div class="bg-(--bg-card) border border-(--border-color) rounded-2xl p-12 text-center flex flex-col items-center justify-center space-y-4 shadow-[0_0_15px_var(--border-glow)]">
                    <span class="text-6xl animate-bounce">🕸️</span>
                    <h2 class="text-xl font-bold font-['Orbitron'] text-(--text-main)">No Notebooks Yet</h2>
                    <p class="text-sm text-(--text-muted) max-w-sm">
                        "No notebooks, friend! Add study module first! Upload PDF! Question?"
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @foreach($notes as $note)
                        <div class="bg-(--bg-card) border border-(--border-color) rounded-2xl p-6 flex flex-col justify-between hover:border-(--primary) transition shadow-[0_4px_15px_rgba(0,0,0,0.2)]">
                            <div>
                                <span class="text-3xl">📓</span>
                                <h3 class="text-lg font-bold font-['Orbitron'] text-(--text-main) mt-3 truncate">
                                    {{ $note->title }}
                                </h3>
                                <p class="text-xs text-(--text-muted) mt-2 line-clamp-3 leading-relaxed">
                                    {{ $note->description }}
                                </p>
                            </div>
                            
                            <div class="mt-6 pt-4 border-t border-(--border-color) flex items-center justify-between gap-4">
                                <a href="{{ route('notes.show', $note) }}" class="grow text-center py-2 px-3 bg-(--bg-main) border border-(--border-color) text-(--primary) hover:bg-(--primary) hover:text-black transition rounded-xl font-bold text-xs">
                                    Open Workspace 
                                </a>
                                
                                <form action="{{ route('notes.destroy', $note) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Notebook along with all quiz & study note data inside?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 border border-red-500/20 text-red-400 hover:bg-red-500 hover:text-white rounded-xl transition cursor-pointer" title="Delete Notebook">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Creation Form Card -->
        <div class="bg-(--bg-card) border border-(--border-color) rounded-2xl p-6 space-y-4 shadow-[0_4px_20px_rgba(0,0,0,0.3)]">
            <h2 class="text-xl font-bold font-['Orbitron'] text-(--primary) flex items-center space-x-2">
                <span>➕</span> <span>Create Notebook</span>
            </h2>
            <p class="text-xs text-(--text-muted)">"Add new notebook, friend!"</p>

            @if($errors->any())
                <div class="p-3 bg-red-950/50 border border-red-500 text-red-200 text-xs rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('notes.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="title" class="block text-xs font-semibold mb-1 text-(--text-main)">Notebook Title</label>
                    <input type="text" name="title" id="title" required value="{{ old('title') }}" placeholder="Example: Quantum Physics"
                           class="w-full px-3 py-2 text-sm bg-(--bg-main) border border-(--border-color) rounded-xl focus:border-(--primary) focus:outline-none text-(--text-main) transition">
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold mb-1 text-(--text-main)">Description</label>
                    <textarea name="description" id="description" required rows="4" placeholder="Description of topic or chapter..."
                              class="w-full px-3 py-2 text-sm bg-(--bg-main) border border-(--border-color) rounded-xl focus:border-(--primary) focus:outline-none text-(--text-main) transition resize-none">{{ old('description') }}</textarea>
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl btn-semi-3d text-sm cursor-pointer">
                    Create New! Amaze!
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

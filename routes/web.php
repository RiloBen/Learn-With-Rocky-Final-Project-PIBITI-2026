<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // settings
    Route::get('/settings', [AuthController::class, 'showSettings'])->name('settings');
    Route::post('/settings/password', [AuthController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/theme', [AuthController::class, 'updateTheme'])->name('settings.theme');

    // notebooks
    Route::resource('notes', NoteController::class)->except(['create', 'edit', 'update']);
    Route::post('/notes/{note}/pdf', [NoteController::class, 'uploadPdf'])->name('notes.pdf.upload');
    
    // AI workspace actions
    Route::get('/notes/{note}/summary/stream', [NoteController::class, 'streamSummary'])->name('notes.summary.stream');
    Route::get('/notes/{note}/generated-note/stream', [NoteController::class, 'streamGeneratedNote'])->name('notes.generated-note.stream');
    Route::post('/notes/{note}/quiz', [NoteController::class, 'generateQuiz'])->name('notes.quiz.generate');
    Route::post('/notes/{note}/quizzes/{quiz}/submit', [NoteController::class, 'submitQuiz'])->name('notes.quizzes.submit');

    // dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

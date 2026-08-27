<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ListEventController;
use App\Http\Controllers\ScanController;

Route::get('/', function () {return view('home');})->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', function () {return view('forgot-password');})->name('forgot.password');

Route::get('/list-event', [ListEventController::class, 'listEvent'])->name('list.event')->middleware('auth');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')
    ->name('dashboard');

Route::get('/edit-profile', function () {return view('edit-profile');})->name('edit.profile');

Route::get('/profile', function () {return view('profile');})->name('profile')->middleware('auth');

Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update')->middleware('auth');

Route::get('/create-event', [EventController::class, 'create'])->name('create.event')->middleware('auth');
Route::post('/create-event', [EventController::class, 'store'])->name('store.event')->middleware('auth');

// Route::get('/results', function () {return view('event-result');})->name('event.result');
Route::get('/event-result', [ScanController::class, 'eventResult'])->name('event.result.show');

Route::get('/scan-candidate', function () {return view('scan-candidate');})->name('scan.candidate');

Route::get('/access-event', function () {return view('access-event');})->name('access.event');

Route::get('/rate-candidate', function () {return view('rate-candidate');})->name('rate.candidate');

// Admin event page
Route::get('/admin/event/{id}', [EventController::class, 'admin'])->name('admin.event');

// Candidate edit/update
Route::get('/candidate/{id}/edit', [CandidateController::class, 'edit'])->name('candidate.edit');
Route::post('/candidate/{id}/update', [CandidateController::class, 'update'])->name('candidate.update');

// Voting
Route::get('/event/{id}/vote', [VoteController::class, 'index'])->name('event.vote');
Route::post('/event/{id}/vote', [VoteController::class, 'submit'])->name('event.vote.submit');

// Event CRUD + toggle
Route::delete('/event/{id}', [EventController::class, 'destroy'])->name('event.delete');
Route::post('/event/{id}/update', [EventController::class, 'update'])->name('event.update');
Route::post('/event/{id}/toggle-status', [EventController::class, 'toggleStatus']);
Route::post('/event/{id}/toggle-voting', [EventController::class, 'toggleVoting']);

// QR Event (PNG via GD)
Route::get('/event/{id}/qr', [EventController::class, 'generateQrEvent']);
Route::get('/event/{id}/qr/download', [EventController::class, 'downloadQrEvent']);

// QR Candidate (PNG via GD)
Route::get('/candidate/{id}/qr', [EventController::class, 'generateQrCandidate']);

// Download all candidate QR as lanyard PDF
Route::get('/event/{id}/download-candidates-pdf', [EventController::class, 'downloadCandidatesPDF']);

// Scan QR Event
Route::get('/scan-event', [ScanController::class, 'scanEvent'])->name('scan.event')->middleware('auth');
Route::post('/scan-process', [ScanController::class, 'processScan'])->name('scan.process');

// Access Event (Private)
Route::get('/access-event', [ScanController::class, 'accessEvent'])->name('access.event');
Route::post('/access-verify', [ScanController::class, 'verifyAccessCode'])->name('access.verify');

// Show Event
Route::get('/event', [ScanController::class, 'showEvent'])->name('event.show');

Route::post('/scan/decode', [ScanController::class, 'decodeImage'])->name('scan.decode');

// Scan QR Kandidat
Route::get('/scan-candidate', [ScanController::class, 'scanCandidate'])->name('scan.candidate');
Route::post('/scan-candidate/process', [ScanController::class, 'processScanCandidate'])->name('scan.candidate.process');

// Penilaian kandidat
Route::get('/rate-candidate', [ScanController::class, 'rateCandidate'])->name('rate.candidate');
Route::post('/rate-candidate/submit', [ScanController::class, 'submitRating'])->name('rate.candidate.submit');

// Hasil (TOPSIS / jumlah vote)
Route::get('/event-result', [ScanController::class, 'eventResult'])->name('event.result.show');
    
<?php

use App\Http\Controllers\DestinationController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\TourTypeController;
use App\Http\Controllers\TravelStyleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/testimonials', [PageController::class, 'testimonials'])->name('testimonials');
Route::get('/terms-and-conditions', [PageController::class, 'legal'])->defaults('slug', 'terms')->name('terms');
Route::get('/privacy-policy', [PageController::class, 'legal'])->defaults('slug', 'privacy')->name('privacy');
Route::get('/photo-credits', [PageController::class, 'photoCredits'])->name('photo-credits');
Route::get('/thank-you', [PageController::class, 'thankYou'])->name('thank-you');

Route::get('/journal', [JournalController::class, 'index'])->name('journal.index');
Route::get('/journal/{post:slug}', [JournalController::class, 'show'])->name('journal.show');
Route::get('/destinations', [DestinationController::class, 'index'])->name('destinations.index');
Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::get('/packages/{package:slug}', [PackageController::class, 'show'])->name('packages.show');
Route::get('/austria-vacations', [TourTypeController::class, 'index'])->name('tours.index');
Route::redirect('/tours', '/austria-vacations', 301);
Route::get('/travel-styles', [TravelStyleController::class, 'index'])->name('styles.index');
Route::get('/tours/{tourType:slug}', [TourTypeController::class, 'show'])->name('tours.show');

Route::post('/enquiry', [EnquiryController::class, 'store'])->middleware('throttle:enquiries')->name('enquiry.store');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

require __DIR__.'/admin.php';

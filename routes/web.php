<?php

use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\TourTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/terms-and-conditions', [PageController::class, 'legal'])->defaults('slug', 'terms')->name('terms');
Route::get('/privacy-policy', [PageController::class, 'legal'])->defaults('slug', 'privacy')->name('privacy');
Route::get('/thank-you', [PageController::class, 'thankYou'])->name('thank-you');

Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::get('/packages/{package:slug}', [PackageController::class, 'show'])->name('packages.show');
Route::get('/tours/{tourType:slug}', [TourTypeController::class, 'show'])->name('tours.show');

Route::post('/enquiry', [EnquiryController::class, 'store'])->middleware('throttle:enquiries')->name('enquiry.store');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

require __DIR__.'/admin.php';

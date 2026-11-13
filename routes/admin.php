<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [Admin\AuthController::class, 'login'])->middleware('throttle:20,1')->name('login.submit');

    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        // Enquiries
        Route::get('enquiries/export', [Admin\EnquiryController::class, 'export'])->name('enquiries.export');
        Route::get('enquiries', [Admin\EnquiryController::class, 'index'])->name('enquiries.index');
        Route::get('enquiries/{enquiry}', [Admin\EnquiryController::class, 'show'])->name('enquiries.show');
        Route::put('enquiries/{enquiry}', [Admin\EnquiryController::class, 'update'])->name('enquiries.update');
        Route::delete('enquiries/{enquiry}', [Admin\EnquiryController::class, 'destroy'])->name('enquiries.destroy');
        Route::post('enquiries/{enquiry}/ghl-retry', [Admin\EnquiryController::class, 'retryGhl'])->name('enquiries.ghl-retry');

        // Simple CRUD lists (index/create/store/edit/update/destroy + move up/down)
        $crud = function (string $uri, string $controller) {
            Route::get($uri, [$controller, 'index'])->name("$uri.index");
            Route::get("$uri/create", [$controller, 'create'])->name("$uri.create");
            Route::post($uri, [$controller, 'store'])->name("$uri.store");
            Route::get("$uri/{id}/edit", [$controller, 'edit'])->whereNumber('id')->name("$uri.edit");
            Route::put("$uri/{id}", [$controller, 'update'])->whereNumber('id')->name("$uri.update");
            Route::delete("$uri/{id}", [$controller, 'destroy'])->whereNumber('id')->name("$uri.destroy");
            Route::post("$uri/{id}/move/{dir}", [$controller, 'move'])->whereNumber('id')->where('dir', 'up|down')->name("$uri.move");
        };
        $crud('packages', Admin\PackageController::class);
        $crud('tour-types', Admin\TourTypeController::class);
        $crud('destinations', Admin\DestinationController::class);
        $crud('testimonials', Admin\TestimonialController::class);
        $crud('faqs', Admin\FaqController::class);
        $crud('posts', Admin\PostController::class);
        $crud('users', Admin\UserController::class);
        $crud('quiz', Admin\QuizController::class);

        // Packages extras
        Route::post('packages/{id}/duplicate', [Admin\PackageController::class, 'duplicate'])->name('packages.duplicate');
        Route::post('packages/{id}/toggle/{what}', [Admin\PackageController::class, 'toggle'])->name('packages.toggle');
        Route::post('packages/{id}/images', [Admin\PackageController::class, 'addImage'])->name('packages.images.store');
        Route::put('package-images/{image}', [Admin\PackageController::class, 'updateImage'])->name('packages.images.update');
        Route::post('package-images/{image}/move/{dir}', [Admin\PackageController::class, 'moveImage'])->where('dir', 'up|down')->name('packages.images.move');
        Route::delete('package-images/{image}', [Admin\PackageController::class, 'deleteImage'])->name('packages.images.destroy');

        // Quiz options
        Route::get('quiz/{id}/options', [Admin\QuizController::class, 'options'])->name('quiz.options');
        Route::post('quiz/{id}/options/{optionId?}', [Admin\QuizController::class, 'saveOption'])->name('quiz.options.save');
        Route::delete('quiz/{id}/options/{optionId}', [Admin\QuizController::class, 'deleteOption'])->name('quiz.options.delete');
        Route::post('quiz/{id}/options/{optionId}/move/{dir}', [Admin\QuizController::class, 'moveOption'])->where('dir', 'up|down')->name('quiz.options.move');

        // Pages, settings, media
        Route::get('pages', [Admin\PageController::class, 'index'])->name('pages.index');
        Route::get('pages/{page}/edit', [Admin\PageController::class, 'edit'])->name('pages.edit');
        Route::put('pages/{page}', [Admin\PageController::class, 'update'])->name('pages.update');
        Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
        Route::get('media', [Admin\MediaController::class, 'index'])->name('media.index');
        Route::post('media', [Admin\MediaController::class, 'store'])->name('media.store');
        Route::delete('media/{media}', [Admin\MediaController::class, 'destroy'])->name('media.destroy');
    });
});

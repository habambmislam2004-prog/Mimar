<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\RegisterController;
use App\Http\Controllers\Web\BusinessAccountController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\ServiceController;
use App\Models\City;
use App\Models\EstimationType;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Admin\BusinessAccountAdminController as WebAdminBusinessAccountController;
use App\Http\Controllers\Web\Admin\CityAdminController;
use App\Http\Controllers\Web\Admin\ServiceAdminController as WebAdminServiceController;
use App\Http\Controllers\Web\Admin\ServiceReportAdminController;
use App\Http\Controllers\Web\FavoriteController;
use App\Http\Controllers\Web\RatingController;
use App\Http\Controllers\Web\ServiceReportController;

Route::get('/', function () {
    return view('public.welcome');
})->name('welcome');

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }

    return redirect()->back();
})->name('lang.switch');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/profile', function () {
        return view('public.profile');
    })->name('profile');

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    Route::get('/admin/business-accounts', [WebAdminBusinessAccountController::class, 'index'])
    ->name('admin.business-accounts.index');

Route::post('/admin/business-accounts', [WebAdminBusinessAccountController::class, 'approve'])
    ->name('admin.business-accounts.approve');

Route::post('/admin/business-accounts/{businessAccount}/reject', [WebAdminBusinessAccountController::class, 'reject'])
    ->name('admin.business-accounts.reject');
    Route::get('/admin/services', [WebAdminServiceController::class, 'index'])
    ->name('admin.services.index');

Route::post('/admin/services/{service}/approve', [WebAdminServiceController::class, 'approve'])
    ->name('admin.services.approve');
Route::get('/admin/cities', [CityAdminController::class, 'index'])->name('admin.cities.index');
Route::post('/admin/cities', [CityAdminController::class, 'store'])->name('admin.cities.store');
Route::put('/admin/cities/{city}', [CityAdminController::class, 'update'])->name('admin.cities.update');
Route::delete('/admin/cities/{city}', [CityAdminController::class, 'destroy'])->name('admin.cities.destroy');


Route::post('/admin/services/{service}/reject', [WebAdminServiceController::class, 'reject'])
    ->name('admin.services.reject');
Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
Route::post('/services/{service}/favorite', [FavoriteController::class, 'store'])->name('favorites.store');
Route::delete('/services/{service}/favorite', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
Route::post('/services/{service}/reports', [ServiceReportController::class, 'store'])->name('reports.store');

Route::get('/admin/reports', [ServiceReportAdminController::class, 'index'])->name('admin.reports.index');
Route::post('/admin/reports/{serviceReport}/resolve', [ServiceReportAdminController::class, 'resolve'])->name('admin.reports.resolve');


    Route::get('/business-account', [BusinessAccountController::class, 'index'])->name('business-account.index');
    Route::get('/business-account/create', [BusinessAccountController::class, 'create'])->name('business-account.create');
    Route::post('/business-account', [BusinessAccountController::class, 'store'])->name('business-account.store');
    Route::put('/business-account/{businessAccount}', [BusinessAccountController::class, 'update'])->name('business-account.update');
   Route::post('/orders/{order}/ratings', [RatingController::class, 'store'])->name('ratings.store');

    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
    Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

    Route::get('/estimations/create', function () {
        return view('public.estimations.create', [
            'cities' => City::query()->orderBy('name_ar')->get(),
            'types' => EstimationType::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    })->name('estimations.create');

   Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::post('/services/{service}/orders', [OrderController::class, 'store'])->name('orders.store');
Route::post('/orders/{order}/accept', [OrderController::class, 'accept'])->name('orders.accept');
Route::post('/orders/{order}/reject', [OrderController::class, 'reject'])->name('orders.reject');
Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::view('/chat', 'public.chat.index')->name('chat.index');
    Route::view('/notifications', 'public.notifications.index')->name('notifications.index');
    Route::view('/categories', 'public.categories.index')->name('categories.index');
});

Route::get('/categories-preview', function () {
    return view('public.categories.index');
})->name('categories.show.preview');
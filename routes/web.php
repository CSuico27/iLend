<?php

use App\Livewire\Auth\AccountVerification;
use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\HomePage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class)->name('user.home');

Route::middleware('guest')->group(function () {
    // AUTH
    Route::get('/register', RegisterPage::class)->name('register');
    Route::get('/login', LoginPage::class)->name('login');
    Route::get('/forgot', ForgotPasswordPage::class)->name('password.request');
    Route::get('/reset/{token}', ResetPasswordPage::class)->name('password.reset');
    Route::get('/account-verification/{user_id}', AccountVerification::class)->name('account.verify');
});

Route::middleware('auth')->group(function (){
    Route::get('/logout', function (){
        auth()->logout();
        return redirect('/');
    });

    // Route::get('/my-account', AccountPage::class)->name('client.account');
    // Route::get('/booking/create', Booking::class)->name('client.booking');
    // Route::get('/success', SuccessPage::class)->name('success');
    // Route::get('/cancel', CancelPage::class)->name('cancel');
    // Route::get('/invoice', ClientInvoicePage::class)->name('client.invoice');
    // Route::get('/calendar', CalendarView::class)->name('client.calendar');
});
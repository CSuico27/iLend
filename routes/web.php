<?php

use App\Livewire\Auth\AccountVerification;
use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\Client\MembershipPage;
use App\Livewire\Client\PortalPage;
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
    Route::get('/account/resend-verification', \App\Livewire\Auth\ResendVerificationPage::class)->name('account.resend-verification');
});

Route::middleware('auth')->group(function (){
    Route::get('/logout', function (){
        auth()->logout();
        return redirect('/');
    });

    Route::get('/membership', MembershipPage::class)->name('client.membership');
    Route::get('/portal', PortalPage::class)->name('client.portal');
});
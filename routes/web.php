<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Account\ServiceController;
use App\Http\Controllers\Account\SupportController;
use App\Http\Controllers\Account\BillingController;
use App\Http\Controllers\TeamInvitationController;
use Laravel\Jetstream\Jetstream;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('account')->middleware(['auth:sanctum',config('jetstream.auth_session'),'verified'])->group(function () {
    Route::get('/', function () { return view('account.index');})->name('account');
    Route::resource('support', SupportController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('billing', BillingController::class);
});

// Override Jetstream Team Invitation route
Route::group(['middleware' => config('jetstream.middleware', ['web'])], function () {

    $authMiddleware = config('jetstream.guard')
        ? 'auth:'.config('jetstream.guard')
        : 'auth';

    $authSessionMiddleware = config('jetstream.auth_session', false)
        ? config('jetstream.auth_session')
        : null;

    Route::group(['middleware' => array_values(array_filter([$authMiddleware, $authSessionMiddleware, 'verified']))], function () {
        // Teams...
        if (Jetstream::hasTeamFeatures()) {
            Route::get('/team-invitations/{invitation}', [TeamInvitationController::class, 'accept'])
                 ->middleware(['signed'])
                 ->name('team-invitations.accept');
        }
    });
});



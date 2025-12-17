<?php

use Illuminate\Support\Facades\Route;
use Tapsilat\Laravel\Http\Controllers\WebhookController;

Route::post('/tapsilat/webhook', [WebhookController::class, 'handle'])
    ->name('tapsilat.webhook')
    ->withoutMiddleware(['web', 'csrf']);

<?php

use App\Http\Controllers\Api\Chat\GetConversationController;
use App\Http\Controllers\Api\Chat\GetMessageController;
use App\Http\Controllers\Api\Chat\SendMessageController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::prefix('v1')->group(function () {
        Route::controller(SendMessageController::class)->group(function () {
            Route::post('/message/send', 'sendMessage');
        });

        Route::controller(GetConversationController::class)->group(function () {
            Route::get('/conversations', 'getConversations');
        });

        Route::controller(GetMessageController::class)->group(function () {
            Route::get('/chat/messages', 'getChat');
        });

        // Route::controller(ChatMessageController::class)->group(function () {
        //     Route::get('/conversations', 'conversations');
        //     Route::post('/message/send', 'sendMessage');
        //     Route::get('/chat/messages', 'getChat');
        //     Route::post('/message/react/{id}', 'messageReact');
        // });
    });
});

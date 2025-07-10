<?php

use App\Http\Controllers\Api\Chat\DeleteMessageController;
use App\Http\Controllers\Api\Chat\GetConversationController;
use App\Http\Controllers\Api\Chat\GetMessageController;
use App\Http\Controllers\Api\Chat\SendMessageController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::prefix('v1')->group(function () {
        Route::post('/message/send', SendMessageController::class);
        Route::get('/conversations', GetConversationController::class);
        Route::get('/chat/messages', GetMessageController::class);
        Route::delete('/chat/message/{id}/delete', DeleteMessageController::class);

        // Route::controller(ChatMessageController::class)->group(function () {
        //     Route::get('/conversations', 'conversations');
        //     Route::post('/message/send', 'sendMessage');
        //     Route::get('/chat/messages', 'getChat');
        //     Route::post('/message/react/{id}', 'messageReact');
        // });
    });
});

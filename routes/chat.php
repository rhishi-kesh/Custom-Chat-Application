<?php

use App\Http\Controllers\Api\Chat\CreateGroupController;
use App\Http\Controllers\Api\Chat\DeleteMessageController;
use App\Http\Controllers\Api\Chat\GetConversationController;
use App\Http\Controllers\Api\Chat\GetMessageController;
use App\Http\Controllers\Api\Chat\GroupInfoController;
use App\Http\Controllers\Api\Chat\GroupMediaController;
use App\Http\Controllers\Api\Chat\GroupParticipantController;
use App\Http\Controllers\Api\Chat\GroupSettingsController;
use App\Http\Controllers\Api\Chat\SendMessageController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::prefix('v1')->group(function () {
        Route::post('/message/send', SendMessageController::class);
        Route::get('/conversations', GetConversationController::class);
        Route::get('/chat/messages', GetMessageController::class);
        Route::delete('/chat/message/{id}/delete', DeleteMessageController::class);

        Route::prefix('group')->group(function () {
            Route::post('/create', CreateGroupController::class);
            Route::get('/{id}/info', GroupInfoController::class);
            Route::get('/{id}/media', GroupMediaController::class);
            Route::get('/{id}/participants', GroupParticipantController::class);
            Route::post('/{id}/settings', GroupSettingsController::class);
        });

        //     Route::get('/conversations', 'conversations');
        //     Route::post('/message/send', 'sendMessage');
        //     Route::get('/chat/messages', 'getChat');
        //     Route::post('/message/react/{id}', 'messageReact');
    });
});

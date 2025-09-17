<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\MessageReactionController;
use App\Http\Controllers\EmojiController;


//Bez přihlášení
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);


//S přihlášením
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);

    //Uživatelé
    Route::get('/users', [UserController::class, 'index']);

    //Konverzace
    Route::apiResource(('conversations'), ConversationController::class);


    //Zprávy

    Route::prefix('conversations/{conversation}')->group(function () {

        //Zobrazení zpráv v konverzaci
        Route::get('messages', [MessageController::class, 'index']);

        //Odeslání zprávy do konverzace
        Route::post('messages', [MessageController::class, 'store']);
    });




    Route::prefix('/messages/{message}')->group(function () {


        //Zobrazit přílohy zprávy
        Route::get('attachments', [AttachmentController::class, 'index']);

        //Přidat reakci
        Route::post('reactions', [MessageReactionController::class, 'store']);

        //Odpovědět na zprávu
        Route::post('reply', [MessageController::class, 'reply']);

    });

    //Vytvořit přílohu před odesláním zprávy
    Route::post('/attachments', [AttachmentController::class, 'store']);

    //Smazat přílohu
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy']);

    //Smazat reakci
    Route::delete('/messages/{message}/reactions/{emoji}', [MessageReactionController::class, 'destroy']);





});


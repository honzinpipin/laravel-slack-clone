<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\MessageReactionController;
use App\Http\Controllers\EmojiController;


    //Bez přihlášení
    Route::post('/register' , [UserController::class, 'register']);
    Route::post('/login' , [UserController::class, 'login']);


//S přihlášením
Route::middleware('auth:api')->group(function() {
   Route::post('/logout', [UserController::class, 'logout']); 

    //Uživatelé
   Route::get('/users', [UserController::class, 'index']); 

   //Konverzace
        //moje konverzace
    Route::get('/conversations', [ConversationController::class, 'index']);
        //založení konverzace
    Route::post('/conversations', [ConversationController::class, 'store']);
        //Odstranění konverzace
    Route::delete('/conversations/{conversation}', [ConversationController::class, 'destroy']);
        //Zobrazení konverzace
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show']);
        //Změna názvu konverzace
    Route::patch('/conversations/{conversation}/name', [ConversationController::class, 'updateName']);


    //Zprávy
        //Zobrazení zpráv v konverzaci
    Route::get('/conversations/{conversation}/messages', [MessageController::class, 'index']);
        //Odeslání zprávy do konverzace
        Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store']); 
        //Odpověď na zprávu
        Route::post('/messages/{message}/reply', [MessageController::class, 'reply']);

    //Attachments
        //Přidat přílohu
    Route::post('/messages/{message}/attachments', [AttachmentController::class, 'store']);
        //Smazat přílohu
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy']);
        //Zobrazit přílohy zprávy
    Route::get('/messages/{message}/attachments', [AttachmentController::class, 'index']);

    //Reakce
        //přidat reakci
    Route::post('/messages/{message}/reactions', [MessageReactionController::class, 'store']);
        //Smazat reakci
    Route::delete('/messages/{message}/reactions/{emoji}', [MessageReactionController::class, 'destroy']);

    



});


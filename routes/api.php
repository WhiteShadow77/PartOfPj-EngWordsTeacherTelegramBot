<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Api\PronController;
use App\Http\Controllers\FdbController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/messages/receive', [MessageController::class, 'receive'])
    ->middleware('auth.telegram-requests');

Route::post('/webhook', [WebhookController::class, 'setWebhook']);

Route::put('/guest/language', [GuestController::class, 'setLanguage'])->name('api.guest.set-language');

Route::group(['middleware' => ['auth']], function () {
    Route::put('/users/english-words/portion', [UserController::class, 'updateEnglishWordsPortion'])
        ->name('api.users.english-words.portion.update');

    Route::put('/users/english-words/is-enabled-sending', [UserController::class, 'updateIsEnabledEngWordsSending'])
        ->name('api.users.english-words.is-enabled-sending.update');

    Route::put('/users/quizes/is-enabled-sending', [UserController::class, 'updateIsEnabledQuizSending'])
        ->name('api.users.quizes.is-enabled-sending.update');

    Route::get('/users/english-words/schedule', [UserController::class, 'getEngWordsSendingSchedule'])
        ->name('api.users.english-words.sending-schedule');

    Route::get('/users/quizes', [UserController::class, 'getQuizSendingSchedule'])
        ->name('api.users.quizes');

    Route::patch('/users/english-words/schedule', [UserController::class, 'updateEngWordsSendingSchedule'])
        ->name('api.users.english-words.sending-schedule.update');

    Route::patch('/users/quizes/schedule', [UserController::class, 'updateQuizSendingSchedule'])
        ->name('api.users.quizes.sending-schedule.update');
    //Route::post('/login', [UserController::class, 'loginUser']);

    Route::put('/users/quizes/max-answers-quantity', [UserController::class, 'updateQuizMaxAnswersQuantity'])
        ->name('api.users.quizes.max-answers-quantity.update');

    Route::get('/users/quizes/is-enabled', [UserController::class, 'getQuizIsEnabled'])
        ->name('api.users.quiz.is-enabled');

    Route::get('/users/english-words/is-enabled', [UserController::class, 'getEnglishWordsIsEnabled'])
        ->name('api.users.english-words.is-enabled');

    Route::get(
        '/users/quizes/is-enabled-repeat-already-known', [UserController::class, 'getQuizIsEnabledRepeatAlreadyKnown']
    )->name('api.users.quiz.is-enabled-repeat-already-known');

    Route::get(
        '/users/quizes/repeat-already-known-percents', [UserController::class, 'getQuizRepeatAlreadyKnownPercents']
    )->name('api.users.quiz.repeat-already-known-percents');

    Route::put('/users/quizes/is-enabled-repeat-known', [UserController::class, 'updateIsEnabledRepeatKnownInQuiz'])
        ->name('api.users.quizes.is-enabled-repeat-known.update');

    Route::put('/users/quizes/repeat-known-words-percent', [UserController::class, 'updateRepeatKnownWordsPercentsInQuiz'])
        ->name('api.users.quizes.repeat-known-words-percent.update');

    Route::get('/users/statistics', [UserController::class, 'getStatistics'])
        ->name('api.users.statistics');

    Route::get(
        '/users/history-delete-period-params/{monthsQuantity}', [UserController::class, 'getHistoryDeletePeriodParams']
    )->name('api.users.history-delete-period-params');

    Route::put('/users/languages', [UserController::class, 'updateLanguage'])
        ->name('api.users.language.update');
});

Route::group(['middleware' => [
    'auth:sanctum',
    'role:admin',
]], function () {
    Route::post('/cv', [CvController::class, 'updateCv']);
    Route::delete('/cv', [CvController::class, 'deleteCv']);

    Route::post('/gallery', [GalleryController::class, 'addItem'])
        ->name('gallery.item-add');
    Route::post('/gallery/{position_number}', [GalleryController::class, 'updateItemByPositionNumber'])
        ->name('gallery.item-update-by-position-number');
    Route::put('/gallery/{position_number}', [GalleryController::class, 'clearItemByPositionNumber'])
        ->name('gallery.item-clear-by-position-number');
    Route::delete('/gallery/{position_number}', [GalleryController::class, 'deleteItemByPositionNumber'])
        ->name('gallery.item-delete-by-position-number');

    Route::post('/logs/twitch-is-enabled-write', [LogController::class, 'twitchIsEnabledWriteLog']);
    Route::post('/logs/twitch-is-enabled-send', [LogController::class, 'twitchIsEnabledSendLog']);
    Route::get('/logs/config', [LogController::class, 'getLogsConfig']);
    Route::get('/logs/link', [LogController::class, 'getLogTextAccessLink']);
    Route::put('/logs/clear', [LogController::class, 'clearLogFile']);

    Route::get('/prons/link', [PronController::class, 'getPronArchiveTemporaryLink']);
    Route::get('/fdb', [FdbController::class, 'getFdbTemporaryLink']);
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
});

Route::post('/login', [ApiAuthController::class, 'login']);


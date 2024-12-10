<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\CodeExplorerController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\DashboardAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\DbDiagramController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Api\PronController;
use App\Http\Controllers\FdbController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [GuestController::class, 'getMainPage'])->name('main.page');

Route::get('/login', [AuthController::class, 'getLoginPage'])->name('login.page');
Route::get('/unknown-user', [AuthController::class, 'getUnknownUserPage'])->name('unknown-user.page');

Route::get('/config', [ConfigController::class, 'getConfigTemplate'])->name('config')
    ->middleware('auth');

Route::get('/history', [HistoryController::class, 'getHistoryTemplate'])->name('history')
    ->middleware('auth');
Route::delete('/history', [HistoryController::class, 'deleteHistory'])->name('history.delete')
    ->middleware('auth');

Route::get('/statistics', [StatisticsController::class, 'getStatisticsTemplate'])->name('statistics')
    ->middleware('auth');

Route::get('/comments', [CommentController::class, 'getCommentTemplate'])->name('comment')
    ->middleware('auth');
Route::post('/comments', [CommentController::class, 'postComment'])->name('comment.post')
    ->middleware('auth');
Route::put('/comments/{id}', [CommentController::class, 'editComment'])->name('comment.edit')
    ->middleware('auth');
Route::delete('/comments/{id}', [CommentController::class, 'deleteComment'])->name('comment.delete')
    ->middleware('auth');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/words/known', [HomeController::class, 'getAllKnownWords'])->middleware('auth');

Route::get('/code', [CodeExplorerController::class, 'getRootDirPage'])->name('code')
    ->middleware('code_explorer');
Route::get('/code/{path}', [CodeExplorerController::class, 'getDirOrFilePage'])->name('code.common')
    ->where('path', '.*')->middleware('code_explorer');

Route::get('/db/diagram', [DbDiagramController::class, 'getDbDiagramView'])->name('db.diagram');
Route::get('/db-diagram-image', [DbDiagramController::class, 'getDbDiagram'])->name('db.diagram-image');

Route::get('/dashboard', [DashboardController::class, 'getDashboardTemplate'])->name('dashboard');
Route::get('/dashboard/login', [DashboardAuthController::class, 'getLoginTemplate'])->name('dashboard.login-page');
Route::post('/dashboard/login', [DashboardAuthController::class, 'login'])->name('dashboard.login');
Route::get('/dashboard/logout', [DashboardAuthController::class, 'logout'])->name('dashboard.logout');

Route::get('/high-light-files/{path?}', [CodeExplorerController::class, 'getHighLightedFileForExplorerPage'])
    ->name('high-light-files')->where('path', '.*')->middleware('code_explorer');

Route::get('/cv', [CvController::class, 'getCv'])->name('cv.download');

Route::get('/gallery', [GalleryController::class, 'getPage'])->name('gallery.page');

Route::post('/feedback', [FeedbackController::class, 'receive'])->name('feedback.handle');
Route::get('/feedback', [FeedbackController::class, 'getWriteFeedbackPage'])->name('feedback.write-feedback-page');

Route::get('/menu', [MenuController::class, 'getPage'])->name('menu.page');
Route::get('/stack', [MenuController::class, 'getStackPage'])->name('stack.page');

Route::get('/prons/download', [PronController::class, 'getPronArchive'])->name('pron-archive-link');
Route::get('/fdb', [FdbController::class, 'getFdbResponse'])->name('fdb-link');
Route::get('/logs', [LogController::class, 'getLogText'])->name('log-text-link');

Route::get('/test', [TestController::class, 'test']);

Route::fallback(function (){
    return view('not-found-view');
});


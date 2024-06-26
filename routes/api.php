<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\BackgroundController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StoryController;
use App\Http\Middleware\AdminMiddleware;

Route::group([
    'middleware' => 'api',
    'prefix' => 'user',
], function () {
    Route::get('me', [UserController::class, 'me']);
    Route::patch('update-password', [UserController::class, 'updatePassword']);
    Route::patch('update-information', [UserController::class, 'updateInformation']);
    Route::post('update-avatar', [UserController::class, 'updateAvatar']);
    Route::post('update-cover-image', [UserController::class, 'updateCoverImage']);
    Route::delete('delete-avatar', [UserController::class, 'deleteAvatar']);
    Route::delete('delete-cover-image', [UserController::class, 'deleteCoverImage']);
    Route::get('/profile/{id}', [UserController::class, 'getProfile']);
    Route::get('/find', [UserController::class, 'find']);
    Route::get('/get-suggestion-list', [UserController::class, 'getSuggestionList']);

});


Route::group([
    'middleware' => ['api', AdminMiddleware::class],
    'prefix' => 'admin',
], function () {
    Route::get('users', [AdminUserController::class, 'index']);
    Route::get('users/{id}', [AdminUserController::class, 'show']);
    Route::patch('users/{id}/lock', [AdminUserController::class, 'lock']);
    Route::patch('users/{id}/unlock', [AdminUserController::class, 'unlock']);

    Route::group([
        'prefix' => 'reports',
    ], function () {
        Route::get('/', [ReportController::class, 'index']);
        Route::put('/approve/{id}', [ReportController::class, 'approve']);
        Route::put('/reject/{id}', [ReportController::class, 'reject']);
        Route::delete('/delete/{id}', [ReportController::class, 'destroy']);
    });
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',
], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

Route::group([
    'middleware' => ['api'],
    'prefix' => 'backgrounds'
], function () {
    Route::get('/', [BackgroundController::class, 'index']);
    Route::post('/', [BackgroundController::class, 'store']);
    Route::get('/{id}', [BackgroundController::class, 'show']);
    Route::put('/{id}', [BackgroundController::class, 'update']);
    Route::put('/toggle-visibility/{id}', [BackgroundController::class, 'toggleVisibility']);
    Route::delete('/{id}', [BackgroundController::class, 'destroy']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'friendship',
], function () {
    Route::get('user/{user_id}', [FriendshipController::class, 'getFriendListOfUser']);
    Route::post('add/{friend}', [FriendshipController::class, 'add']);
    Route::patch('accept/{id}', [FriendshipController::class, 'accept']);
    Route::delete('{id}', [FriendshipController::class, 'delete']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'posts',
], function () {
    Route::get('/', [PostController::class, 'index']);
    Route::post('/', [PostController::class, 'store']);
    Route::get('/{id}', [PostController::class, 'show']);
    Route::put('/{id}', [PostController::class, 'update']);
    Route::delete('/{id}', [PostController::class, 'destroy']);

    Route::get('user/{id}', [PostController::class, 'getUserPosts']);

    Route::get('reaction-detail/{postId}', [PostController::class, 'getReactionsDetail']);
    Route::get('user-reaction/{postId}', [PostController::class, 'getUserReaction']);
    Route::get('top-reactions/{postId}', [PostController::class, 'getTopReactions']);
    Route::post('reaction/{postId}', [PostController::class, 'addOrUpdateReaction']);
    Route::delete('reaction/{id}', [PostController::class, 'removeReaction']);

    Route::get('/comments/{postId}', [PostController::class, 'getAllComments']);
    Route::get('/top-comments/{postId}', [PostController::class, 'getTopComments']);
    Route::post('/comments/{commentId}', [PostController::class, 'storeComment']);
    Route::put('/comments/{postId}/{commentId}', [PostController::class, 'updateComment']);
    Route::delete('/comments/{postId}/{commentId}', [PostController::class, 'deleteComment']);

    Route::post('/share/{postId}', [PostController::class, 'sharePost']);

});

Route::group([
    'middleware' => 'api',
    'prefix' => 'archived',
], function () {
    Route::get('/', [PostController::class, 'getArchivedPosts']);
    Route::post('/{id}', [PostController::class, 'saveToArchive']);
    Route::delete('/{id}', [PostController::class, 'removeFromArchive']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'search',
], function () {
    Route::get('/posts', [PostController::class, 'searchPost']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'chat',
], function () {
    Route::post('create-conversation', [ChatController::class, 'createConversation']);
    Route::post('message', [ChatController::class, 'createMessage']);
    Route::get('my-conversations', [ChatController::class, 'getMyConversations']);
    Route::get('get-secret-key/{conversation_id}', [ChatController::class, 'getSecretKey']);
    Route::get('messages/{conversation_id}', [ChatController::class, 'getMessagesByConversationId']);
    Route::get('conversation_participants', [ChatController::class, 'getConversationParticipants']);
    Route::post('message/mark-is-read/{message_id}', [ChatController::class, 'markIsRead']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'stories',
], function () {
    Route::get('/', [StoryController::class, 'index']);
    Route::post('/', [StoryController::class, 'create']);
    Route::patch('/update/{id}', [StoryController::class, 'update']);
    Route::get('/{id}', [StoryController::class, 'show']);
    Route::delete('/{id}', [StoryController::class, 'delete']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'notifications',
], function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::put('/read/{id}', [NotificationController::class, 'markAsRead']);

});

Route::group([
    'middleware' => 'api',
], function () {
    Route::get('/get-secret-key', [NotificationController::class, 'getSecretKey']);
});


Route::group([
    'middleware' => 'api',
    'prefix' => 'reports',
], function () {
    Route::get('/', [ReportController::class, 'index']);
    Route::post('/', [ReportController::class, 'store']);
});

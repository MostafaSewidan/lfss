<?php

use Illuminate\Support\Facades\Route;


Route::get('categories', 'MainController@list_categories');
Route::get('governorates', 'MainController@list_governorates');
Route::get('cities', 'MainController@list_cities');
Route::get('losts', 'MainController@listLosts');
Route::get('home', 'MainController@home');
Route::get('show-losts', 'MainController@showLosts');

Route::post('register' , 'AuthForTestController@register');
//auth cycle
Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::post('send-pin-code', 'AuthController@sendPinCode');
Route::post('reset-password', 'AuthController@resetPassword');
Route::post('active-account', 'AuthController@activeAccount');


Route::group(['middleware' => ['auth:client', 'client-active']], function () {

    /// api auth routes in auth
    Route::get('show-profile', 'AuthController@showProfile');
    Route::post('update-profile', 'AuthController@updateProfile');
    Route::post('logout', 'AuthController@logout');

    Route::get('my-ads', 'MainController@myAds');
    Route::post('ads/add-new', 'MainController@adsAddNew');


    ////////////////////////////////////////////
    ///
    // notifications

    Route::get('notifications', 'MainController@notifications');
    Route::get('notifications/unread-count', 'MainController@unReadNotificationCount');
    Route::post('notifications/delete', 'MainController@deleteNotification');
    Route::post('notifications/read', 'MainController@readNotification');

    ////////////////////////////////
});



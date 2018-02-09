<?php

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

Route::group(['prefix' => 'secure'], function () {
    Route::get('bootstrap-data', 'BootstrapController@getBootstrapData');
    Route::get('update', 'UpdateController@show');
    Route::post('update/run', 'UpdateController@update');

    //AUTH ROUTES
    Route::post('auth/register', 'Auth\RegisterController@register');
    Route::post('auth/login', 'Auth\LoginController@login');
    Route::post('auth/logout', 'Auth\LoginController@logout');
    Route::post('auth/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('auth/password/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');
    Route::get('auth/email/confirm/{code}', 'Auth\ConfirmEmailController@confirm');

    //SOCIAL AUTHENTICATION
    Route::get('auth/social/{provider}/connect', 'Auth\SocialAuthController@connect');
    Route::get('auth/social/{provider}/login', 'Auth\SocialAuthController@login');
    Route::get('auth/social/{provider}/callback', 'Auth\SocialAuthController@loginCallback');
    Route::post('auth/social/extra-credentials', 'Auth\SocialAuthController@extraCredentials');
    Route::post('auth/social/{provider}/disconnect', 'Auth\SocialAuthController@disconnect');

    //search
    Route::get('search/audio/{artist}/{track}', 'SearchController@searchAudio');
    Route::get('search/{query}', 'SearchController@search');

    //albums
    Route::get('albums', 'AlbumController@index');
    Route::post('albums', 'AlbumController@store');
    Route::put('albums/{id}', 'AlbumController@update');
    Route::delete('albums', 'AlbumController@destroy');
    Route::get('albums/popular', 'PopularAlbumsController@index');
    Route::get('albums/new-releases', 'NewReleasesController@index');
    Route::get('albums/{id}', 'AlbumController@show');

    //artists
    Route::get('artists', 'ArtistController@index');
    Route::post('artists', 'ArtistController@store');
    Route::put('artists/{id}', 'ArtistController@update');
    Route::get('artists/{nameOrId}', 'ArtistController@show');
    Route::get('artists/{id}/albums', 'ArtistAlbumsController@index');
    Route::delete('artists', 'ArtistController@destroy');
    
    //tracks
    Route::get('tracks', 'TrackController@index');
    Route::post('tracks', 'TrackController@store');
    Route::put('tracks/{id}', 'TrackController@update');
    Route::get('tracks/top', 'TopTracksController@index');
    Route::get('tracks/{id}', 'TrackController@show');
    Route::delete('tracks', 'TrackController@destroy');
    Route::post('tracks/{id}/plays/increment', 'TrackPlaysController@increment');

    //LYRICS
    Route::get('lyrics', 'LyricsController@index');
    Route::post('lyrics', 'LyricsController@store');
    Route::delete('lyrics', 'LyricsController@destroy');
    Route::get('tracks/{id}/lyrics', 'LyricsController@show');
    Route::put('lyrics/{id}', 'LyricsController@update');

    //RADIO
    Route::get('radio/artist/{id}', 'ArtistRadioController@getRecommendations');
    Route::get('radio/track/{id}', 'TrackRadioController@getRecommendations');

    //GENRES
    Route::get('genres/popular', 'PopularGenresController@index');
    Route::get('genres/{name}/artists', 'GenreArtistsController@index');

    //USER LIBRARY
    Route::post('user/library/tracks/add', 'UserLibrary\UserLibraryTracksController@add');
    Route::post('user/library/tracks/remove', 'UserLibrary\UserLibraryTracksController@remove');
    Route::get('user/library/tracks', 'UserLibrary\UserLibraryTracksController@index');
    Route::get('user/library/albums', 'UserLibrary\UserLibraryAlbumsController@index');
    Route::get('user/library/artists', 'UserLibrary\UserLibraryArtistsController@index');

    //PLAYLISTS
    Route::get('playlists/{id}', 'PlaylistController@show');
    Route::get('playlists', 'PlaylistController@index');
    Route::get('user/{id}/playlists', 'UserPlaylistsController@index');
    Route::put('playlists/{id}', 'PlaylistController@update');
    Route::post('playlists', 'PlaylistController@store');
    Route::delete('playlists', 'PlaylistController@destroy');
    Route::post('playlists/{id}/follow', 'UserPlaylistsController@follow');
    Route::post('playlists/{id}/unfollow', 'UserPlaylistsController@unfollow');
    Route::get('playlists/{id}/tracks', 'PlaylistTracksController@index');
    Route::post('playlists/{id}/tracks/add', 'PlaylistTracksController@add');
    Route::post('playlists/{id}/tracks/remove', 'PlaylistTracksController@remove');
    Route::post('playlists/{id}/tracks/order', 'PlaylistTracksOrderController@change');

    //USERS
    Route::get('users', 'UsersController@index');
    Route::get('users/{id}', 'UsersController@show');
    Route::post('users', 'UsersController@store');
    Route::put('users/{id}', 'UsersController@update');
    Route::delete('users/delete-multiple', 'UsersController@deleteMultiple');
    Route::post('users/{id}/follow', 'UsersController@follow');
    Route::post('users/{id}/unfollow', 'UsersController@unfollow');

    //USER PASSWORD
    Route::post('users/{id}/password/change', 'Auth\ChangePasswordController@change');

    //USER AVATAR
    Route::post('users/{userId}/avatar', 'UserAvatarController@store');
    Route::delete('users/{userId}/avatar', 'UserAvatarController@destroy');

    //USER GROUPS
    Route::post('users/{id}/groups/attach', 'UserGroupsController@attach');
    Route::post('users/{id}/groups/detach', 'UserGroupsController@detach');

    //USER PERMISSIONS
    Route::post('users/{id}/permissions/add', 'UserPermissionsController@add');
    Route::post('users/{id}/permissions/remove', 'UserPermissionsController@remove');

    //GROUPS
    Route::get('groups', 'GroupsController@index');
    Route::post('groups', 'GroupsController@store');
    Route::put('groups/{id}', 'GroupsController@update');
    Route::delete('groups/{id}', 'GroupsController@destroy');
    Route::post('groups/{id}/add-users', 'GroupsController@addUsers');
    Route::post('groups/{id}/remove-users', 'GroupsController@removeUsers');

    //PAGES
    Route::get('pages', 'PagesController@index');
    Route::get('pages/{id}', 'PagesController@show');
    Route::post('pages', 'PagesController@store');
    Route::put('pages/{id}', 'PagesController@update');
    Route::delete('pages', 'PagesController@destroy');

    //EMAIL
    Route::post('media-items/links/send', 'EmailMediaItemLinksController@send');

    //STATIC UPLOADS
    Route::post('images/static/upload', 'StaticFilesUploadController@images');
    Route::post('videos/static/upload', 'StaticFilesUploadController@videos');

    //UPLOADS
    Route::get('uploads', 'UploadsController@index');
    Route::get('uploads/{id}', 'UploadsController@show');
    Route::get('uploads/{id}/download', 'FileDownloadController@download');
    Route::post('uploads', 'UploadsController@store');
    Route::delete('uploads', 'UploadsController@destroy');

    //VALUE LISTS
    Route::get('value-lists/{name}', 'ValueListsController@getValueList');

    //SETTINGS
    Route::get('settings', 'SettingsController@index');
    Route::post('settings', 'SettingsController@persist');

    //ADMIN
    //Route::get('admin/error-log', 'AdminController@getErrorLog');
    Route::post('admin/appearance', 'AppearanceController@save');
    Route::get('admin/appearance/values', 'AppearanceController@getValues');
    Route::get('admin/analytics/stats', 'AnalyticsController@stats');
    Route::post('admin/sitemap/generate', 'SitemapController@generate');
    Route::post('cache/clear', 'CacheController@clear');

    //LOCALIZATIONS
    Route::get('localizations', 'LocalizationsController@index');
    Route::post('localizations', 'LocalizationsController@store');
    Route::put('localizations/{id}', 'LocalizationsController@update');
    Route::delete('localizations/{id}', 'LocalizationsController@destroy');
    Route::get('localizations/{name}', 'LocalizationsController@show');

    //MAIL TEMPLATES
    Route::get('mail-templates', 'MailTemplatesController@index');
    Route::post('mail-templates/render', 'MailTemplatesController@render');
    Route::post('mail-templates/{id}/restore-default', 'MailTemplatesController@restoreDefault');
    Route::put('mail-templates/{id}', 'MailTemplatesController@update');
});

//FRONT-END ROUTES THAT NEED TO BE PRE-RENDERED
Route::get('/', 'HomeController@index')->middleware('prerenderIfCrawler:home');
Route::get('artist/{name}', 'HomeController@index')->middleware('prerenderIfCrawler:artist');
Route::get('artist/{id}/{name}', 'HomeController@index')->middleware('prerenderIfCrawler:artist');
Route::get('album/{albumId}/{artistId}/{albumName}', 'HomeController@index')->middleware('prerenderIfCrawler:album');
Route::get('track/{id}', 'HomeController@index')->middleware('prerenderIfCrawler:track');
Route::get('track/{id}/{name}', 'HomeController@index')->middleware('prerenderIfCrawler:track');
Route::get('playlist/{id}', 'HomeController@index')->middleware('prerenderIfCrawler:playlist');
Route::get('playlist/{id}/{name}', 'HomeController@index')->middleware('prerenderIfCrawler:playlist');
Route::get('user/{id}', 'HomeController@index')->middleware('prerenderIfCrawler:user');
Route::get('user/{id}/{name}', 'HomeController@index')->middleware('prerenderIfCrawler:user');
Route::get('genre/{name}', 'HomeController@index')->middleware('prerenderIfCrawler:genre');
Route::get('new-releases', 'HomeController@index')->middleware('prerenderIfCrawler:new-releases');
Route::get('popular-genres', 'HomeController@index')->middleware('prerenderIfCrawler:popular-genres');
Route::get('popular-albums', 'HomeController@index')->middleware('prerenderIfCrawler:popular-albums');
Route::get('top-50', 'HomeController@index')->middleware('prerenderIfCrawler:top-50');
Route::get('search/{query}', 'HomeController@index')->middleware('prerenderIfCrawler:search');
Route::get('search/{query}/{tab}', 'HomeController@index')->middleware('prerenderIfCrawler:search');

//CACHE ALL ROUTES AND REDIRECT TO HOME
Route::get('{all}', 'HomeController@index')->where('all', '.*');

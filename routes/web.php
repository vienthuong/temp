<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->get('/', function () {
    return file_get_contents(base_path().'/index.html');
});
$router->get('ask', [
    'as' => 'bot', 'uses' => 'BotController@index'
]);
$router->get('test', function() {
    return 'This is a Test';
});
$router->post('challenge', [
    'as' => 'bot_send_message', 'uses' => 'BotController@postMessage'
]);


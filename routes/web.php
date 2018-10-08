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
$router->post('challenge', function (Illuminate\Http\Request $request) {
	info($request->all());
	if(empty($request->post('event')['bot_id']) || $request->post('event')['bot_id'] !== 'BDAENJ614') {
	$url = 'https://slack.com/api/chat.postMessage';
	    $ch = curl_init();
	    $postdata = [];
	        $postdata['channel'] = 'CDAEGEG14';
	        $postdata['text'] = 'hello';
		    curl_setopt($ch, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			    if ($postdata)
				        {
						        curl_setopt($ch, CURLOPT_POST, 1);
							        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
			    		}
			    $header = array(
				        'Accept: application/json',
					    'Content-Type: application/json; charset=utf-8',
					        'Authorization: Bearer xoxb-452492488390-450397110288-wQYniGHQz56rmdwCIoJUR91d'
					);
			    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			    $contents = curl_exec($ch);
			    $headers = curl_getinfo($ch);
			    info($headers);info($contents);
				    curl_close($ch);
				    return array($contents, $headers);
		}
	        return $request->post('challenge');
});


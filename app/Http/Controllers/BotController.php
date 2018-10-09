<?php

namespace App\Http\Controllers;

use App\Services\BotService;
use Illuminate\Http\Request;
use App\Jobs\PostMessageJob;

class BotController extends Controller
{
    protected $botSerivce;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->botService = new BotService();
    }

    /**
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $question = $request->input('question');
        $rand_answer = $this->botService->getBestAnswer($question);
        die(json_encode($rand_answer));
    }

    /**
     * Catch and Send message
     * @param Request $request
     * @return json
     */
    public function postMessage(Request $request)
    {
        info($request->all());
        if(!empty($request->post('event')) && !empty($request->post('event')['text']) && empty($request->post('event')['bot_id'])) {
	    $event = $request->post('event');	
	    $channel = $event['channel'];
	    $question = $event['text'];
	    $user = $event['user'];
            dispatch(new PostMessageJob($this->botService, $question, $channel, $user));
            die(json_encode(array('status' => 200, 'message' => 'OK')));
        }

        return null;
    }
}

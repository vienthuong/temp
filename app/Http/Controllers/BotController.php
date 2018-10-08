<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bot;
use App\Jobs\PostMessageJob;

class BotController extends Controller
{
    protected $bot;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->bot = new Bot();
    }

    /**
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $question = $request->input('question');
        $rand_answer = $this->bot->getBestAnswer($question);
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
            $channel = $request->post('event')['channel'];
            $question = $request->post('event')['text'];
            dispatch(new PostMessageJob($this->bot, $question, $channel));
            die(json_encode(array('status' => 200, 'message' => 'OK')));
        }

        return null;
    }
}

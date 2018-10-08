<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bot;

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
        if(empty($request->post('event')['bot_id']) || $request->post('event')['bot_id'] !== 'BDAENJ614') {
            $question = $request->post('event')['text'];
            $rand_answer = $this->bot->getBestAnswer($question);

            $url = 'https://slack.com/api/chat.postMessage';
            $ch = curl_init();
            $message = [];
            $message['channel'] = 'CDAEGEG14';
            $message['text'] = $rand_answer;

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);

            if (!empty($message))
            {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
            }

            $header = array(
                'Accept: application/json',
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Bearer xoxb-452492488390-450397110288-wQYniGHQz56rmdwCIoJUR91d'
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $contents = curl_exec($ch);
            $headers = curl_getinfo($ch);
            curl_close($ch);
            die(json_encode(array($rand_answer, $contents, $headers)));
        }

        return null;
    }
}

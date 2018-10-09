<?php

namespace App\Jobs;

use App\Services\BotService;

class PostMessageJob extends Job
{
    protected $botService;
    protected $question;
    protected $channel;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(BotService $botService, $question = '', $channel = '')
    {
	    info('========QUEUE RUNNING===========');
        $this->botService = $botService;
        $this->question = $question;
        $this->channel = $channel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
	    info('RUNNING');
        $rand_answer = $this->botService->getBestAnswer($this->question);
        info('Question: ' . $this->question);
        info('Answer: ' . $rand_answer['value']);
        $url = 'https://slack.com/api/chat.postMessage';
        $ch = curl_init();
        $message = [];
        $message['channel'] = $this->channel;
        $message['text'] = $rand_answer['value'];

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
    }
}

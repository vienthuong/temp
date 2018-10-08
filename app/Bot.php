<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use FuzzyWuzzy\Fuzz;
use FuzzyWuzzy\Process;
use Google_Service_Sheets;
use Google_Client;

class Bot extends Model
{
    protected $fillable = [
        //
    ];

    public function getBestAnswer($question = '')
    {
        $fuzz = new Fuzz();
        $process = new Process($fuzz);
        // Prints the names and majors of students in a sample spreadsheet:
        // https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
        $values = $this->getSheetData();
        $choices = array_pluck($values, 1);

        $matches = $process->extractBests($question, $choices)->toArray();
        $match_count = count($matches);
        $bestChoices = [$matches[0]];

        for($i = 0; $i < $match_count; $i++) {
            if ($i + 1 == $match_count) {
                break;
            }
            if($matches[$i][1] == $matches[$i + 1][1]) {
                $bestChoices[] = $matches[$i + 1];
            } else {
                break;
            }
        }


        $bestChoice = count($bestChoices) == 1 ? $bestChoices[0] : $bestChoices[array_rand($bestChoices)];

        $result = array_filter($values, function($value) use ($bestChoice) {
            return $value[1] == $bestChoice[0];
        });

        $intent = array_first($result)[0];

        // Get Answer from Intent
        $answers = array_filter($values, function($value) use ($intent) {
            return $value[0] == $intent && count($value) == 3;
        });

        $rand_answer = [];
        $rand_answer['value'] = $answers[array_rand($answers)][2];
        $rand_answer['ratio'] = $bestChoice[1] . '%';
    }

    public function getSheetData()
    {
        $spreadsheetId = '14RoXzXTkHLBGaMgquflzzN8T-xQY5X5wOXeEQlf9EH0';
        $range = 'AI!A2:C';

        try{
            $client = $this->getClient();
            $service = new Google_Service_Sheets($client);
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        }catch (Exception $e) {
            $e->getMessage();
            die();
        }

        return array_filter($response->getValues());
    }

    private function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Sheets API PHP Quickstart');
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
        $client->setAuthConfig(base_path().'/credentials2.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Load previously authorized token from a file, if it exists.
        $tokenPath = base_path() . '/token.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                $authCode = '4/bgC-J92BZ6z7c1nkG_GMLGgOtcBIjLtjL5qhniaTPlLUVzZRsXAxXxc';

                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }
            // Save the token to a file.
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }
        return $client;
    }

}

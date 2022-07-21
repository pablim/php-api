<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/libraries/google-api-php-client-2.2.2/vendor/autoload.php';
use \Google\Client;

function getClient() {
    $client = new Google_Client();
    $client->setApplicationName('phpapi');
    $client->setIncludeGrantedScopes(true);   // incremental auth
    // $client->setAccessType("offline");        // offline access
    $client->setScopes(Google_Service_Calendar::CALENDAR);
    // $client->setScopes(Google_Service_Drive::DRIVE);
    // $client->setAuthConfig('credentials.json');
    $client->setAuthConfig($_SERVER["DOCUMENT_ROOT"] . '/credentials.json');
    $client->setAccessType('offline');
    // $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/teste_calendar.php';
    $redirect_uri = '/teste_calendar.php';
    // $client->setRedirectUri('http://127.0.0.1:81'. '/teste_calendar');
    $client->setRedirectUri('http://localhost'.'/teste_calendar');
    // $client->setPrompt('select_account consent');


    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = $_SERVER["DOCUMENT_ROOT"] . '/token.json';
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
            // printf("Open the following link in your browser:\n%s\n", $authUrl);
            // print 'Enter verification code: ';

            if (!isset($_GET['code'])) {
              header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
            }

            $authCode = trim($_GET['code']);
            // $client->authenticate($authCode);
            // var_dump(trim($_GET['code']));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            // var_dump($accessToken);

            $client->setAccessToken($accessToken);
            // $_SESSION['access_token'] = $client->getAccessToken();

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

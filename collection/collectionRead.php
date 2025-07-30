<?php

class ReadCollection
{
    private const API_CONSUMER_KEY = 'x';
    private const API_CONSUMER_SECRET = 'x';
    private const API_TOKEN = 'x';
    private const API_TOKEN_SECRET = 'x';
    private const DOMAIN = 'https://www.peoplescollection.wales';

    private \OAuth $oauth;

    public function __construct()
    {
        $this->oauth = new \OAuth(
            self::API_CONSUMER_KEY,
            self::API_CONSUMER_SECRET,
            OAUTH_SIG_METHOD_HMACSHA1,
            OAUTH_AUTH_TYPE_URI
        );

        $this->oauth->setToken(self::API_TOKEN, self::API_TOKEN_SECRET);

        $this->getData();
    }

    private function getData(): void
    {
        try {
            $this->oauth->fetch(self::DOMAIN . '/rest/v1/collection/2275356', [], OAUTH_HTTP_METHOD_GET);
            $response = $this->oauth->getLastResponse();
            print_r($response);
        } catch (\OAuthException $e) {
            echo "OAuth Exception: " . $e->getMessage() . PHP_EOL;
            echo "Last Response: " . $e->lastResponse . PHP_EOL;
        }
    }
}

new ReadCollection();

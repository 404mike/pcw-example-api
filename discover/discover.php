<?php

class PCWDiscover
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

        $this->apiCalls();
    }

    /**
     * Example API calls for /discover endpoint.
     */
    private function apiCalls(): void
    {
        // discover
        $discover = $this->apiRequest('discover');
        // location
        $locations = $this->apiRequest('discover', "?lat=51.504789&lon=-3.161316&radius=10");
        // byCreator
        $byCreator = $this->apiRequest('discover', '?byCreator=Hazel%20Thomas');
        // what facet - Mining (Other)
        $whatFacet = $this->apiRequest('discover', '?what[0]=71');
        // when facet - 1970s
        $whenFacet = $this->apiRequest('discover', '?when[0]=35');
        // query
        $query = $this->apiRequest('discover', '?query=cardiff%20castle');
        // query limited to userId
        $queryUserId = $this->apiRequest('discover', '?query=cardiff%20castle&userId=3106');
        // query limited to userId with limit and offset
        $queryUserIdLimitOffset = $this->apiRequest('discover', '?query=cardiff%20castle&userId=3106&limit=10&offset=1');
        // tag
        $tag = $this->apiRequest('discover', '?containsTag[0]=Rugby');
        // tag limited to userId
        $tagUserId = $this->apiRequest('discover', '?containsTag[0]=Police&userId=7647');
    }

    /**
     * Makes an API request to the discover endpoint.
     *
     * @param string $endpoint
     * @param string $query
     * @return array<string, mixed>|null
     */
    public function apiRequest(string $endpoint, string $query = ''): ?array
    {
        try {
            $url = self::DOMAIN . '/rest/v1/' . $endpoint . $query;
            $this->oauth->fetch($url);
            return json_decode($this->oauth->getLastResponse(), true);
        } catch (\OAuthException $e) {
            echo "OAuth Error: " . $e->getMessage();
            return null;
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }
}

new PCWDiscover();

<?php

class StoryCreate
{
    private const API_CONSUMER_KEY = 'x';
    private const API_CONSUMER_SECRET = 'x';
    private const API_TOKEN = 'x';
    private const API_TOKEN_SECRET = 'x';
    private const DOMAIN = 'https://www.peoplescollection.wales';

    private \OAuth $oauth;
    private ?array $coverImage = null;

    public function __construct()
    {
        $this->oauth = new \OAuth(
            self::API_CONSUMER_KEY,
            self::API_CONSUMER_SECRET,
            OAUTH_SIG_METHOD_HMACSHA1,
            OAUTH_AUTH_TYPE_URI
        );
        $this->oauth->setToken(self::API_TOKEN, self::API_TOKEN_SECRET);

        $this->uploadCover();
        $this->createData();
    }

    /**
     * Uploads a cover image using cURL and stores the result.
     *
     * @return void
     */
    private function uploadCover(): void
    {
        $ch = curl_init();
        $file = curl_file_create('images/image1.jpg', 'image/jpeg', 'image1.jpg');
        $data = ['files[attachment]' => $file];

        curl_setopt($ch, CURLOPT_URL, self::DOMAIN . '/api_gateway/file');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        $this->coverImage = is_string($result) ? json_decode($result, true) : null;
    }

    /**
     * Builds the post data and sends it to the API.
     *
     * @return void
     */
    private function createData(): void
    {
        $postData['title[en]'] = 'API UPDATE Title English';
        $postData['title[cy]'] = 'API UPDATE Title Welsh';
        $postData['description[en]'] = 'This is the english description asdfasds d';
        $postData['description[cy]'] = 'This is the welsh descriptiona a dasdasd';
        $postData['content[en]'] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
        $postData['content[cy]'] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
        $postData['creator'] = 'John Smith';
        $postData['owner'] = 'Dave Johnson';

        // Here we tell the API where the files are. The name is all we need - the API knows where the gateway put them.
        $postData['image'] = $this->coverImage[0]->fid;

        $postData['tags[000]'] = 'Tag1';
        $postData['tags[001]'] = 'Tag2';

        $postData['what[0]'] = 42;
        $postData['what[1]'] = 43;

        $postData['when[0]'] = 29;
        $postData['when[1]'] = 30;

        $postData['items[0]'] = 8;
        $postData['items[1]'] = 11;
        $postData['items[2]'] = 22;
        $postData['items[3]'] = 33;

        $postData['created_date'] = '+0020131001140000,+0020141001140000';
        $postData['onBehalf'] = 3213;

        $this->createStory($postData);
    }

    /**
     * Sends the story data to the API.
     *
     * @param array<string, mixed> $storyData
     * @return string|null
     */
    public function createStory(array $storyData): ?string
    {
        try {
            $this->oauth->fetch(self::DOMAIN . '/rest/v1/story', $storyData, OAUTH_HTTP_METHOD_POST);
            return $this->oauth->getLastResponse();
        } catch (\OAuthException $e) {
            echo "OAuth Exception: " . $e->getMessage() . PHP_EOL;
            echo "Response: " . $e->lastResponse . PHP_EOL;
            return null;
        }
    }
}

new StoryCreate();
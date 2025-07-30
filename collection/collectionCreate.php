<?php

declare(strict_types=1);

class CollectionCreate
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
        $postData = [
            'title[en]' => 'API UPDATE Title English',
            'title[cy]' => 'API UPDATE Title Welsh',
            'description[en]' => 'This is the english description asdfasds d',
            'description[cy]' => 'This is the welsh descriptiona a dasdasd',
            'creator' => 'John Smith',
            'owner' => 'Dave Johnson',
        ];

        if (is_array($this->coverImage) && isset($this->coverImage[0]['fid'])) {
            $postData['image'] = $this->coverImage[0]['fid'];
        }

        // Tags
        $tags = ['Tag1', 'Tag2'];
        foreach ($tags as $i => $tag) {
            $postData[sprintf('tags[%03d]', $i)] = $tag;
        }

        // Facets
        $postData['what[0]'] = 42;
        $postData['what[1]'] = 43;
        $postData['when[0]'] = 39;
        $postData['when[1]'] = 40;

        // Items
        $itemIds = [8, 11, 22, 33];
        foreach ($itemIds as $i => $id) {
            $postData[sprintf('items[%d]', $i)] = $id;
        }

        $postData['onBehalf'] = 3213;

        $this->createCollection($postData);
    }

    /**
     * Sends the collection data to the API.
     *
     * @param array<string, mixed> $collectionData
     * @return string|null
     */
    public function createCollection(array $collectionData): ?string
    {
        try {
            $this->oauth->fetch(self::DOMAIN . '/rest/v1/collection', $collectionData, OAUTH_HTTP_METHOD_POST);
            return $this->oauth->getLastResponse();
        } catch (\OAuthException $e) {
            echo "OAuth Exception: " . $e->getMessage() . PHP_EOL;
            echo "Response: " . $e->lastResponse . PHP_EOL;
            return null;
        }
    }
}

new CollectionCreate();
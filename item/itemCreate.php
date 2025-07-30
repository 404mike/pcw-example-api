<?php

class CreateItem
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

        $this->createData();
    }

    private function createData(): void
    {
        $postData = [
            'title[en]' => 'API UPDATE Title English',
            'title[cy]' => 'API UPDATE Title Welsh',
            'description[en]' => 'This is the english description asdfasds d',
            'description[cy]' => 'This is the welsh descriptiona a dasdasd',
            'creator' => 'John Smith',
            'owner' => 'Dave Johnson',
            'publisher_ref' => 'GTJ01007',
        ];

        // Upload 4 images
        for ($i = 0; $i <= 3; $i++) {
            $imageID = $i + 1;
            $file = $this->uploadImage("image$imageID.jpg");

            if (is_array($file) && isset($file[0]->fid, $file[0]->name)) {
                $postData[sprintf('files[00%d][fid]', $i)] = $file[0]->fid;
                $postData[sprintf('files[00%d][name]', $i)] = $file[0]->name;
            }
        }

        // Facets
        $postData['what[0]'] = 42;
        $postData['what[1]'] = 43;
        $postData['when[0]'] = 39;
        $postData['when[1]'] = 40;

        // URLs
        $postData['original_url[en]'] = 'https://www.peoplescollection.wales';
        $postData['original_url[cy]'] = 'https://www.casgliadywerin.cymru';

        // Rights
        $postData['rights[0][english_rights_holder]'] = 'Johnny';
        $postData['rights[0][welsh_rights_holder]'] = 'Johnny';
        $postData['rights[0][rights_type]'] = 'copyright';
        $postData['rights[0][rights_year]'] = '1985';

        // Tags
        $tags = [
            'Tag1', 'Tag2', 'welshness', 's4c', 'nationalism', 'refferendwm',
            'nationality', 'gwleidyddiaeth', 'royal family', 'atgofion',
            'coronation', 1954
        ];
        foreach ($tags as $i => $tag) {
            $postData[sprintf('tags[%03d]', $i)] = $tag;
        }

        // Location
        $postData['locations[0][lat]'] = '53.331714';
        $postData['locations[0][lon]'] = '-3.830576';
        $postData['locations[0][description][en]'] = 'English location';
        $postData['locations[0][description][cy]'] = 'Welsh location';

        // Other
        $postData['created_date'] = '+0020131001140000,+0020141001140000';
        $postData['onBehalf'] = 3213;

        $this->createItem($postData);
    }

    /**
     * Sends data to the API to create an item.
     *
     * @param array<string, mixed> $itemData
     * @return void
     */
    public function createItem(array $itemData): void
    {
        try {
            $this->oauth->fetch(self::DOMAIN . '/rest/v1/item', $itemData, OAUTH_HTTP_METHOD_POST);
            $response = $this->oauth->getLastResponse();
            print_r($response);
        } catch (\OAuthException $e) {
            echo "OAuth Exception: " . $e->getMessage() . PHP_EOL;
            echo "Last Response: " . $e->lastResponse . PHP_EOL;
        }
    }

    /**
     * Uploads an image file via the API Gateway.
     *
     * @param string $file
     * @return array<int, object>|null
     */
    public function uploadImage(string $file): ?array
    {
        $ch = curl_init();
        $curlFile = curl_file_create($file, 'image/jpeg', basename($file));
        $data = ['files[attachment]' => $curlFile];

        curl_setopt($ch, CURLOPT_URL, self::DOMAIN . '/api_gateway/file');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return is_string($result) ? json_decode($result) : null;
    }
}

new CreateItem();

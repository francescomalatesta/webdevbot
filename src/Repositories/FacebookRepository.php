<?php

namespace WebDevBot\Repositories;


use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use WebDevBot\Support\Logger;

class FacebookRepository
{
    const BASE_URI = 'https://graph.facebook.com/';
    const FB_API_VERSION = 'v2.6';

    private $client;
    private $logger;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => self::BASE_URI . self::FB_API_VERSION
        ]);

        $this->logger = new Logger();
    }

    public function getLatestPosts()
    {
        $fifteenMinutesAgo = Carbon::now()->subMinutes(intval(getenv('TIME_SLOT')))->toAtomString();
        $groupEndpoint = getenv('FACEBOOK_GROUP_ID') . '/feed';

        try {
            $response = $this->client->request('GET', $groupEndpoint, [
                'query' => [
                    'since' => $fifteenMinutesAgo,
                    'access_token' => getenv('FACEBOOK_ACCESS_TOKEN')
                ]
            ]);

            $posts = json_decode($response->getBody()->getContents(), true)['data'];
        } catch (ClientException $e) {
            $this->logger->error($e->getResponse()->getBody()->getContents());
            $posts = [];
        }

        return $posts;
    }

    public function commentPostWithWarning($postId)
    {
        $postCommentEndpoint = getenv('FACEBOOK_GROUP_ID') . '_' . $postId . '/comments';
        $warningMessage = str_replace('\n', "\n", getenv('WARNING_MESSAGE'));

        try {
            $this->client->request('POST', $postCommentEndpoint, [
                'query' => [
                    'access_token' => getenv('FACEBOOK_ACCESS_TOKEN')
                ],
                'form_params' => [
                    'message' => $warningMessage
                ]
            ]);
        } catch (ClientException $e) {
            $this->logger->error($e->getResponse()->getBody()->getContents());
        }
    }

    public function findPostById($postId)
    {
        $postEndpoint = getenv('FACEBOOK_GROUP_ID') . '_' . $postId;

        try {
            $response = $this->client->request('GET', $postEndpoint, [
                'query' => [
                    'access_token' => getenv('FACEBOOK_ACCESS_TOKEN')
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $e) {
            $this->logger->error($e->getResponse()->getBody()->getContents());
            throw new \Exception('Non è stato possibile trovare il post ' . $postId);
        }
    }

    public function deletePostById($postId)
    {
        $postEndpoint = getenv('FACEBOOK_GROUP_ID') . '_' . $postId;

        try {
            $this->client->request('DELETE', $postEndpoint, [
                'query' => [
                    'access_token' => getenv('FACEBOOK_ACCESS_TOKEN')
                ]
            ]);
        } catch (ClientException $e) {
            $this->logger->error($e->getResponse()->getBody()->getContents());
        }
    }
}

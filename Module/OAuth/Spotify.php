<?php

namespace Module\OAuth;

use Npf\Core\Common;

/**
 * Class Spotify
 * @package Module
 */
class Spotify extends Base
{
    protected $name = 'spotify';

    /**
     * @param $redirectUri
     * @param string $state
     * @param bool $prompt
     * @return bool|string
     */
    final public function getOAuthUrl($redirectUri, $state, $prompt = false)
    {
        $params = [
            'client_id' => $this->config['client_id'],
            'response_type' => 'code',
            'show_dialog' => $prompt,
            'redirect_uri' => $redirectUri,
            'nonce' => Common::genUuidV4(),
        ];
        if (!empty($this->config['scopes']))
            $params['scope'] = implode(' ', $this->config['scopes']);
        if (!empty($state))
            $params['state'] = (string)$state;
        return "{$this->config['endpoint']}/authorize?" . http_build_query($params);
    }

    /**
     * @param $redirectUri
     * @param $code
     * @return array|boolean
     */
    final public function getAccessToken($redirectUri, $code)
    {
        $response = $this->app->library->rpc->run("{$this->config['endpoint']}/api/token", 'POST', [
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
            'code' => $code,
        ], ['Authorization' => "Basic " . base64_encode("{$this->config['client_id']}:{$this->config['client_secret']}")]);
        return json_decode($response, true);
    }

    /**
     * @param $accessToken
     * @return array
     */
    final public function getUserInfo($accessToken)
    {
        $response = $this->app->library->rpc->run("{$this->config['apiUrl']}/v1/me", 'GET', '', [
            'Authorization' => "Bearer {$accessToken}",
        ]);
        return json_decode($response, true);
    }
}
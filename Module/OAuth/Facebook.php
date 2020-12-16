<?php

namespace Module\OAuth;

use Npf\Core\Common;

/**
 * Class Facebook
 * @package Module
 */
class Facebook extends Base
{
    protected $name = 'facebook';

    /**
     * @param $redirectUri
     * @param string $state
     * @return bool|string
     */
    final public function getOAuthUrl($redirectUri, $state = '')
    {
        $params = [
            'client_id' => $this->config['client_id'],
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'nonce' => Common::genUuidV4(),
        ];
        if (!empty($this->config['scopes']))
            $params['scope'] = implode(',', $this->config['scopes']);
        if (!empty($state))
            $params['state'] = (string)$state;
        return "{$this->config['endpoint']}?" . http_build_query($params);
    }

    /**
     * @param $redirectUri
     * @param $code
     * @return array|boolean
     */
    final public function getAccessToken($redirectUri, $code)
    {
        return $this->apiCall('/oauth/access_token', [
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'redirect_uri' => $redirectUri,
            'code' => $code,
        ]);
    }

    /**
     * @param $accessToken
     * @return array
     */
    final public function getUserInfo($accessToken)
    {
        return $this->apiCall('/me', [
            'fields' => 'id,name,email',
            'access_token' => $accessToken,
        ]);
    }

    /**
     * @param $action
     * @param $params
     * @return mixed
     */
    private function apiCall($action, $params)
    {
        $response = $this->app->library->rpc->run("{$this->config['apiUrl']}{$action}", 'GET', $params);
        return json_decode($response, true);
    }
}
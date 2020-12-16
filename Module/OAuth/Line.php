<?php

namespace Module\OAuth;

use Npf\Core\Common;

/**
 * Class Line
 * @package Module
 */
class Line extends Base
{
    protected $name = 'line';

    /**
     * @param $redirectUri
     * @param string $prompt
     * @param string $state
     * @return bool|string
     */
    final public function getOAuthUrl($redirectUri, $state, $prompt = 'none')
    {
        $params = [
            'client_id' => $this->config['client_id'],
            'response_type' => $prompt,
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
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
            'code' => $code,
        ]);
        return json_decode($response, true);
    }

    /**
     * @param $idToken
     * @return array
     */
    final public function getUserInfo($idToken)
    {
        $data = explode('.', $idToken);
        $this->app->profiler->debug($idToken, $data);
        foreach ($data as &$item)
            $item = base64_decode($item);
        $userInfo = json_decode($data[1], true);
        $result = [];
        if (!empty($userInfo['sub']))
            $result['id'] = $userInfo['sub'];
        if (!empty($userInfo['name']))
            $result['name'] = $userInfo['name'];
        if (!empty($userInfo['preferred_username']))
            $result['email'] = $userInfo['preferred_username'];
        if (!empty($userInfo['ttl']))
            $result['ttl'] = $userInfo['exp'];
        return $result;
    }
}
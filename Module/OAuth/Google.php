<?php

namespace Module\OAuth;

use Npf\Core\Common;

/**
 * Class Google
 * @package Module
 */
class Google extends Base
{
    protected $name = 'google';

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
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'prompt' => $prompt,
            'include_granted_scopes' => 'true',
            'nonce' => Common::genUuidV4(),
        ];
        if (!empty($this->config['scopes']))
            $params['scope'] = implode(' ', $this->config['scopes']);
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
        return $this->apiCall('/token', [
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
            'code' => $code,
        ]);
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
        if (!empty($userInfo['email']))
            $result['email'] = $userInfo['email'];
        if (!empty($userInfo['picture']))
            $result['picture'] = $userInfo['picture'];
        if (!empty($userInfo['email_verified']))
            $result['email_verified'] = $userInfo['email_verified'];
        if (!empty($userInfo['ttl']))
            $result['ttl'] = $userInfo['exp'];
        return $result;
    }

    /**
     * @param $action
     * @param $params
     * @return mixed
     */
    private function apiCall($action, $params)
    {
        $response = $this->app->library->rpc->run("{$this->config['apiUrl']}{$action}", 'POST', $params);
        return json_decode($response, true);
    }
}
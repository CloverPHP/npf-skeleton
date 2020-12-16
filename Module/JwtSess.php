<?php

namespace Module;

use Exception;
use Npf\Core\App;
use Firebase\JWT\JWT;
use Npf\Exception\InternalError;
use Firebase\JWT\ExpiredException;
use Exception\LoginRequired;

/**
 * Class Reporter
 * @package Module
 */
class JwtSess
{
    /**
     * @var App
     */
    private $app;

    /**
     * @var Module
     */
    private $module;

    /**
     * @var mixed|string
     */
    private $secret = '';

    /**
     * @var int
     */
    private $minTtl = 1800;

    /**
     * @var int
     */
    private $maxTtl = 3600;

    /**
     * jwt Data
     * @var string
     */
    private $jwt = '';

    /**
     * Is obtain data already?
     * @var bool
     */
    private $obtain = false;

    /**
     * @var array
     */
    private $token = [
        'iss' => 'php',
        "sub" => '',
        "adu" => "",
        "exp" => 0,
        "data" => []
    ];

    /**
     * Jwt Encode Algorithm
     * @var array
     */
    private $jwtAlg = [
        'typ' => 'JWT',
        'alg' => 'HS256'
    ];

    /**
     * Jwt Cookie Name
     * @var string
     */
    private $cookieName = '';

    private $updated;

    /**
     * JwtSess constructor.
     * @param App $app
     * @param Module $module
     * @throws InternalError
     */
    public function __construct(App &$app, Module &$module)
    {
        $this->app = &$app;
        $this->module = &$module;
        $this->cookieName = $app->getAppName();
        $this->minTtl = 86400 * 30;
        $this->maxTtl = 86400 * 30;
        $this->secret = $this->app->config('General')->get('secret');
    }

    /**
     * Clear Jwt Data
     */
    final public function clear()
    {
        $this->init();
        $this->app->cookie->set($this->cookieName, '', -1);
    }

    /**
     * Initial Jwt
     * @param array $data
     * @return JwtSess
     */
    final public function init(array $data = [])
    {
        $this->token['data'] = $data;
        $this->obtain = true;
        return $this;
    }

    /**
     * Obtain Jwt Data
     * @return JwtSess
     * @throws LoginRequired
     */
    final public function obtain()
    {
        if (!$this->obtain) {
            $jwt = (string)$this->app->request->get('jwt');
            if (!$jwt && $this->cookieName) $jwt = $this->app->cookie->get($this->cookieName, '');
            if (!empty($jwt)) {
                try {
                    $this->token = (array)JWT::decode($jwt, $this->secret, $this->jwtAlg);
                } catch (ExpiredException $err) {
                    throw new LoginRequired($err->getMessage() . ' ' . $jwt);
                } catch (Exception $ex) {
                }
                $this->token['data'] = (array)$this->token['data'];
            }
            $this->jwt = JWT::encode($this->token, $this->secret, $this->jwtAlg['alg']);
            $this->obtain = true;
        }
        return $this;
    }

    /**
     * Add Jwt Data
     * @param $name
     * @param $value
     */
    final public function add($name, $value)
    {
        if (!is_array($this->token['data']))
            $this->token['data'] = [];
        $this->token['data'][$name] = $value;
        $this->updated = true;
    }

    /**
     * Get Jwt String
     * @return string
     */
    final public function getJwt()
    {
        return $this->jwt;
    }

    /**
     * Get Jwt String
     * @param $jwt
     * @return string
     */
    final public function setJwt($jwt)
    {
        return $this->jwt = $jwt;
    }

    /**
     *
     */
    final public function saveJwt()
    {
        $this->token['iss'] = '';
        $this->token['adu'] = '';
        $this->token['sub'] = '';
        $this->token['iat'] = time();
        $this->token['exp'] = time() + mt_rand($this->minTtl, $this->maxTtl);
        $this->jwt = JWT::encode($this->token, $this->secret, $this->jwtAlg['alg']);
        $this->app->response->set('jwt', $this->jwt);
        $this->app->cookie->set($this->cookieName, $this->jwt, $this->token['exp'], '/', "", false, true);
    }

    /**
     * @return bool|mixed
     * @throws LoginRequired
     */
    final public function renewJwt()
    {
        $ttl = (int)$this->token['exp'] - time();
        if (!empty($jwt) && $ttl < 0) {
            throw new LoginRequired('Session Expired');
        } elseif ($ttl < $this->minTtl || $ttl > $this->maxTtl) {
            $this->saveJwt();
            return $this->token['exp'];
        }
        return false;
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed|null
     * @throws LoginRequired
     */
    final public function get($name, $default = null)
    {
        if (!$this->obtain)
            $this->obtain();
        return $name === '*' ? $this->token['data'] : (isset($this->token['data'][$name]) ? $this->token['data'][$name] : $default);
    }

    /**
     * @return array
     */
    final public function getToken()
    {
        return $this->token;
    }

    /**
     * @param $name
     * @return mixed|null
     * @throws LoginRequired
     */
    final public function __invoke($name)
    {
        return $this->get($name);
    }

    /**
     * @param $name
     * @return mixed|null
     * @throws LoginRequired
     */
    final public function __get($name)
    {
        return $this->get($name);
    }
}
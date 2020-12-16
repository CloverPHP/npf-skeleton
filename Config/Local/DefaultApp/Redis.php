<?php

namespace Config\Local\DefaultApp;

/**
 * Class Redis
 * @package Config\Local\App
 */
class Redis
{
    /**
     * @var bool Redis Enable or not.
     */
    public $enable = true;
    /**
     * @var int Redis DB Index
     */
    public $db = 0;
    /**
     * @var string Redis Post Hash
     */
    public $postHash = '';
    /**
     * @var string Redis Server Auth Pass
     */
    public $authPass = '';
    /**
     * @var int Connection Timeout
     */
    public $timeout = 3;
    /**
     * @var int Read/Write Wait Timeout
     */
    public $rwTimeout = 3;

    public $allowReconnect = false;

    /**
     * @var array Redis Instance
     */
    public $instance = [
        [
            ['127.0.0.1', 6379],
        ],
    ];
}
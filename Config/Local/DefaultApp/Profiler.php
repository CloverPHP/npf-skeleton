<?php

namespace Config\Local\DefaultApp;

/**
 * Class Profiler
 * @package Config\Local\App
 */
class Profiler
{
    /**
     * @var bool Profiler Enable or not
     */
    public $enable = true;
    /**
     * @var string Output Error as config format: json, xml, twig, none(status=500)
     */
    public $errorOutput = 'auto';
    /**
     * @var string Output Error Twig, if empty will internal twig
     */
    public $errorTwig = '@Template/Layout/Other/Error500.twig';
    /**
     * @var int Maximum Log
     */
    public $maxLog = 100;
    /**
     * @var bool Log Critical
     */
    public $logCritical = true;
    /**
     * @var bool Log Error
     */
    public $logError = true;
    /**
     * @var bool Log Info
     */
    public $logInfo = true;
    /**
     * @var bool Log Debug or not
     */
    public $logDebug = true;
    /**
     * @var bool Log db query
     */
    public $queryLogDb = true;
    /**
     * @var bool Log redis query
     */
    public $queryLogRedis = true;
    /**
     * Reporter for Rocket Chat
     * @var array
     */
    public $rocketChat = [
        'connectionTimeout' => 1,
        'timeout' => 1,
        'gateway' => 'http://collector.syncbug.com/RocketChat/Message',
        'channel' => [
            'log' => 'log_notice',
            'debug' => 'debug_notification',
        ],
    ];
}
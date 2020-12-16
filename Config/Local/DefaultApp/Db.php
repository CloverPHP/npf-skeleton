<?php

namespace Config\Local\DefaultApp;

/**
 * Class Db
 * @package Config\Local\App
 */
class Db
{
    /**
     * @var array Db Host Instance
     */
    public $hosts = ['localhost'];
    /**
     * @var string Port Number
     */
    public $port = '3306';
    /**
     * @var string Db User Name
     */
    public $user = 'root';
    /**
     * @var string DB Password
     */
    public $pass = '';
    /**
     * @var string Db Name
     */
    public $name = 'own_app';
    /**
     * @var string Client Side Charset
     */
    public $charset = 'UTF8MB4';
    /**
     * @var string Client Side Charset Collate
     */
    public $collate = 'UTF8MB4_UNICODE_CI';
    /**
     * @var int DbConnection Timeout
     */
    public $timeout = 1;
    /**
     * @var bool Persistent Connection
     */
    public $persistent = false;
    /**
     * @var string Db Driver Name
     */
    public $driver = 'DbMysqli';
    /**
     * @var bool Auto Transaction On.
     */
    public $tran = true;
}
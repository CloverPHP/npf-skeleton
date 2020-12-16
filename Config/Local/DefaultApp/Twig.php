<?php

namespace Config\Local\DefaultApp;

/**
 * Class Twig
 * @package Config\Local\App
 */
class Twig
{
    /**
     * Twig Syntax Style
     * @var array
     */
    public $syntaxStyle = [
        'tag_comment' => ['{#', '#}'],
        'tag_block' => ['{%', '%}'],
        'tag_variable' => ['{{', '}}'],
        'interpolation' => ['#{', '}'],
    ];

    /**
     * @var array Twig Enviroment Option
     */
    public $environmentOption = [];

    /**
     * Auto Append Header when using twig
     * @var array
     */
    public $appendHeader = [];

    /**
     * Twig Paths to load
     * @var array
     */
    public $paths = [];

    /**
     * Twig Extension
     */
    public $extension = [];
}
<?php

namespace Config\Local\DefaultApp;

/**
 * Class Misc
 * @package Config\Local\App
 */
class Misc
{
    /**
     * @var string Lock Key Prefix
     */
    public $geoIpDB = '';

    /**
     * @var string CDN Path for static file (include domain)
     */
    public $cdnDomain = '';

    public $OAuth = [
        'google' => [
            'client_id' => '?',
            'client_secret' => '?',
            'endpoint' => 'https://accounts.google.com/o/oauth2/v2/auth',
            'apiUrl' => 'https://oauth2.googleapis.com',
            'scopes' => [
                'profile', 'email', 'openid'
            ],
        ],
        'facebook' => [
            'client_id' => '?',
            'client_secret' => '?',
            'endpoint' => 'https://www.facebook.com/v9.0/dialog/oauth',
            'apiUrl' => 'https://graph.facebook.com/v9.0',
            'scopes' => [
                'public_profile', 'email'
            ],
        ],
    ];
}
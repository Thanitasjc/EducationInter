<?php

namespace App\Services;

class LineSocialiteProvider extends \SocialiteProviders\Line\Provider
{
    protected $scopes = [
        'openid',
        'profile',
    ];
}

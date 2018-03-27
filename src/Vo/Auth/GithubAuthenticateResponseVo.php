<?php

namespace App\Vo\Auth;

use App\Vo\ValueObject;

class GithubAuthenticateResponseVo extends ValueObject
{
    public $access_token;
    public $token_type;
    public $scope;
}

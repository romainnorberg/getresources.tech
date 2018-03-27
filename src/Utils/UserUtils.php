<?php

namespace App\Utils;

use App\Entity\User;
use App\Vo\Auth\GithubUserResponseVo;

class UserUtils
{
    /* @var $user \App\Entity\User */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function populateFromGithubUserResponseVo(GithubUserResponseVo $githubUserResponseVo): User
    {
        $this->user->setUsername($githubUserResponseVo->login);
        $this->user->setEmail($githubUserResponseVo->email); // can be empty

        return $this->user;
    }
}
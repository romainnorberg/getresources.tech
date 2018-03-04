<?php

namespace App\Vo\Auth;

use App\Vo\ValueObject;

/**
 * Class GithubUserResponseVo
 * @package App\Vo\Auth
 *
 * Doc:
 *   - https://developer.github.com/v3/users/#get-a-single-user
 *   - https://developer.github.com/v3/users/#get-the-authenticated-user
 */
class GithubUserResponseVo extends ValueObject
{
    public $login;
    public $id;
    public $avatar_url;
    public $gravatar_id;
    public $url;
    public $html_url;
    public $followers_url;
    public $following_url;
    public $gists_url;
    public $starred_url;
    public $subscriptions_url;
    public $organizations_url;
    public $repos_url;
    public $events_url;
    public $received_events_url;
    public $type;
    public $site_admin;
    public $name;
    public $company;
    public $blog;
    public $location;
    public $email;
    public $hireable;
    public $bio;
    public $public_repos;
    public $public_gists;
    public $followers;
    public $following;
    public $created_at;
    public $updated_at;
    public $private_gists;
    public $total_private_repos;
    public $owned_private_repos;
    public $disk_usage;
    public $collaborators;
    public $two_factor_authentication;
    public $plan;
}

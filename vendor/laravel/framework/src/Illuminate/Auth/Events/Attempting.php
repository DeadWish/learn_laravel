<?php

namespace Illuminate\Auth\Events;

class Attempting
{
    //尝试登陆事件
    /**
     * The credentials for the user.
     * 用户校验身份的凭证
     * @var array
     */
    public $credentials;

    /**
     * Indicates if the user should be "remembered".
     *
     * @var bool
     */
    public $remember;

    /**
     * Create a new event instance.
     *
     * @param  array  $credentials
     * @param  bool  $remember
     * @return void
     */
    public function __construct($credentials, $remember)
    {
        $this->remember = $remember;
        $this->credentials = $credentials;
    }
}

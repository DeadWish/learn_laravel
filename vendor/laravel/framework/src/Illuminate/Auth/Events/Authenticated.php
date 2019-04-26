<?php

namespace Illuminate\Auth\Events;

use Illuminate\Queue\SerializesModels;

class Authenticated
{
    //用户已被验证事件,在第一次设置到 guard 中 user 时触发
    use SerializesModels;

    /**
     * The authenticated user.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}

<?php

namespace Illuminate\Events;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Queue\Factory as QueueFactoryContract;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     * app启动时会被注册
     * @return void
     */
    public function register()
    {
        //容器中绑定'events'匿名函数
		//events 被解析一次以后，就会被存下
		/* 这个返回的实例
		 * (new Dispatcher($app))->setQueueResolver(function () use ($app) {
                return $app->make(QueueFactoryContract::class);
            });
		 */
        $this->app->singleton('events', function ($app) {
            return (new Dispatcher($app))->setQueueResolver(function () use ($app) {
                return $app->make(QueueFactoryContract::class);
            });
        });
    }
}

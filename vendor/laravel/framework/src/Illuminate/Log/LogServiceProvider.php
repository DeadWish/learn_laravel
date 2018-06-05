<?php

namespace Illuminate\Log;

use Monolog\Logger as Monolog;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     * app启动时会被注册
     * @return void
     */
    public function register()
    {
        //单例绑定'log'为匿名函数，好处就是，使用的时候才
        $this->app->singleton('log', function () {
            return $this->createLogger();
        });
    }

    /**
     * Create the logger.
     *
     * @return \Illuminate\Log\Writer
     */
    public function createLogger()
    {
    	//创建一个log writer的实例 channel就是日志的名字，后面的app['events']，是事件分发器，用来处理日志记录事件
        $log = new Writer(
            new Monolog($this->channel()), $this->app['events']
        );

        //这里的代码是设置 monolog的handler，hasMonologConfigurator设置的话可以自定义monolog handler
        if ($this->app->hasMonologConfigurator()) {
            call_user_func($this->app->getMonologConfigurator(), $log->getMonolog());
        } else {
        	//看名字就知道，这个是读取config配置的log handler
            $this->configureHandler($log);
        }

        //这里返回的是Illuminate\Log\Writer 的实例，所以Log::xxx(),调用的都是Writer的方法
        return $log;
    }

    /**
     * Get the name of the log "channel".
     *
     * @return string
     */
    protected function channel()
    {
        if ($this->app->bound('config') &&
            $channel = $this->app->make('config')->get('app.log_channel')) {
            return $channel;
        }

        return $this->app->bound('env') ? $this->app->environment() : 'production';
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected function configureHandler(Writer $log)
    {
    	//拼写出handler的名字
		//查找这样命名的函数，知道有四种处理函数
		/*
		 * single —— 将日志记录到单个文件中。该日志处理器对应Monolog的StreamHandler。
		 * daily —— 以日期为单位将日志进行归档，每天创建一个新的日志文件记录日志。该日志处理器 对应Monolog的RotatingFileHandler。
		 * syslog —— 将日志记录到syslog中。该日志处理器 对应Monolog的SyslogHandler。
		 * errorlog —— 将日志记录到PHP的error_log中。该日志处理器 对应Monolog的ErrorLogHandler。
		 *
		 * 项目实际日志处理器通过config/app.php中的log配置项决定，默认配置值为single。
		 */
        $this->{'configure'.ucfirst($this->handler()).'Handler'}($log);
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected function configureSingleHandler(Writer $log)
    {
        $log->useFiles(
            $this->app->storagePath().'/logs/laravel.log',
            $this->logLevel()
        );
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected function configureDailyHandler(Writer $log)
    {
        $log->useDailyFiles(
            $this->app->storagePath().'/logs/laravel.log', $this->maxFiles(),
            $this->logLevel()
        );
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected function configureSyslogHandler(Writer $log)
    {
        $log->useSyslog('laravel', $this->logLevel());
    }

    /**
     * Configure the Monolog handlers for the application.
     *
     * @param  \Illuminate\Log\Writer  $log
     * @return void
     */
    protected function configureErrorlogHandler(Writer $log)
    {
        $log->useErrorLog($this->logLevel());
    }

    /**
     * Get the default log handler.
     *
     * @return string
     */
    protected function handler()
    {
        if ($this->app->bound('config')) {
            return $this->app->make('config')->get('app.log', 'single');
        }

        return 'single';
    }

    /**
     * Get the log level for the application.
     *
     * @return string
     */
    protected function logLevel()
    {
        if ($this->app->bound('config')) {
            return $this->app->make('config')->get('app.log_level', 'debug');
        }

        return 'debug';
    }

    /**
     * Get the maximum number of log files for the application.
     *
     * @return int
     */
    protected function maxFiles()
    {
        if ($this->app->bound('config')) {
            return $this->app->make('config')->get('app.log_max_files', 5);
        }

        return 0;
    }
}

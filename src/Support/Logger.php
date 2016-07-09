<?php

namespace WebDevBot\Support;


use Monolog\Handler\StreamHandler;

class Logger
{
    private $logger;

    public function __construct()
    {
        $this->logger = new \Monolog\Logger('app');
        $this->logger->pushHandler(new StreamHandler('app.log', \Monolog\Logger::INFO));
    }

    public function error($message)
    {
        $this->logger->error($message);
    }

    public function info($message)
    {
        $this->logger->info($message);
    }
}

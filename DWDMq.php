<?php

namespace Hkuan\Mq;


class DWDMq {

    const CHANNEL  = '_channel';
    const EXCHANGE = '_exchange';
    const ROUTER   = '_router';
    const CONFIG   = array(
                        'host'      => '127.0.0.1',
                        'port'      => '5672',
                        'login'     => 'guest',
                        'password'  => 'guest',
                        'vhost'     => '/'
                     );

    protected static $instance = null;
    static $key;
    protected static $mq = array(
                            'connect'  => '',
                            'channel'  => '',
                            'connect'  => '',
                            'exchange' => ''
                           );

    private function __construct()
    {
        self::$mq['connect'] = $conn = new \AMQPConnection(self::CONFIG);
        !$conn->connect() && die("Connect Error".PHP_EOL);
    }

    private function __clone()
    {

    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function exchange($exchange)
    {
        $channel = self::$mq['channel'];
        $ex      = new \AMQPExchange($channel);
        $ex->setName($exchange.self::EXCHANGE);
        $ex->setType(AMQP_EX_TYPE_TOPIC);
        $ex->setFlags(AMQP_DURABLE);
        $ex->declareExchange();
        return self::$mq['exchange'] = $ex;
    }

    private function channel()
    {
        $conn                = self::$mq['connect'];
        self::$mq['channel'] = new \AMQPChannel($conn);
    }

    private function subject($subject)
    {
        self::channel();
        self::exchange($subject);
        self::$key = $subject.self::ROUTER;
    }

    public function send($subject, $data)
    {
        self::subject($subject);

        $ex   = self::$mq['exchange'];
        $conn = self::$mq['connect'];
        $ex->publish($data, self::$key);
        $conn->disconnect();
    }

    public function get($subject, $callback)
    {
        self::subject($subject);
        self::queue($subject, $callback);
    }

    private function queue($subject, $callback)
    {
        $channel    = self::$mq['channel'];
        $exchange   = $subject.self::EXCHANGE;
        $bindKey    = $subject.self::ROUTER;
        $queue      = new \AMQPQueue($channel);
        $queue->setName($subject.'_queue');
        $queue->setFlags(AMQP_DURABLE);
        $queue->declareQueue();
        $queue->bind($exchange,$bindKey);
        $queue->consume($callback,AMQP_AUTOACK);//自动ACK应答
    }
}

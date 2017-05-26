<?php

namespace Hkuan\Mq;


class AmqpAdapter {

    const CHANNEL = '_channel';
    const EXCHANGE = '_exchange';
    const ROUTER = '_router';
    const CONFIG = array(
        'host' => '127.0.0.1',
        'port' => '5672',
        'login' => 'guest',
        'password' => 'guest',
        'vhost'=>'/'
    );

    private static $instance;
    private static $mq;
    private static $key;

    private function connect()
    {
        if(!(self::$mq['connect'])){
            self::$mq['connect'] = $conn = new AMQPConnection(self::CONFIG);
            !$conn->connect() && die("Connect Error".PHP_EOL);
        }
        return self::$mq['connect'];
    }

    private function exchange($exchange)
    {
        $channel = self::$mq['channel'];
        $ex = new AMQPExchange($channel);
        $ex->setName($exchange.self::EXCHANGE);
        $ex->setType(AMQP_EX_TYPE_TOPIC);
        $ex->setFlags(AMQP_DURABLE);
        $ex->declareExchange();
        return self::$mq['exchange'] = $ex;
    }

    private function channel()
    {
        $conn = self::$mq['connect'];
        self::$mq['channel'] = new AMQPChannel($conn);
    }

    private function router($router)
    {

    }

    static function subject($subject)
    {
        self::connect();
        self::channel();
        self::exchange($subject);
        self::$key = $subject.self::ROUTER;
        return self;
    }

    public function send($data)
    {
        $ex = self::$mq['exchange'];
        return $ex->publish($data, $this->key);
    }



    public function test(){
        echo 'kuan Amqp';exit;
    }


}

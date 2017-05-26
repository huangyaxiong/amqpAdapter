amqpAdapter
=====

From AMQP adapter to php-amqplib,This package is used for projects "haoshiqi"

### Install

If you have Composer, just include Macaw as a project dependency in your `composer.json`. If you don't just install it by downloading the .ZIP file and extracting it to your project directory.

```
require: {
    "hkuan/amqp_adapterr": "dev-master"
}
```

### Examples

First, `use` the Macaw namespace:

```PHP
producer:

use \Hkuan\Mq\Amqadapter;
DWDMq::getInstance()->send('test','2222');

```
```PHP
consumer:
use \Hkuan\Mq\Amqadapter;
DWDMq::getInstance()->get('test', 'deal');

$i = 1;
function deal($envelope, $queue) {
    $msg = $envelope->getBody();
    echo $msg.PHP_EOL;   
}
```

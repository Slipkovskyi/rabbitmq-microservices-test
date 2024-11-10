<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection(
    getenv('RABBITMQ_HOST'),
    getenv('RABBITMQ_PORT'),
    getenv('RABBITMQ_USER'),
    getenv('RABBITMQ_PASSWORD')
);
$channel = $connection->channel();

$channel->queue_declare('user_queue', false, false, false, false);

$callback = function ($req) {
    $users = [
        ["id" => 1, "name" => "John Doe"],
        ["id" => 2, "name" => "Jane Smith"],
        ["id" => 3, "name" => "Alice Johnson"]
    ];

    $msg = new AMQPMessage(
        json_encode($users),
        ['correlation_id' => $req->get('correlation_id')]
    );
    $req->delivery_info['channel']->basic_publish($msg, '', $req->get('reply_to'));
    $req->delivery_info['channel']->basic_ack($req->delivery_info['delivery_tag']);
};

$channel->basic_consume('user_queue', '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();

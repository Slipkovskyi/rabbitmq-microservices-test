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

$channel->queue_declare('post_queue', false, false, false, false);

$callback = function ($req) {
    $posts = [
        ["id" => 1, "title" => "Post 1", "content" => "Content of post 1"],
        ["id" => 2, "title" => "Post 2", "content" => "Content of post 2"],
        ["id" => 3, "title" => "Post 3", "content" => "Content of post 3"]
    ];

    $msg = new AMQPMessage(
        json_encode($posts),
        ['correlation_id' => $req->get('correlation_id')]
    );
    $req->delivery_info['channel']->basic_publish($msg, '', $req->get('reply_to'));
    $req->delivery_info['channel']->basic_ack($req->delivery_info['delivery_tag']);
};

$channel->basic_consume('post_queue', '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();

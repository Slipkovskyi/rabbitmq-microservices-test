<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

header('Content-Type: application/json');

if (!isset($_SERVER['HTTP_X_KEY'])) {
    http_response_code(403);
    echo json_encode(["error" => "X-KEY header missing"]);
    exit;
}

$service = '';
if (strpos($_SERVER['REQUEST_URI'], '/user') === 0) {
    $service = 'user_queue';
} elseif (strpos($_SERVER['REQUEST_URI'], '/post') === 0) {
    $service = 'post_queue';
} else {
    http_response_code(404);
    echo json_encode(["error" => "Service not found"]);
    exit;
}

$connection = new AMQPStreamConnection(
    getenv('RABBITMQ_HOST'),
    getenv('RABBITMQ_PORT'),
    getenv('RABBITMQ_USER'),
    getenv('RABBITMQ_PASSWORD')
);
$channel = $connection->channel();

$callbackQueue = $channel->queue_declare("", false, false, true, false)[0];
$response = null;
$correlationId = uniqid();

$channel->basic_consume($callbackQueue, '', false, true, false, false, function ($msg) use (&$response, $correlationId) {
    if ($msg->get('correlation_id') === $correlationId) {
        $response = $msg->body;
    }
});

$msg = new AMQPMessage(
    json_encode(["path" => $_SERVER['REQUEST_URI']]),
    ['correlation_id' => $correlationId, 'reply_to' => $callbackQueue]
);
$channel->basic_publish($msg, '', $service);

while (!$response) {
    $channel->wait();
}

echo $response;

$channel->close();
$connection->close();

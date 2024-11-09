<?php
header('Content-Type: application/json');

// Проверка наличия заголовка X-KEY
if (!isset($_SERVER['HTTP_X_KEY'])) {
    http_response_code(403);
    echo json_encode(["error" => "X-KEY header missing"]);
    exit;
}

// Определение сервиса в зависимости от пути
$service = '';
if (strpos($_SERVER['REQUEST_URI'], '/user') === 0) {
    $service = 'http://user-service:8000';
} elseif (strpos($_SERVER['REQUEST_URI'], '/post') === 0) {
    $service = 'http://post-service:8000';
} else {
    http_response_code(404);
    echo json_encode(["error" => "Service not found"]);
    exit;
}

// Прокси-запрос к соответствующему сервису
$response = file_get_contents($service . $_SERVER['REQUEST_URI']);
echo $response;

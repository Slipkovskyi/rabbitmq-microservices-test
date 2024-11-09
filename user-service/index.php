<?php
header('Content-Type: application/json');

$users = [
    ["id" => 1, "name" => "John Doe"],
    ["id" => 2, "name" => "Jane Smith"],
    ["id" => 3, "name" => "Alice Johnson"]
];

echo json_encode($users);

<?php
header('Content-Type: application/json');

$posts = [
    ["id" => 1, "title" => "Post 1", "content" => "Content of post 1"],
    ["id" => 2, "title" => "Post 2", "content" => "Content of post 2"],
    ["id" => 3, "title" => "Post 3", "content" => "Content of post 3"]
];

echo json_encode($posts);

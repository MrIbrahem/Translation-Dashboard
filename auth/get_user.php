<?php
//---
// page type json
if (!headers_sent()) {
    header('Content-Type: application/json');
}
//---
include_once __DIR__ . '/user_infos.php';
// ---
$data = ["username" => global_username];
// ---
echo json_encode($data);


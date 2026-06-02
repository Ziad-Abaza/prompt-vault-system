<?php
require_once 'bootstrap.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'error' => 'auth_required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $following_id = (int)($_POST['following_id'] ?? 0);
    if ($following_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'invalid_user']);
        exit;
    }

    $result = toggle_user_follow($following_id);
    if ($result) {
        echo json_encode([
            'success' => true, 
            'state' => $result,
            'followers_count' => get_followers_count($following_id)
        ]);
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'failed']);

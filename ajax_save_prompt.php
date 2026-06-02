<?php
require_once 'bootstrap.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'error' => 'auth_required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prompt_id = (int)($_POST['prompt_id'] ?? 0);
    if ($prompt_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'invalid_prompt']);
        exit;
    }

    $result = toggle_prompt_save($prompt_id);
    if ($result) {
        echo json_encode([
            'success' => true, 
            'state' => $result,
            'count' => get_prompt_save_count($prompt_id)
        ]);
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'failed']);

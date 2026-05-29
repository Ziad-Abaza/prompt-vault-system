<?php
require_once 'bootstrap.php';

$user_id = get_current_user_id();

$data = [
    'categories' => get_categories(),
    'tags' => get_tags(),
    'collections' => get_collections(),
    'prompts' => query("SELECT * FROM prompts WHERE user_id = ?", [$user_id])->fetchAll(),
    'prompt_tags' => query("SELECT pt.* FROM prompt_tags pt JOIN prompts p ON pt.prompt_id = p.id WHERE p.user_id = ?", [$user_id])->fetchAll(),
    'prompt_collections' => query("SELECT pc.* FROM prompt_collections pc JOIN prompts p ON pc.prompt_id = p.id WHERE p.user_id = ?", [$user_id])->fetchAll(),
];

$json = json_encode($data, JSON_PRETTY_PRINT);
$filename = 'prompt_vault_backup_' . get_current_username() . '_' . date('Y-m-d_H-i-s') . '.json';

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($json));

echo $json;
exit;

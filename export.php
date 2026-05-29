<?php
require_once 'bootstrap.php';

$data = [
    'categories' => get_categories(),
    'tags' => get_tags(),
    'collections' => get_collections(),
    'prompts' => query("SELECT * FROM prompts")->fetchAll(),
    'prompt_tags' => query("SELECT * FROM prompt_tags")->fetchAll(),
    'prompt_collections' => query("SELECT * FROM prompt_collections")->fetchAll(),
];

$json = json_encode($data, JSON_PRETTY_PRINT);
$filename = 'prompt_vault_backup_' . date('Y-m-d_H-i-s') . '.json';

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($json));

echo $json;
exit;

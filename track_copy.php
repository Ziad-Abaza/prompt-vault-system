<?php
require_once 'bootstrap.php';

$id = $_GET['id'] ?? null;
if ($id) {
    increment_prompt_copy_count($id);
}
exit;

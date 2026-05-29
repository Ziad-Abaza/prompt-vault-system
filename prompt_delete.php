<?php
require_once 'bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        delete_prompt($id);
        set_flash('Prompt deleted successfully.');
    }
}

redirect('index.php');

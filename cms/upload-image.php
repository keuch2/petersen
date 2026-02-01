<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/upload.php';

$auth = new Auth();
$auth->requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

if (!isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'message' => 'No se recibió ningún archivo']);
    exit;
}

$upload = new Upload();
$result = $upload->uploadImage($_FILES['file']);

echo json_encode($result);

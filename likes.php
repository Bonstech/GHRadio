<?php
header('Content-Type: application/json');

$file = 'likes.txt';
$action = $_GET['action'] ?? '';

try {
    // Get current count
    $count = file_exists($file) ? (int)file_get_contents($file) : 0;
    
    if ($action === 'increment') {
        // Increment count
        $count++;
        file_put_contents($file, $count);
    }
    
    echo json_encode(['count' => $count]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
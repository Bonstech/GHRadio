<?php
// send-voice-message.php - Process voice message submission (COMPLETE FIX)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header - MUST be first
header('Content-Type: application/json');

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Include database files
    require_once 'config/database.php';
    require_once 'includes/functions.php';
    
    $conn = getDB();
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    // Get user info
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $user_name = isset($_POST['user_name']) ? trim($_POST['user_name']) : '';
    $user_email = isset($_POST['user_email']) ? trim($_POST['user_email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $audio_data = isset($_POST['audio_data']) ? $_POST['audio_data'] : '';
    $audio_duration = isset($_POST['audio_duration']) ? intval($_POST['audio_duration']) : 0;
    $file_size = isset($_POST['file_size']) ? intval($_POST['file_size']) : 0;
    
    // For non-logged-in users, name and email are required
    if (!$user_id) {
        if (empty($user_name) || empty($user_email)) {
            echo json_encode(['success' => false, 'message' => 'Please provide your name and email.']);
            exit;
        }
        if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
            exit;
        }
    }
    
    // Validate audio data
    if (empty($audio_data)) {
        echo json_encode(['success' => false, 'message' => 'No audio recording found. Please record a message first.']);
        exit;
    }
    
    // Clean audio data
    $audio_data = preg_replace('/^data:audio\/[^;]+;base64,/', '', $audio_data);
    $audio_data = str_replace(' ', '+', $audio_data);
    $audio_binary = base64_decode($audio_data);
    
    if ($audio_binary === false || empty($audio_binary)) {
        echo json_encode(['success' => false, 'message' => 'Invalid audio data. Please try recording again.']);
        exit;
    }
    
    // Create upload directory if not exists
    $upload_dir = __DIR__ . '/uploads/voice-messages/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $filename = 'voice_' . time() . '_' . bin2hex(random_bytes(8)) . '.webm';
    $filepath = $upload_dir . $filename;
    
    // Save audio file
    if (file_put_contents($filepath, $audio_binary) === false) {
        echo json_encode(['success' => false, 'message' => 'Failed to save audio file.']);
        exit;
    }
    
    // Create table if not exists
    $conn->query("
        CREATE TABLE IF NOT EXISTS voice_messages (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NULL,
            user_name VARCHAR(100),
            user_email VARCHAR(100),
            message TEXT,
            audio_file VARCHAR(255),
            audio_duration INT DEFAULT 0,
            file_size INT DEFAULT 0,
            status ENUM('pending', 'read', 'archived') DEFAULT 'pending',
            ip_address VARCHAR(45),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            read_at TIMESTAMP NULL,
            INDEX idx_user (user_id),
            INDEX idx_status (status)
        )
    ");
    
    // Save to database
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    
    if ($user_id) {
        // For logged-in users: 6 columns
        $stmt = $conn->prepare("
            INSERT INTO voice_messages (user_id, message, audio_file, audio_duration, file_size, ip_address) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        // types: i=integer, s=string, s=string, i=integer, i=integer, s=string
        $stmt->bind_param("issiis", $user_id, $message, $filename, $audio_duration, $file_size, $ip_address);
    } else {
        // For non-logged-in users: 7 columns
        $stmt = $conn->prepare("
            INSERT INTO voice_messages (user_name, user_email, message, audio_file, audio_duration, file_size, ip_address) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        // types: s=string, s=string, s=string, s=string, i=integer, i=integer, s=string
        $stmt->bind_param("ssssiis", $user_name, $user_email, $message, $filename, $audio_duration, $file_size, $ip_address);
    }
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Voice message sent successfully!'
        ]);
    } else {
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    error_log("Voice message error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
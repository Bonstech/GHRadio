<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = intval($_POST['post_id']);
    $comment = sanitize($_POST['comment']);
    
    if (empty($comment)) {
        $_SESSION['error'] = 'Comment cannot be empty.';
        header('Location: article.php?slug=' . $_POST['slug']);
        exit;
    }
    
    $conn = getDB();
    
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment, status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("iis", $post_id, $user_id, $comment);
    } else {
        $name = sanitize($_POST['commenter_name']);
        $email = sanitize($_POST['commenter_email']);
        
        if (empty($name) || empty($email)) {
            $_SESSION['error'] = 'Name and email are required for non-registered users.';
            header('Location: article.php?slug=' . $_POST['slug']);
            exit;
        }
        
        $stmt = $conn->prepare("INSERT INTO comments (post_id, commenter_name, commenter_email, comment, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->bind_param("isss", $post_id, $name, $email, $comment);
    }
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Your comment has been submitted for approval.';
    } else {
        $_SESSION['error'] = 'Failed to submit comment: ' . $conn->error;
    }
    
    header('Location: article.php?slug=' . $_POST['slug']);
} else {
    header('Location: index.php');
}
exit;
?>
<?php
// test-subscribe.php - Direct test without AJAX
require_once 'config/database.php';
require_once 'includes/functions.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $message = 'Email is required';
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email address';
        $message_type = 'error';
    } else {
        try {
            $conn = getDB();
            
            // Check if subscribers table exists
            $conn->query("CREATE TABLE IF NOT EXISTS subscribers (
                id INT PRIMARY KEY AUTO_INCREMENT,
                email VARCHAR(255) UNIQUE NOT NULL,
                subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                status ENUM('active', 'unsubscribed') DEFAULT 'active',
                ip_address VARCHAR(45),
                user_agent TEXT
            )");
            
            // Check if email exists
            $stmt = $conn->prepare("SELECT id, status FROM subscribers WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $subscriber = $result->fetch_assoc();
                if ($subscriber['status'] === 'unsubscribed') {
                    $stmt = $conn->prepare("UPDATE subscribers SET status = 'active', subscribed_at = NOW() WHERE id = ?");
                    $stmt->bind_param("i", $subscriber['id']);
                    $stmt->execute();
                    $message = 'Welcome back! You have been re-subscribed.';
                    $message_type = 'success';
                } else {
                    $message = 'You are already subscribed!';
                    $message_type = 'success';
                }
            } else {
                $ip = $_SERVER['REMOTE_ADDR'] ?? '';
                $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
                $stmt = $conn->prepare("INSERT INTO subscribers (email, ip_address, user_agent) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $email, $ip, $agent);
                $stmt->execute();
                $message = 'Thank you for subscribing! 🎉';
                $message_type = 'success';
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Subscription</title>
    <style>
        body { font-family: Arial; max-width: 500px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input, button { padding: 12px; font-size: 16px; width: 100%; margin: 5px 0; box-sizing: border-box; }
        input { border: 2px solid #ddd; border-radius: 8px; }
        button { background: #F97316; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
        button:hover { background: #EA580C; }
        .success { color: #065F46; background: #ECFDF5; padding: 12px; border-radius: 8px; border: 1px solid #A7F3D0; }
        .error { color: #DC2626; background: #FEF2F2; padding: 12px; border-radius: 8px; border: 1px solid #FECACA; }
        .subscribers { margin-top: 20px; }
        .subscribers table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .subscribers th, .subscribers td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        .subscribers th { background: #f0f0f0; }
    </style>
</head>
<body>
    <div class="card">
        <h1>📧 Test Subscription</h1>
        
        <?php if ($message): ?>
            <div class="<?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Subscribe</button>
        </form>
    </div>
    
    <div class="card subscribers">
        <h2>📋 Current Subscribers</h2>
        <?php
        $conn = getDB();
        $result = $conn->query("SELECT * FROM subscribers ORDER BY subscribed_at DESC");
        if ($result && $result->num_rows > 0):
        ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Subscribed</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo date('M j, Y', strtotime($row['subscribed_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No subscribers yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
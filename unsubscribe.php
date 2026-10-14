<?php
// unsubscribe.php
require_once 'config/database.php';
require_once 'includes/functions.php';

$email = $_GET['email'] ?? '';
$done = false;
$error = '';

if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
        $conn = getDB();
        $stmt = $conn->prepare("UPDATE subscribers SET status = 'unsubscribed' WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $done = $stmt->affected_rows > 0;
        if (!$done) $error = 'Email not found in our list.';
    }
}

$page_title = 'Unsubscribe';
include 'includes/header.php';
?>
<div class="max-w-md mx-auto my-16 px-4">
    <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
        <?php if ($done): ?>
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-2xl text-green-500"></i>
            </div>
            <h1 class="text-xl font-bold text-gray-800 mb-2">Unsubscribed</h1>
            <p class="text-gray-600 text-sm mb-6">You will no longer receive emails from us.</p>
            <a href="<?php echo SITE_URL; ?>" class="inline-block bg-orange-500 text-white px-6 py-2.5 rounded-lg font-semibold">Go Home</a>
        <?php elseif (!empty($email) && !isset($_GET['confirm'])): ?>
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-question text-2xl text-yellow-500"></i>
            </div>
            <h1 class="text-xl font-bold text-gray-800 mb-2">Unsubscribe?</h1>
            <p class="text-gray-600 text-sm mb-4">Confirm you want to unsubscribe:</p>
            <p class="font-semibold text-gray-800 text-sm mb-6"><?php echo htmlspecialchars($email); ?></p>
            <div class="flex gap-2">
                <a href="?" class="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700">Cancel</a>
                <a href="?email=<?php echo urlencode($email); ?>&confirm=yes" 
                   class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg text-sm font-semibold">Unsubscribe</a>
            </div>
        <?php else: ?>
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-times text-2xl text-red-500"></i>
            </div>
            <h1 class="text-xl font-bold text-gray-800 mb-2">Invalid Link</h1>
            <p class="text-gray-600 text-sm mb-6"><?php echo htmlspecialchars($error ?: 'This link is invalid.'); ?></p>
            <a href="<?php echo SITE_URL; ?>" class="inline-block bg-orange-500 text-white px-6 py-2.5 rounded-lg font-semibold">Go Home</a>
        <?php endif; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
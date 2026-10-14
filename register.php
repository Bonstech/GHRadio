<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = sanitize($_POST['firstname']);
    $lastname = sanitize($_POST['lastname']);
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $bio = sanitize($_POST['bio']);
    
    if (empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $conn = getDB();
        
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Username already taken.';
        } else {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $error = 'Email already registered.';
            } else {
                $profile_picture = 'default.png';
                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                    $upload_result = uploadFile($_FILES['profile_picture'], __DIR__ . '/uploads/profiles/');
                    if ($upload_result['success']) {
                        $profile_picture = $upload_result['filename'];
                    }
                }
                
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("
                    INSERT INTO users (firstname, lastname, username, email, password, profile_picture, bio) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("sssssss", $firstname, $lastname, $username, $email, $hashed_password, $profile_picture, $bio);
                
                if ($stmt->execute()) {
                    $success = 'Registration successful! You can now login.';
                    // Clear form
                    $_POST = [];
                } else {
                    $error = 'Registration failed: ' . $conn->error;
                }
            }
        }
    }
}

$page_title = 'Register';
include 'includes/header.php';
?>

<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">
            Create Account
        </h1>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <?php echo $success; ?>
                <p class="mt-2"><a href="login.php" class="text-green-700 font-semibold underline">Login here</a></p>
            </div>
        <?php else: ?>
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">First Name *</label>
                        <input type="text" name="firstname" required 
                               value="<?php echo isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : ''; ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Last Name *</label>
                        <input type="text" name="lastname" required 
                               value="<?php echo isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : ''; ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Username *</label>
                    <input type="text" name="username" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
                </div>
               <div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Password *</label>
    <div class="relative">
        <input type="password" name="password" id="registerPassword" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition pr-10">
        <button type="button" onclick="togglePassword('registerPassword', 'registerPasswordToggle')" 
                id="registerPasswordToggle"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-500 transition"
                title="Show/Hide Password">
            <i class="fas fa-eye"></i>
        </button>
    </div>
    <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password *</label>
    <div class="relative">
        <input type="password" name="confirm_password" id="confirmPassword" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition pr-10">
        <button type="button" onclick="togglePassword('confirmPassword', 'confirmPasswordToggle')" 
                id="confirmPasswordToggle"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-500 transition"
                title="Show/Hide Password">
            <i class="fas fa-eye"></i>
        </button>
    </div>
</div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Profile Picture</label>
                    <input type="file" name="profile_picture" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500">
                    <p class="text-xs text-gray-500 mt-1">Max size: 5MB. Allowed: JPG, PNG, GIF</p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bio (Optional)</label>
                    <textarea name="bio" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500"><?php echo isset($_POST['bio']) ? htmlspecialchars($_POST['bio']) : ''; ?></textarea>
                </div>
                
                <button type="submit" class="w-full btn-primary px-6 py-3 rounded-lg font-semibold text-lg">
                    <i class="fas fa-user-plus mr-2"></i> Register
                </button>
                
                <p class="text-center text-gray-600 text-sm">
                    Already have an account? <a href="login.php" class="text-orange-500 hover:underline font-semibold">Login</a>
                </p>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>
function togglePassword(inputId, toggleId) {
    const input = document.getElementById(inputId);
    const toggle = document.getElementById(toggleId);
    
    if (!input || !toggle) return;
    
    if (input.type === 'password') {
        input.type = 'text';
        toggle.innerHTML = '<i class="fas fa-eye-slash"></i>';
        toggle.classList.add('text-orange-500');
        toggle.classList.remove('text-gray-400');
        toggle.title = 'Hide Password';
    } else {
        input.type = 'password';
        toggle.innerHTML = '<i class="fas fa-eye"></i>';
        toggle.classList.remove('text-orange-500');
        toggle.classList.add('text-gray-400');
        toggle.title = 'Show Password';
    }
}

document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.shiftKey && e.key === 'P') {
        e.preventDefault();
        const activeElement = document.activeElement;
        if (activeElement && activeElement.type === 'password') {
            const parent = activeElement.parentElement;
            const toggleBtn = parent.querySelector('button[onclick*="togglePassword"]');
            if (toggleBtn) {
                toggleBtn.click();
            }
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    console.log('Password toggle functionality loaded!');
});
</script>
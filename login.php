<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
        exit;
    } else {
        header('Location: user/dashboard.php');
        exit;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter username and password.';
    } else {
        $conn = getDB();
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['lastname'] = $user['lastname'];
                $_SESSION['email'] = $user['email'];
                
                if ($user['role'] === 'admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: user/dashboard.php');
                }
                exit;
            } else {
                $error = 'Invalid password.';
            }
        } else {
            $error = 'User not found.';
        }
    }
}

$page_title = 'Login';
include 'includes/header.php';
?>

<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">
            Welcome Back
        </h1>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Username or Email</label>
                <input type="text" name="username" required 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition pr-10">
                    <button type="button" onclick="togglePassword('password', 'passwordToggle')" 
                            id="passwordToggle"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-500 transition">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="w-full btn-primary px-6 py-3 rounded-lg font-semibold text-lg">
                <i class="fas fa-sign-in-alt mr-2"></i> Login
            </button>
            
            <p class="text-center text-gray-600 text-sm">
                Don't have an account? <a href="register.php" class="text-orange-500 hover:underline font-semibold">Register</a>
            </p>
            
            <!--<p class="text-center text-gray-500 text-xs">
                //<a href="forgot-password.php" class="hover:text-orange-500 transition">Forgot Password?</a>
            </p>-->
        </form>
    </div>
</div>

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
    } else {
        input.type = 'password';
        toggle.innerHTML = '<i class="fas fa-eye"></i>';
        toggle.classList.remove('text-orange-500');
        toggle.classList.add('text-gray-400');
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        const form = document.querySelector('form');
        if (form) {
            form.submit();
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>
<?php
require_once 'config.php';
session_start();

$error = '';
$debug_info = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if (!$admin) {
                $error = "Username not found";
                $debug_info = "No user found with username: " . htmlspecialchars($username);
            } else {
                if (password_verify($password, $admin['password'])) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    header('Location: admin_dashboard.php');
                    exit;
                } else {
                    $error = "Invalid password";
                    $debug_info = "Password verification failed for user: " . htmlspecialchars($username);
                }
            }
        } catch (Exception $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - LMS Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Admin Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($error); ?>
                                <?php if (!empty($debug_info)): ?>
                                    <br><small><?php echo htmlspecialchars($debug_info); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        
                        <div class="mt-3 text-center">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <small><strong>Default Login:</strong></small><br>
                                    <small><strong>Username:</strong> admin</small><br>
                                    <small><strong>Password:</strong> admin123</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <a href="debug_admin.php" class="btn btn-outline-secondary btn-sm">Debug Info</a>
                            <a href="setup_admin.php" class="btn btn-outline-warning btn-sm">Setup Admin</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
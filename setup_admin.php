<?php
require_once 'config.php';

echo "<h2>Setting up Admin System</h2>";

try {
    // Create admin_users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<p style='color: green;'>✓ admin_users table created/verified</p>";

    // Delete existing admin user (if any)
    $pdo->exec("DELETE FROM admin_users WHERE username = 'admin'");
    
    // Create fresh password hash
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Insert admin user
    $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
    $stmt->execute(['admin', $password_hash, 'admin@example.com']);
    
    echo "<p style='color: green;'>✓ Admin user created successfully</p>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><strong>Password Hash:</strong> $password_hash</p>";
    
    // Verify the setup
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute(['admin']);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify('admin123', $admin['password'])) {
        echo "<p style='color: green;'>✓ Setup verification successful - login should work now!</p>";
    } else {
        echo "<p style='color: red;'>✗ Setup verification failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<br><a href='admin_login.php'>Go to Admin Login</a>";
?>
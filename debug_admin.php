<?php
require_once 'config.php';

echo "<h2>Admin Login Debug</h2>";

// Check if admin_users table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'admin_users'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ admin_users table exists</p>";
        
        // Check if admin user exists
        $stmt = $pdo->query("SELECT * FROM admin_users");
        $users = $stmt->fetchAll();
        
        echo "<p>Found " . count($users) . " admin users:</p>";
        foreach ($users as $user) {
            echo "<p>Username: " . $user['username'] . " | Email: " . $user['email'] . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ admin_users table does not exist</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
}

// Test password verification
$test_password = 'admin123';
$test_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "<h3>Password Test:</h3>";
echo "<p>Testing password: '$test_password'</p>";
echo "<p>Against hash: $test_hash</p>";
echo "<p>Result: " . (password_verify($test_password, $test_hash) ? "✓ MATCH" : "✗ NO MATCH") . "</p>";

// Create a fresh password hash
$new_hash = password_hash('admin123', PASSWORD_DEFAULT);
echo "<p>Fresh hash for 'admin123': $new_hash</p>";
echo "<p>Fresh hash verification: " . (password_verify('admin123', $new_hash) ? "✓ WORKS" : "✗ FAILED") . "</p>";
?>
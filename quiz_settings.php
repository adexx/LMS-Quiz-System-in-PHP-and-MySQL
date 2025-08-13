<?php
require_once 'config.php';
require_once 'admin_auth.php';
checkAdminAuth();

// Create settings table if it doesn't exist
$pdo->exec("
    CREATE TABLE IF NOT EXISTS system_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
");

// Default settings
$defaultSettings = [
    'site_title' => ['value' => 'LMS Quiz System', 'description' => 'Website title'],
    'default_time_per_question' => ['value' => '60', 'description' => 'Default time per question in seconds'],
    'default_pass_percentage' => ['value' => '60', 'description' => 'Default pass percentage'],
    'allow_retakes' => ['value' => '1', 'description' => 'Allow quiz retakes by default'],
    'max_file_size' => ['value' => '5242880', 'description' => 'Maximum upload file size in bytes (5MB)'],
    'email_notifications' => ['value' => '0', 'description' => 'Send email notifications'],
    'admin_email' => ['value' => 'admin@example.com', 'description' => 'Admin email address']
];

// Insert default settings if they don't exist
foreach ($defaultSettings as $key => $data) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO system_settings (setting_key, setting_value, description) VALUES (?, ?, ?)");
    $stmt->execute([$key, $data['value'], $data['description']]);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST as $key => $value) {
        if ($key != 'submit') {
            $stmt = $pdo->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $key]);
        }
    }
    $success = "Settings updated successfully!";
}

// Get current settings
$stmt = $pdo->query("SELECT * FROM system_settings ORDER BY setting_key");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR + PDO::FETCH_GROUP);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <h1 class="mb-4">System Settings</h1>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="site_title" class="form-label">Site Title</label>
                                <input type="text" class="form-control" id="site_title" name="site_title" 
                                       value="<?php echo htmlspecialchars($settings['site_title'][0]['setting_value'] ?? ''); ?>">
                                <div class="form-text"><?php echo htmlspecialchars($settings['site_title'][0]['description'] ?? ''); ?></div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="default_time_per_question" class="form-label">Default Time per Question (seconds)</label>
                                    <input type="number" class="form-control" id="default_time_per_question" name="default_time_per_question" 
                                           value="<?php echo htmlspecialchars($settings['default_time_per_question'][0]['setting_value'] ?? '60'); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="default_pass_percentage" class="form-label">Default Pass Percentage</label>
                                    <input type="number" class="form-control" id="default_pass_percentage" name="default_pass_percentage" 
                                           value="<?php echo htmlspecialchars($settings['default_pass_percentage'][0]['setting_value'] ?? '60'); ?>" 
                                           min="0" max="100" step="0.01">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="admin_email" class="form-label">Admin Email</label>
                                <input type="email" class="form-control" id="admin_email" name="admin_email" 
                                       value="<?php echo htmlspecialchars($settings['admin_email'][0]['setting_value'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="max_file_size" class="form-label">Max Upload File Size (bytes)</label>
                                <input type="number" class="form-control" id="max_file_size" name="max_file_size" 
                                       value="<?php echo htmlspecialchars($settings['max_file_size'][0]['setting_value'] ?? '5242880'); ?>">
                                <div class="form-text">5242880 bytes = 5MB</div>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="allow_retakes" name="allow_retakes" value="1"
                                       <?php echo ($settings['allow_retakes'][0]['setting_value'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="allow_retakes">
                                    Allow Quiz Retakes by Default
                                </label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" value="1"
                                       <?php echo ($settings['email_notifications'][0]['setting_value'] ?? '0') == '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="email_notifications">
                                    Enable Email Notifications
                                </label>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" name="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
require_once 'config.php';

// Get all available active quizzes with question counts
$stmt = $pdo->query("
    SELECT q.*, 
           (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) as total_questions 
    FROM quizzes q 
    WHERE q.is_active = 1 
    ORDER BY q.created_at DESC
");
$quizzes = $stmt->fetchAll();

// Get some statistics for display
$total_quizzes = $pdo->query("SELECT COUNT(*) FROM quizzes WHERE is_active = 1")->fetchColumn();
$total_questions = $pdo->query("SELECT COUNT(*) FROM questions q JOIN quizzes qz ON q.quiz_id = qz.id WHERE qz.is_active = 1")->fetchColumn();
$total_attempts = $pdo->query("SELECT COUNT(*) FROM quiz_attempts WHERE completed_at IS NOT NULL")->fetchColumn();

// Get recent top scores for leaderboard
$recent_scores = $pdo->query("
    SELECT qa.user_name, qa.score, q.title as quiz_title, qa.completed_at
    FROM quiz_attempts qa 
    JOIN quizzes q ON qa.quiz_id = q.id 
    WHERE qa.completed_at IS NOT NULL 
    ORDER BY qa.score DESC, qa.completed_at DESC
    LIMIT 5
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
            margin-bottom: 50px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,100 0,0 1000,0 1000,80"/></svg>') no-repeat bottom;
            background-size: cover;
        }
        
        .stats-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .quiz-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .quiz-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .admin-panel {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #667eea;
        }
        
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .question-count-selector {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border: 2px solid #e9ecef;
        }
        
        .badge-custom {
            font-size: 0.75em;
            padding: 0.35em 0.65em;
        }
        
        .leaderboard-card {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        
        .quiz-stats {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 15px;
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .floating-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            z-index: 10;
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-graduation-cap text-primary"></i> LMS Quiz System
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#quizzes">
                            <i class="fas fa-list"></i> Available Quizzes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="results.php">
                            <i class="fas fa-chart-bar"></i> View Results
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#leaderboard">
                            <i class="fas fa-trophy"></i> Leaderboard
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog"></i> Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="admin_login.php">
                                <i class="fas fa-sign-in-alt"></i> Admin Login
                            </a></li>
                            <li><a class="dropdown-item" href="admin_dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="manage_quizzes.php">
                                <i class="fas fa-clipboard-list"></i> Manage Quizzes
                            </a></li>
                            <li><a class="dropdown-item" href="admin_results.php">
                                <i class="fas fa-chart-line"></i> View All Results
                            </a></li>
                            <li><a class="dropdown-item" href="import_questions.php">
                                <i class="fas fa-file-import"></i> Import Questions
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Welcome to LMS Quiz System</h1>
                    <p class="lead mb-4">Test your knowledge with our interactive quiz platform. Choose your preferred number of questions, track your progress, and compete with others.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="#quizzes" class="btn btn-light btn-lg">
                            <i class="fas fa-play"></i> Start Quiz
                        </a>
                        <a href="results.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-chart-bar"></i> View Results
                        </a>
                        <a href="#admin-panel" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-cog"></i> Admin Panel
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="position-relative">
                        <i class="fas fa-laptop-code pulse" style="font-size: 8rem; opacity: 0.3;"></i>
                        <div class="floating-badge">
                            <span class="badge bg-warning text-dark">New Features!</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <!-- Statistics Section -->
        <div class="row mb-5">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stats-card text-center">
                    <div class="card-body">
                        <div class="feature-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h3 class="fw-bold gradient-text"><?php echo $total_quizzes; ?></h3>
                        <p class="text-muted mb-0">Available Quizzes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stats-card text-center">
                    <div class="card-body">
                        <div class="feature-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <h3 class="fw-bold gradient-text"><?php echo number_format($total_questions); ?></h3>
                        <p class="text-muted mb-0">Question Bank</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stats-card text-center">
                    <div class="card-body">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="fw-bold gradient-text"><?php echo number_format($total_attempts); ?></h3>
                        <p class="text-muted mb-0">Quiz Attempts</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stats-card text-center">
                    <div class="card-body">
                        <div class="feature-icon">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <h3 class="fw-bold gradient-text">Custom</h3>
                        <p class="text-muted mb-0">Question Count</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaderboard Section -->
        <?php if (!empty($recent_scores)): ?>
        <section id="leaderboard" class="mb-5">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card leaderboard-card">
                        <div class="card-header border-0">
                            <h4 class="mb-0">
                                <i class="fas fa-trophy me-2"></i>Top Performers
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php foreach ($recent_scores as $index => $score): ?>
                                <div class="d-flex align-items-center mb-3 <?php echo $index === 0 ? 'border-bottom pb-3' : ''; ?>">
                                    <div class="me-3">
                                        <?php if ($index === 0): ?>
                                            <i class="fas fa-crown text-warning fa-2x"></i>
                                        <?php elseif ($index === 1): ?>
                                            <i class="fas fa-medal text-light fa-2x"></i>
                                        <?php elseif ($index === 2): ?>
                                            <i class="fas fa-award text-warning fa-2x"></i>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark fs-6"><?php echo $index + 1; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold"><?php echo htmlspecialchars($score['user_name']); ?></div>
                                        <small><?php echo htmlspecialchars($score['quiz_title']); ?></small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold fs-5"><?php echo number_format($score['score'], 1); ?>%</div>
                                        <small><?php echo date('M j', strtotime($score['completed_at'])); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-star text-warning me-2"></i>New Features
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <strong>Custom Question Count:</strong> Choose how many questions to attempt
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <strong>Smart Navigation:</strong> Jump between questions easily
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <strong>Random Selection:</strong> Questions picked from large question banks
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <strong>Detailed Results:</strong> See exactly what you got right/wrong
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <strong>Admin Controls:</strong> Full quiz management system
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Admin Panel Section -->
        <section id="admin-panel" class="admin-panel text-center mb-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h2 class="mb-4">
                        <i class="fas fa-user-shield text-primary"></i> Admin Panel
                    </h2>
                    <p class="lead mb-4">Manage your quiz system with our comprehensive admin tools</p>
                    
                    <div class="row">
                        <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                            <a href="admin_dashboard.php" class="btn btn-outline-primary w-100 py-3 h-100">
                                <i class="fas fa-tachometer-alt d-block mb-2 fa-2x"></i>
                                <small class="fw-bold">Dashboard</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                            <a href="manage_quizzes.php" class="btn btn-outline-primary w-100 py-3 h-100">
                                <i class="fas fa-clipboard-list d-block mb-2 fa-2x"></i>
                                <small class="fw-bold">Manage Quizzes</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                            <a href="import_questions.php" class="btn btn-outline-primary w-100 py-3 h-100">
                                <i class="fas fa-file-import d-block mb-2 fa-2x"></i>
                                <small class="fw-bold">Import Questions</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                            <a href="export_questions.php" class="btn btn-outline-primary w-100 py-3 h-100">
                                <i class="fas fa-file-export d-block mb-2 fa-2x"></i>
                                <small class="fw-bold">Export Questions</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                            <a href="admin_results.php" class="btn btn-outline-primary w-100 py-3 h-100">
                                <i class="fas fa-chart-line d-block mb-2 fa-2x"></i>
                                <small class="fw-bold">View Results</small>
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                            <a href="quiz_settings.php" class="btn btn-outline-primary w-100 py-3 h-100">
                                <i class="fas fa-cogs d-block mb-2 fa-2x"></i>
                                <small class="fw-bold">Settings</small>
                            </a>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="admin_login.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Admin Login
                        </a>
                        <p class="text-muted mt-2 mb-0">
                            <small>Default: admin / admin123</small>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Available Quizzes Section -->
        <section id="quizzes">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-clipboard-list text-primary"></i> Available Quizzes
                </h2>
                <div>
                    <a href="results.php" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-history"></i> View All Results
                    </a>
                                    <a href="admin_login.php" class="btn btn-outline-primary">
                        <i class="fas fa-plus"></i> Add Quiz
                    </a>
                </div>
            </div>
            
            <?php if (empty($quizzes)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                    <h4 class="text-muted mt-3">No Quizzes Available</h4>
                    <p class="text-muted">Check back later or contact the administrator.</p>
                    <a href="admin_login.php" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> Create First Quiz (Admin)
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($quizzes as $quiz): ?>
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="card quiz-card position-relative">
                                <?php if ($quiz['allow_question_selection']): ?>
                                    <div class="floating-badge">
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-sliders-h"></i> Flexible
                                        </span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-quiz"></i> <?php echo htmlspecialchars($quiz['title']); ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?php echo htmlspecialchars($quiz['description']); ?></p>
                                    
                                    <!-- Quiz Statistics -->
                                    <div class="quiz-stats">
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="fw-bold text-primary"><?php echo number_format($quiz['total_questions']); ?></div>
                                                <small class="text-muted">Total Questions</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold text-info"><?php echo $quiz['time_per_question']; ?>s</div>
                                                <small class="text-muted">Time/Question</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold text-success">
                                                    <?php 
                                                    if ($quiz['allow_question_selection']) {
                                                        echo 'Custom';
                                                    } else {
                                                        echo $quiz['questions_to_show'] ?: 'All';
                                                    }
                                                    ?>
                                                </div>
                                                <small class="text-muted">Default Show</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Quiz Settings Badges -->
                                    <div class="mb-3 text-center">
                                        <span class="badge badge-custom bg-<?php echo $quiz['allow_skip'] ? 'success' : 'secondary'; ?> me-1" title="Skip Questions">
                                            <i class="fas fa-forward"></i> <?php echo $quiz['allow_skip'] ? 'Skip OK' : 'No Skip'; ?>
                                        </span>
                                        <span class="badge badge-custom bg-<?php echo $quiz['allow_navigation'] ? 'info' : 'secondary'; ?> me-1" title="Question Navigation">
                                            <i class="fas fa-arrows-alt"></i> <?php echo $quiz['allow_navigation'] ? 'Navigate' : 'Linear'; ?>
                                        </span>
                                        <?php if ($quiz['shuffle_questions']): ?>
                                            <span class="badge badge-custom bg-warning text-dark me-1" title="Questions Shuffled">
                                                <i class="fas fa-random"></i> Shuffled
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($quiz['allow_question_selection']): ?>
                                            <span class="badge badge-custom bg-gradient bg-warning text-dark me-1" title="Custom Question Count">
                                                <i class="fas fa-sliders-h"></i> Custom Count
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($quiz['total_questions'] > 0): ?>
                                        <form action="quiz_setup.php" method="GET" class="quiz-form">
                                            <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                                            
                                            <div class="mb-3">
                                                <label for="user_name_<?php echo $quiz['id']; ?>" class="form-label">
                                                    <i class="fas fa-user text-primary"></i> Your Name:
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="user_name_<?php echo $quiz['id']; ?>" 
                                                       name="user_name" 
                                                       placeholder="Enter your full name"
                                                       required>
                                            </div>
                                            
                                            <?php if ($quiz['allow_question_selection'] && $quiz['total_questions'] > 1): ?>
                                                <div class="question-count-selector">
                                                    <label for="question_count_<?php echo $quiz['id']; ?>" class="form-label">
                                                        <i class="fas fa-list-ol text-success"></i> Number of Questions:
                                                    </label>
                                                    <select class="form-control" id="question_count_<?php echo $quiz['id']; ?>" name="question_count">
                                                        <?php 
                                                        $default_count = $quiz['questions_to_show'] ?: $quiz['total_questions'];
                                                        $max_questions = $quiz['total_questions'];
                                                        
                                                        // Offer different question count options
                                                        $options = [];
                                                        
                                                        // Add common options based on total questions
                                                        if ($max_questions >= 5) $options[] = 5;
                                                        if ($max_questions >= 10) $options[] = 10;
                                                        if ($max_questions >= 15) $options[] = 15;
                                                        if ($max_questions >= 20) $options[] = 20;
                                                        if ($max_questions >= 25) $options[] = 25;
                                                        if ($max_questions >= 30) $options[] = 30;
                                                        if ($max_questions >= 40) $options[] = 40;
                                                        if ($max_questions >= 50) $options[] = 50;
                                                        if ($max_questions >= 75) $options[] = 75;
                                                        if ($max_questions >= 100) $options[] = 100;
                                                        
                                                        // Always include the total and default
                                                        if (!in_array($max_questions, $options)) {
                                                            $options[] = $max_questions;
                                                        }
                                                        if ($default_count != $max_questions && !in_array($default_count, $options)) {
                                                            $options[] = $default_count;
                                                        }
                                                        
                                                        // Remove duplicates and sort
                                                        $options = array_unique($options);
                                                        sort($options);
                                                        ?>
                                                        
                                                        <?php foreach ($options as $option): ?>
                                                            <?php if ($option <= $max_questions): ?>
                                                                <option value="<?php echo $option; ?>" 
                                                                        <?php echo $option == $default_count ? 'selected' : ''; ?>>
                                                                    <?php echo $option; ?> Questions
                                                                    <?php if ($option == $max_questions): ?> (All Available)<?php endif; ?>
                                                                </option>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="form-text">
                                                        <i class="fas fa-info-circle text-info"></i>
                                                        Questions will be randomly selected from <strong><?php echo number_format($quiz['total_questions']); ?></strong> available in the question bank
                                                    </div>
                                                </div>
                                            <?php elseif ($quiz['questions_to_show'] && $quiz['questions_to_show'] < $quiz['total_questions']): ?>
                                                <div class="alert alert-info py-2">
                                                    <small>
                                                        <i class="fas fa-info-circle"></i>
                                                        This quiz will show <strong><?php echo $quiz['questions_to_show']; ?></strong> 
                                                        randomly selected questions from <strong><?php echo $quiz['total_questions']; ?></strong> available.
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                                <i class="fas fa-play"></i> Start Quiz
                                                <?php if ($quiz['allow_question_selection']): ?>
                                                    <span class="badge bg-light text-dark ms-2">Custom</span>
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i> 
                                            No questions available for this quiz.
                                        </div>
                                        <a href="manage_questions.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn btn-outline-secondary w-100">
                                            <i class="fas fa-plus"></i> Add Questions (Admin)
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer text-muted bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small>
                                            <i class="fas fa-clock"></i> Created: <?php echo date('M j, Y', strtotime($quiz['created_at'])); ?>
                                        </small>
                                        <div>
                                            <?php if ($quiz['pass_percentage']): ?>
                                                <small class="me-3">
                                                    <i class="fas fa-target text-success"></i> Pass: <?php echo $quiz['pass_percentage']; ?>%
                                                </small>
                                            <?php endif; ?>
                                            <a href="manage_questions.php?quiz_id=<?php echo $quiz['id']; ?>" 
                                               class="btn btn-outline-primary btn-sm" title="Manage Questions">
                                                <i class="fas fa-cog"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Quick Links Section -->
        <div class="row mt-5 mb-5">
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-user"></i> For Students</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <a href="#quizzes" class="text-decoration-none d-flex align-items-center">
                                    <i class="fas fa-play text-primary me-3 fa-lg"></i>
                                    <div>
                                        <div class="fw-bold">Take Quizzes</div>
                                        <small class="text-muted">Choose your question count and start</small>
                                    </div>
                                </a>
                            </li>
                            <li class="mb-3">
                                <a href="results.php" class="text-decoration-none d-flex align-items-center">
                                    <i class="fas fa-chart-bar text-success me-3 fa-lg"></i>
                                    <div>
                                        <div class="fw-bold">View Results</div>
                                        <small class="text-muted">Check your quiz performance</small>
                                    </div>
                                </a>
                            </li>
                            <li class="mb-0">
                                <a href="result_details.php" class="text-decoration-none d-flex align-items-center">
                                    <i class="fas fa-eye text-info me-3 fa-lg"></i>
                                    <div>
                                        <div class="fw-bold">Detailed Analysis</div>
                                        <small class="text-muted">See what you got right/wrong</small>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-user-cog"></i> For Administrators</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <a href="admin_login.php" class="text-decoration-none d-flex align-items-center">
                                    <i class="fas fa-sign-in-alt text-primary me-3 fa-lg"></i>
                                    <div>
                                        <div class="fw-bold">Admin Login</div>
                                        <small class="text-muted">Access admin dashboard</small>
                                    </div>
                                </a>
                            </li>
                            <li class="mb-3">
                                <a href="manage_quizzes.php" class="text-decoration-none d-flex align-items-center">
                                    <i class="fas fa-clipboard-list text-success me-3 fa-lg"></i>
                                    <div>
                                        <div class="fw-bold">Manage Quizzes</div>
                                        <small class="text-muted">Create & configure quizzes</small>
                                    </div>
                                </a>
                            </li>
                            <li class="mb-0">
                                <a href="import_questions.php" class="text-decoration-none d-flex align-items-center">
                                    <i class="fas fa-file-import text-info me-3 fa-lg"></i>
                                    <div>
                                        <div class="fw-bold">Import/Export</div>
                                        <small class="text-muted">Bulk question management</small>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-star"></i> Key Features</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Custom Question Count</strong>
                                <br><small class="text-muted">Choose 5, 10, 25, 50+ questions</small>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Large Question Banks</strong>
                                <br><small class="text-muted">Import 100+ questions easily</small>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Smart Navigation</strong>
                                <br><small class="text-muted">Jump between questions</small>
                            </li>
                            <li class="mb-0">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Detailed Analytics</strong>
                                <br><small class="text-muted">Track performance & progress</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- How It Works Section -->
        <section class="mb-5">
            <h2 class="text-center mb-5">
                <i class="fas fa-question-circle text-primary"></i> How It Works
            </h2>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="text-center">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; margin-bottom: 20px;">
                            <i class="fas fa-user-plus fa-2x"></i>
                        </div>
                        <h5>1. Enter Your Name</h5>
                        <p class="text-muted">Start by entering your name to track your progress</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="text-center">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; margin-bottom: 20px;">
                            <i class="fas fa-sliders-h fa-2x"></i>
                        </div>
                        <h5>2. Choose Question Count</h5>
                        <p class="text-muted">Select how many questions you want to attempt</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="text-center">
                        <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; margin-bottom: 20px;">
                            <i class="fas fa-play fa-2x"></i>
                        </div>
                        <h5>3. Take the Quiz</h5>
                        <p class="text-muted">Answer questions at your own pace with navigation</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="text-center">
                        <div class="bg-warning text-dark rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; margin-bottom: 20px;">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <h5>4. View Results</h5>
                        <p class="text-muted">Get detailed feedback and track your improvement</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <h5><i class="fas fa-graduation-cap"></i> LMS Quiz System</h5>
                                        <p class="text-muted">A comprehensive quiz management system with flexible question selection from large question banks. Perfect for educational institutions and training centers.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-linkedin fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-github fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3">
                    <h6>Student Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#quizzes" class="text-muted text-decoration-none">Available Quizzes</a></li>
                        <li class="mb-2"><a href="results.php" class="text-muted text-decoration-none">View Results</a></li>
                        <li class="mb-2"><a href="#leaderboard" class="text-muted text-decoration-none">Leaderboard</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Help & FAQ</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-3">
                    <h6>Admin Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="admin_login.php" class="text-muted text-decoration-none">Admin Login</a></li>
                        <li class="mb-2"><a href="admin_dashboard.php" class="text-muted text-decoration-none">Dashboard</a></li>
                        <li class="mb-2"><a href="manage_quizzes.php" class="text-muted text-decoration-none">Manage Quizzes</a></li>
                        <li class="mb-2"><a href="import_questions.php" class="text-muted text-decoration-none">Import/Export</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-3">
                    <h6>Features</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><span class="text-muted">Custom Question Count</span></li>
                        <li class="mb-2"><span class="text-muted">Smart Navigation</span></li>
                        <li class="mb-2"><span class="text-muted">Detailed Analytics</span></li>
                        <li class="mb-2"><span class="text-muted">Bulk Import/Export</span></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-3">
                    <h6>Quick Stats</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <span class="text-muted">
                                <i class="fas fa-clipboard-list text-primary me-1"></i>
                                <?php echo $total_quizzes; ?> Quizzes
                            </span>
                        </li>
                        <li class="mb-2">
                            <span class="text-muted">
                                <i class="fas fa-question-circle text-success me-1"></i>
                                <?php echo number_format($total_questions); ?> Questions
                            </span>
                        </li>
                        <li class="mb-2">
                            <span class="text-muted">
                                <i class="fas fa-users text-info me-1"></i>
                                <?php echo number_format($total_attempts); ?> Attempts
                            </span>
                        </li>
                        <li class="mb-2">
                            <span class="text-muted">
                                <i class="fas fa-clock text-warning me-1"></i>
                                24/7 Available
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 border-secondary">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="text-center text-md-start text-muted">
                        <small>&copy; <?php echo date('Y'); ?> LMS Quiz System. Built with PHP & MySQL.</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-center text-md-end">
                        <small class="text-muted">
                            Version 2.0 | Last Updated: <?php echo date('F Y'); ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="btn btn-primary position-fixed bottom-0 end-0 m-4 rounded-circle" 
            id="backToTop" 
            style="width: 50px; height: 50px; display: none; z-index: 1000;">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Back to top button functionality
        const backToTopButton = document.getElementById('backToTop');
        
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopButton.style.display = 'block';
            } else {
                backToTopButton.style.display = 'none';
            }
        });
        
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Add fade-in animation to cards as they come into view
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards for animation
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.quiz-card, .stats-card');
            cards.forEach((card, index) => {
                // Initial state for animation
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.6s ease ' + (index * 0.1) + 's';
                
                observer.observe(card);
            });
        });

        // Form validation and enhancement
        document.querySelectorAll('.quiz-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const userName = this.querySelector('input[name="user_name"]').value.trim();
                
                if (userName.length < 2) {
                    e.preventDefault();
                    alert('Please enter a valid name (at least 2 characters).');
                    return false;
                }
                
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Starting Quiz...';
                submitBtn.disabled = true;
                
                // Re-enable if something goes wrong
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            });
        });

        // Question count selector enhancement
        document.querySelectorAll('select[name="question_count"]').forEach(select => {
            select.addEventListener('change', function() {
                const selectedCount = this.value;
                const totalQuestions = this.closest('.card-body').querySelector('[data-total-questions]');
                
                // Update visual feedback
                this.style.borderColor = '#28a745';
                this.style.backgroundColor = '#f8fff9';
                
                setTimeout(() => {
                    this.style.borderColor = '';
                    this.style.backgroundColor = '';
                }, 1000);
            });
        });

        // Tooltip initialization for badges and icons
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Auto-refresh stats every 30 seconds (optional)
        let statsRefreshInterval;
        
        function refreshStats() {
            // Only refresh if user is on the page and not taking a quiz
            if (document.visibilityState === 'visible' && !sessionStorage.getItem('quiz_in_progress')) {
                fetch('get_stats.php')
                    .then(response => response.json())
                    .then(data => {
                        // Update stats if they've changed
                        document.querySelectorAll('.gradient-text').forEach((el, index) => {
                            switch(index) {
                                case 0: if (data.total_quizzes !== undefined) el.textContent = data.total_quizzes; break;
                                case 1: if (data.total_questions !== undefined) el.textContent = data.total_questions.toLocaleString(); break;
                                case 2: if (data.total_attempts !== undefined) el.textContent = data.total_attempts.toLocaleString(); break;
                            }
                        });
                    })
                    .catch(error => {
                        console.log('Stats refresh failed:', error);
                        // Disable auto-refresh on error
                        if (statsRefreshInterval) {
                            clearInterval(statsRefreshInterval);
                        }
                    });
            }
        }

        // Start auto-refresh (uncomment if you want this feature)
        // statsRefreshInterval = setInterval(refreshStats, 30000);

        // Keyboard shortcuts for power users
        document.addEventListener('keydown', function(event) {
            // Alt + A for Admin
            if (event.altKey && event.key === 'a') {
                event.preventDefault();
                window.location.href = 'admin_login.php';
            }
            
            // Alt + Q for Quizzes section
            if (event.altKey && event.key === 'q') {
                event.preventDefault();
                document.querySelector('#quizzes').scrollIntoView({ behavior: 'smooth' });
            }
            
            // Alt + R for Results
            if (event.altKey && event.key === 'r') {
                event.preventDefault();
                window.location.href = 'results.php';
            }
        });

        // Show keyboard shortcuts hint after page load
        setTimeout(function() {
            console.log('Keyboard shortcuts: Alt+A (Admin), Alt+Q (Quizzes), Alt+R (Results)');
        }, 2000);

        // Performance monitoring (optional)
        window.addEventListener('load', function() {
            const loadTime = performance.now();
            if (loadTime > 3000) {
                console.warn('Page load time is slow:', Math.round(loadTime) + 'ms');
            }
        });
    </script>

    <!-- Optional: Service Worker for offline support -->
    <script>
        // Register service worker for better performance (optional)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful');
                    })
                    .catch(function(error) {
                        console.log('ServiceWorker registration failed');
                    });
            });
        }
    </script>
</body>
</html>
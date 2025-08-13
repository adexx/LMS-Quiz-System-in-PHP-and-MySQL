<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="admin_dashboard.php">
            <i class="fas fa-graduation-cap"></i> LMS Admin
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="admin_dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_quizzes.php">
                        <i class="fas fa-clipboard-list"></i> Quizzes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_questions.php">
                        <i class="fas fa-question-circle"></i> Questions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_results.php">
                        <i class="fas fa-chart-bar"></i> Results
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-cogs"></i> Tools
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="import_questions.php">Import Questions</a></li>
                        <li><a class="dropdown-item" href="export_questions.php">Export Questions</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="quiz_settings.php">Global Settings</a></li>
                    </ul>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> <?php echo $_SESSION['admin_username']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="index.php" target="_blank">View Site</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="admin_logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
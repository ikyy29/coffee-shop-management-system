<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'food_db';
$sql_file = 'food_db.sql';

$status = 'idle'; // idle, success, error
$message = '';
$error_details = '';

// Check if MySQL is running when user triggers setup
if (isset($_POST['setup'])) {
    try {
        // Connect to MySQL without specifying database first
        $pdo = new PDO("mysql:host=$host", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
        
        // Connect to the database
        $pdo->exec("USE `$db_name`;");
        
        // Read and execute SQL file
        if (file_exists($sql_file)) {
            $sql = file_get_contents($sql_file);
            
            // Execute the schema queries
            $pdo->exec($sql);
            
            $status = 'success';
            $message = 'Database and tables have been successfully imported!';
        } else {
            $status = 'error';
            $message = 'SQL file (food_db.sql) was not found in the root directory.';
        }
    } catch (PDOException $e) {
        $status = 'error';
        $message = 'Failed to connect or initialize the database.';
        $error_details = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop - Database Setup</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f0b08;
            --card-bg: rgba(30, 24, 20, 0.6);
            --border-color: rgba(139, 92, 26, 0.2);
            --primary: #c9883b;
            --primary-hover: #e09d4f;
            --text-main: #f5f0eb;
            --text-muted: #a89f95;
            --success: #3fab60;
            --error: #d94e4e;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(at 0% 0%, rgba(139, 92, 26, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(30, 24, 20, 0.4) 0px, transparent 50%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            max-width: 520px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 40px;
            backdrop-filter: blur(12px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            text-align: center;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #f5f0eb, #c9883b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p.subtitle {
            font-size: 1rem;
            color: var(--text-muted);
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .status-box {
            padding: 24px;
            border-radius: 16px;
            margin-bottom: 30px;
            text-align: left;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .status-box.success {
            background: rgba(63, 171, 96, 0.1);
            border: 1px solid rgba(63, 171, 96, 0.3);
        }

        .status-box.success h3 {
            color: var(--success);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            font-size: 1.15rem;
        }

        .status-box.error {
            background: rgba(217, 78, 78, 0.1);
            border: 1px solid rgba(217, 78, 78, 0.3);
        }

        .status-box.error h3 {
            color: var(--error);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            font-size: 1.15rem;
        }

        .status-box p {
            font-size: 0.95rem;
            color: var(--text-main);
            line-height: 1.5;
        }

        .error-code {
            font-family: monospace;
            background: rgba(0, 0, 0, 0.3);
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 0.8rem;
            color: #d9a3a3;
            word-break: break-all;
            max-height: 120px;
            overflow-y: auto;
        }

        .btn {
            display: inline-block;
            width: 100%;
            padding: 16px 24px;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, var(--primary), #b3732b);
            border: none;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(201, 136, 59, 0.3);
        }

        .btn:hover {
            background: linear-gradient(135deg, var(--primary-hover), var(--primary));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(201, 136, 59, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: none;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            box-shadow: none;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .instruction-list {
            text-align: left;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 30px;
        }

        .instruction-list h4 {
            font-size: 1rem;
            margin-bottom: 12px;
            color: var(--primary);
        }

        .instruction-list ol {
            padding-left: 20px;
        }

        .instruction-list li {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .badge {
            background: rgba(201, 136, 59, 0.15);
            color: var(--primary);
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
            border: 1px solid rgba(201, 136, 59, 0.20);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">☕</div>
        <span class="badge">LARAGON DATABASE SETUP</span>
        <h1>Database Initializer</h1>
        <p class="subtitle">Quickly initialize the <strong>food_db</strong> database for your Coffee Shop Management System.</p>

        <?php if ($status === 'idle'): ?>
            <div class="instruction-list">
                <h4>Prerequisites:</h4>
                <ol>
                    <li>Make sure <strong>Laragon</strong> is running.</li>
                    <li>Click <strong>"Start All"</strong> in the Laragon control panel.</li>
                    <li>Ensure MySQL service is active (port 3306).</li>
                </ol>
            </div>

            <form method="POST">
                <button type="submit" name="setup" class="btn">Initialize Database</button>
            </form>
        <?php elseif ($status === 'success'): ?>
            <div class="status-box success">
                <h3>✓ Success</h3>
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
            <div class="btn-group">
                <a href="home.php" class="btn">Go to Coffee Shop Homepage</a>
                <a href="admin/admin_login.php" class="btn btn-secondary">Go to Admin Dashboard</a>
            </div>
        <?php elseif ($status === 'error'): ?>
            <div class="status-box error">
                <h3>✗ Error</h3>
                <p><?php echo htmlspecialchars($message); ?></p>
                <?php if (!empty($error_details)): ?>
                    <div class="error-code">
                        <?php echo htmlspecialchars($error_details); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="btn-group">
                <form method="POST" style="width: 100%;">
                    <button type="submit" name="setup" class="btn">Try Again</button>
                </form>
                <a href="setup_db.php" class="btn btn-secondary">Refresh Page</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

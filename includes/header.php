<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quiz App</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>

<body>
    <header>
        <h1>Quiz App</h1>
            <nav>
                <a href="<?= BASE_URL ?>">Home</a> | 
                <?php if (isLoggedIn()): ?>
                    <a href="<?= BASE_URL ?>views/dashboard.php">My Dashboard</a> |
                    <a href="<?= BASE_URL ?>actions/logout.php">Logout</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>views/auth/login.php">Login</a> |
                    <a href="<?= BASE_URL ?>views/auth/register.php">Register</a>
                <?php endif; ?>
            </nav>
    </header>
    <main>
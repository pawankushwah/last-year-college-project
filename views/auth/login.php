<?php 
require_once '../../config/config.php';
require_once '../../includes/functions.php';
redirectIfLoggedIn();

include '../../includes/header.php'; 
?>

<h2>Login</h2>

<form action="<?= BASE_URL ?>actions/login_action.php" method="POST">
    <label>Email:</label>
    <input type="email" name="email" required><br><br>

    <label>Password:</label>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>

<p>Don't have an account? <a href="<?= BASE_URL ?>views/auth/register.php">Register</a></p>

<?php include '../../includes/footer.php'; ?>

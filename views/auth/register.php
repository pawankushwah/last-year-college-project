<?php 
require_once '../../config/config.php';
require_once '../../includes/functions.php';
redirectIfLoggedIn();

include '../../includes/header.php';
?>

<h2>Register</h2>

<form action="<?= BASE_URL ?>actions/register_action.php" method="POST">
    <label>Name:</label>
    <input type="text" name="name" required><br><br>

    <label>Email:</label>
    <input type="email" name="email" required><br><br>

    <label>Password:</label>
    <input type="password" name="password" required><br><br>

    <button type="submit">Register</button>
</form>

<p>Already have an account? <a href="<?= BASE_URL ?>views/auth/login.php">Login</a></p>

<?php include '../../includes/footer.php'; ?>
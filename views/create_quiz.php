<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
redirectIfNotLoggedIn();
require_once '../includes/dashboard_header.php';

$user_id = $_SESSION['user_id'];
?>

<h2>Create a New Quiz</h2>

<form action="<?= BASE_URL ?>actions/create_quiz_action.php" method="POST">
    <label>Title:</label>
    <input type="text" name="title" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="4" cols="40" required></textarea><br><br>

    <input type="checkbox" name="show_on_submit" value="off">
    <label>Show Answers on Submit</label><br>
    <p>if you want to show the answers on submit then check this box otherwise it will show the answers after every question</p>

    <button type="submit">Create Quiz</button>
</form>

<?php require_once '../includes/footer.php'; ?>
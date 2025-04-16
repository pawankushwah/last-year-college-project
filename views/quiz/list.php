<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/header.php';

// Fetch quizzes from DB
$stmt = $pdo->prepare("SELECT * FROM quizzes ORDER BY created_at DESC");
$stmt->execute();
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Available Quizzes</h2>

<?php if (count($quizzes) > 0): ?>
    <ul>
        <?php foreach ($quizzes as $quiz): ?>
            <li>
                <strong><?= htmlspecialchars($quiz['title']) ?></strong><br>
                <?= htmlspecialchars($quiz['description']) ?><br>
                <a href="take.php?quiz_id=<?= $quiz['id'] ?>">Take Quiz</a>
            </li>
            <hr>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No quizzes available at the moment.</p>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>

<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
redirectIfNotLoggedIn();
require_once '../includes/dashboard_header.php';
require_once '../includes/theme_switcher.php';

$user_id = $_SESSION['user_id'];
?>

<div class="p-2 min-h-[80vh] flex flex-col items-center justify-center text-sm">
    <form action="<?= BASE_URL ?>actions/create_quiz_action.php" method="POST" class="text-center flex flex-col gap-2 bg-gray-100 dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-2xl p-5 w-full sm:w-3/4 md:w-1/2">
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-5 text-center">Create a New Quiz</h2>

        <label class="input w-full">
            <input type="text" name="title" required placeholder="Type your Title" class="grow">
        </label>

        <label class="fieldset">
            <textarea name="description" rows="4" cols="40" placeholder="Type your Description" required class="textarea h-24 w-full"></textarea>
        </label>

        <label class="fieldset-label text-sm">
            <input type="checkbox" name="show_on_submit" checked="checked" class="checkbox checkbox-info checkbox-xs" />
            Show Answers on Submit
        </label>
        <button type="submit" class="btn btn-outline btn-info">Create Quiz</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
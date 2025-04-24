<?php 
require_once '../../config/config.php';
require_once '../../includes/functions.php';
redirectIfLoggedIn();

include '../../includes/header.php'; 
include '../../includes/theme_switcher.php';
?>

<div class="h-[80vh] p-2 flex flex-col items-center justify-center text-sm">
    <form action="<?= BASE_URL ?>actions/login_action.php" method="POST" class="text-center flex flex-col gap-2 bg-gray-100 dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-2xl p-5 w-full sm:w-3/4 md:w-1/2">
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-5 text-center">Login</h2>

        <label class="input w-full">
            <i class="fa-solid fa-envelope h-[1em] opacity-50"></i>
            <input type="email" name="email" required placeholder="Type your Email" class="grow">
        </label>

        <label class="input w-full">
            <i class="fa-solid fa-lock h-[1em] opacity-50"></i>
            <input type="password" name="password" required placeholder="Enter Password" class="grow">
        </label>

        <button type="submit" class="btn btn-outline btn-info">Login</button>
        <p class="text-sm">Don't have an account? <a href="<?= BASE_URL ?>views/auth/register.php" class="link">Register</a></p>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>

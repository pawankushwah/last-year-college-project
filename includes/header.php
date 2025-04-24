<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quiz App</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CSS Link -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/tailwind.css">
    <!-- Font Awesome Icon Link -->
    <!-- Font Awesome Icon Link -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/fontawesome/css/fontawesome.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/fontawesome/css/solid.css">
</head>
<?php
$uri = $_SERVER['REQUEST_URI'];
$basePath = parse_url(BASE_URL)['path'];
$isHome = $uri === $basePath || $uri === $basePath . '/index.php';
?>

<body class="select-none text-sm sm:text-md lg:text-lg">
    <div class="max-w-6xl mx-auto p-2">
        <header class="flex flex-col bg-blue-500 text-white p-6 dark:bg-gray-900 dark:text-gray-300 rounded-2xl overflow-hidden">
            <nav class="flex justify-around items-center">
                <div>
                    <a href="<?= BASE_URL ?>" class="text-3xl sm:text-4xl lg:text-5xl font-bold"><?= APP_NAME ?></a>
                </div>
                <div class="lg:hidden">
                    <label class="swap swap-rotate">
                        <input id="toggle-navigation" type="checkbox" class="theme-controller">
                        <i class="swap-off text-lg sm:text-xl lg:text-2xl fill-current fa-solid fa-bars"></i>
                        <i class="swap-on text-lg sm:text-xl lg:text-2xl fill-current fa-solid fa-xmark"></i>
                    </label>
                </div>
                <div id="navigation-links-pc" class="gap-6 hidden lg:flex">
                    <?php if (!$isHome): ?>
                        <a href="<?= BASE_URL ?>" class="p-2 rounded-sm hover:text-blue-500 hover:bg-white  dark:hover:text-gray-300 dark:hover:bg-blue-900">
                            <i class="pr-1 text-sm fa-solid fa-house"></i>
                            <span>Home</span>
                        </a>
                    <?php endif; ?>

                    <?php if (isLoggedIn()): ?>
                        <a href="<?= BASE_URL ?>views/dashboard.php" class="p-2 rounded-sm hover:text-blue-500 hover:bg-white dark:hover:text-gray-300 dark:hover:bg-blue-900">
                            <i class="pr-1 text-sm fa-solid fa-user"></i>
                            <span>My Dashboard</span>
                        </a>
                        <a href="<?= BASE_URL ?>actions/logout.php" class="p-2 rounded-sm hover:text-blue-500 hover:bg-white dark:hover:text-gray-300 dark:hover:bg-blue-900">
                            <i class="pr-1 text-sm fa-solid fa-right-from-bracket"></i>
                            <span>Logout</span>
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>views/auth/login.php" class="p-2 rounded-sm hover:text-blue-500 hover:bg-white dark:hover:text-gray-300 dark:hover:bg-blue-900">
                            <i class="pr-1 text-sm fa-solid fa-user"></i>
                            <span>Login</span>
                        </a>
                        <a href="<?= BASE_URL ?>views/auth/register.php" class="p-2 rounded-sm hover:text-blue-500 hover:bg-white dark:hover:text-gray-300 dark:hover:bg-blue-900">
                            <i class="pr-1 text-sm fa-solid fa-user-plus"></i>
                            <span>Register</span>
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
            <div id="navigation-links-mobile" class="flex-col gap-2 mt-10 p-2 text-xl hidden">
                <hr>
                <?php if (!$isHome): ?>
                    <a href="<?= BASE_URL ?>" class="p-1 rounded-sm hover:text-blue-500 hover:bg-white  dark:hover:text-gray-300 dark:hover:bg-blue-900">
                        <i class="text-xl pr-2 fa-solid fa-house"></i>
                        <span>Home</span>
                    </a>
                <?php endif; ?>

                <?php if (isLoggedIn()): ?>
                    <a href="<?= BASE_URL ?>views/dashboard.php" class="p-1 rounded-sm hover:text-blue-500 hover:bg-white dark:hover:text-gray-300 dark:hover:bg-blue-900">
                        <i class="text-xl pr-2 fa-solid fa-user"></i>
                        <span>My Dashboard</span>
                    </a>
                    <a href="<?= BASE_URL ?>actions/logout.php" class="p-1 rounded-sm hover:text-blue-500 hover:bg-white dark:hover:text-gray-300 dark:hover:bg-blue-900">
                        <i class="text-xl pr-2 fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>views/auth/login.php" class="p-1 rounded-sm hover:text-blue-500 hover:bg-white dark:hover:text-gray-300 dark:hover:bg-blue-900">
                        <i class="text-xl pr-2 fa-solid fa-user"></i>
                        <span>Login</span>
                    </a>
                    <a href="<?= BASE_URL ?>views/auth/register.php" class="p-1 rounded-sm hover:text-blue-500 hover:bg-white dark:hover:text-gray-300 dark:hover:bg-blue-900">
                        <i class="text-xl pr-2 fa-solid fa-user-plus"></i>
                        <span>Register</span>
                    </a>
                <?php endif; ?>
            </div>
        </header>
        <div id="start" class="p-10"></div>
        <script>
            const toggleNavigation = document.getElementById("toggle-navigation");
            const navigationLinks = document.getElementById("navigation-links-mobile");
            toggleNavigation.onclick = (e) => {
                if (navigationLinks.classList.contains("hidden")) {
                    navigationLinks.classList.remove("hidden");
                    navigationLinks.classList.add("flex");
                } else {
                    navigationLinks.classList.add("hidden");
                    navigationLinks.classList.remove("flex");
                }
            }
        </script>
        <main>
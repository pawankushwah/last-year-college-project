<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';
require_once '../includes/theme_switcher.php';
?>

<div class="min-h-[80vh] flex justify-center h-screen">
    <div class="text-center my-10">
        <h1 class="text-4xl font-bold mb-10">Team Teenstars</h1>
        <div class="flex justify-center flex-wrap gap-5 p-5">
            <!-- card 1 -->
            <div class="max-w-sm bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 p-10">
                <div class="flex flex-col items-center pb-10">
                    <img class="w-24 h-24 mb-3 rounded-full shadow-lg" src="<?= BASE_URL ?>assets/images/rohit.jpg" alt="Bonnie image" />
                    <h5 class="mb-1 text-xl font-medium text-gray-900 dark:text-white">Rohit Rawat</h5>
                </div>
            </div>
            <!-- card 2 -->
            <div class="max-w-sm bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 p-10">
                <div class="flex flex-col items-center pb-10">
                    <img class="w-24 h-24 mb-3 rounded-full shadow-lg" src="<?= BASE_URL ?>assets/images/pawan.jpg" alt="Bonnie image" />
                    <h5 class="mb-1 text-xl font-medium text-gray-900 dark:text-white">Pawan Kushwah</h5>
                </div>
            </div>
            <!-- card 3 -->
            <div class="max-w-sm bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 p-10">
                <div class="flex flex-col items-center pb-10">
                    <img class="w-24 h-24 mb-3 rounded-full shadow-lg" src="<?= BASE_URL ?>assets/images/tanish.png" alt="Bonnie image" />
                    <h5 class="mb-1 text-xl font-medium text-gray-900 dark:text-white">Tanish Sharma</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once '../includes/footer.php';
?>
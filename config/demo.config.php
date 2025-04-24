<?php
// DB Config
$host = "localhost";
$db = "quiz_app";
$user = "root";
$pass = "";
$models = [
    'FLASH_2' => 'gemini-2.0-flash',
    'FLASH_LITE' => 'gemini-2.0-flash-lite',
    'PRO' => 'gemini-1.5-pro',
    'FLASH_1_5' => 'gemini-1.5-flash',
    'FLASH_1_5_8B' => 'gemini-1.5-flash-8b'
];

// DB Connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection Failed: " . $e->getMessage());
}

// Base URL (adjust if needed)
define("BASE_URL", "http://localhost/quiz_app/");
define("APP_NAME", "Quizeer");
define("GEMINI_API", "<- your-gemini-api-key ->");
define("GEMINI_MODEL", $models['FLASH_2']);
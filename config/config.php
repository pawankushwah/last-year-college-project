<?php
// DB Config
$host = "localhost";
$db = "quiz_app";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection Failed: " . $e->getMessage());
}

// Base URL (adjust if needed)
define("BASE_URL", "http://localhost/quiz_app/");
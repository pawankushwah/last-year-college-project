<?php

function is_session_started()
{
    // for checking the version of PHP and using relevant session functions
    if (php_sapi_name() !== 'cli') {
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            return session_status() === PHP_SESSION_ACTIVE;
        } else {
            return session_id() !== '';
        }
    }
    return false;
}

function isLoggedIn() {
    if(!is_session_started()) {
        session_start();
    }
    return isset($_SESSION['user_id']);
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "views/auth/login.php");
        exit();
    }
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header("Location: " . BASE_URL . "views/dashboard.php");
        exit();
    }
}
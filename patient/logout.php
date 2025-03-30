<?php
session_start();

// Define the absolute path to the root directory
$root_path = "/MindCheck/";

// Unset all session variables
$_SESSION = array();

// Get session parameters 
$params = session_get_cookie_params();

// Delete the actual cookie
setcookie(session_name(),
    '', 
    time() - 42000,
    '/', // Set cookie path to root
    $params["domain"],
    $params["secure"], 
    $params["httponly"]
);

// Destroy session
session_destroy();

// Clear any other cookies that might be set
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000, '/');
        setcookie($name, '', time()-1000, $root_path);
    }
}

// Use absolute path for redirection
header("Location: " . $root_path . "index.php");
exit();
?>

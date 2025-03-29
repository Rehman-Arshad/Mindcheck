<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Get session parameters 
$params = session_get_cookie_params();

// Delete the actual cookie
setcookie(session_name(),
    '', 
    time() - 42000,
    $params["path"], 
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
        setcookie($name, '', time()-1000);
        setcookie($name, '', time()-1000, '/');
    }
}

// Redirect to login page
header("Location: ../login.php");
exit();
?>

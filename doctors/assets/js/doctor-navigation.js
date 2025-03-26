// Doctor section navigation handler
function handleDoctorNavigation(defaultPath = '../index.php') {
    // Get the current page path
    const currentPath = window.location.pathname;
    
    // If we have a previous page and it's within the MindCheck system
    if (document.referrer && document.referrer.includes('MindCheck')) {
        // Check if we're not trying to go back to a patient page
        if (!document.referrer.includes('/patient/')) {
            window.history.back();
        } else {
            // If trying to go to patient section, redirect to doctor's dashboard
            window.location.href = 'index.php';
        }
    } else {
        // Default fallback
        window.location.href = defaultPath;
    }
}

// Function to check if path exists
function pathExists(path) {
    var http = new XMLHttpRequest();
    http.open('HEAD', path, false);
    try {
        http.send();
        return http.status != 404;
    } catch(e) {
        return false;
    }
}

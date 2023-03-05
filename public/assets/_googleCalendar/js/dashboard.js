// Display Snackbar
function displaySnackbar(message, type, next) {
    // Get the snackbar DIV
    var snackbar = document.getElementById('snackbar');
    switch (type) {
        case 'success':
            snackbar.style.backgroundColor = '#59983b';
            break;
        case 'error':
            snackbar.style.backgroundColor = '#dc3545';
            break;

        default:
            break;
    }
    // Add the "show" class to DIV
    snackbar.className = 'show';
    snackbar.innerHTML = message;
    // After 3 seconds, remove the show class from DIV
    setTimeout(function() {
        snackbar.className = snackbar.className.replace('show', '');
        next();
    }, 5000);
}

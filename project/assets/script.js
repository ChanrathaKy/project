
function validateLoginForm() {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailPattern.test(email)) {
        alert('Please enter a valid email address.');
        return false;
    }

    if (password.length < 6) {
        alert('Password must be at least 6 characters long.');
        return false;
    }

    return true; 
}


function confirmAssignment(orderId) {
    return confirm(`Are you sure you want to assign this order (ID: ${orderId})?`);
}
document.getElementById('loginForm')?.addEventListener('submit', function(event) {
    if (!validateLoginForm()) {
        event.preventDefault();
    }
});

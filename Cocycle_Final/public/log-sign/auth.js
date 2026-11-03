document.addEventListener('DOMContentLoaded', () => {
    const profileInput = document.getElementById('profilePicInput');
    const preview = document.getElementById('profilePicPreview');
    profileInput?.addEventListener('change', event => {
        const file = event.target.files?.[0];
        if (file && preview) preview.src = URL.createObjectURL(file);
    });

    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        signupForm.action = '/register';
        signupForm.method = 'POST';
    }

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.action = '/login';
        loginForm.method = 'POST';
    }
});

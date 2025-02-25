// Password Validation
function checkPasswordRequirements() {
    const password = document.getElementById('password').value;
    
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /\d/.test(password),
        special: /[!@#$%^&*:;<>,.?~`-]/.test(password)
    };

    Object.keys(requirements).forEach(key => {
        const element = document.getElementById(key);
        element.classList.toggle('valid', requirements[key]);
        element.classList.toggle('invalid', !requirements[key]);
    });
}

function validatePassword() {
    const password = document.getElementById('password').value;
    return /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*:;<>,.?~`-]).{8,}$/.test(password);
}

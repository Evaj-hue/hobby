// Remove the `updateContactNumber` function since it's no longer needed.
function validateContactNumber() {
    const contactNumber = document.getElementById('contact_number').value;

    // Validate Philippine number format: Starts with 0 and is followed by 10 digits
    if (!/^0\d{10}$/.test(contactNumber)) {
        document.getElementById('contact_number').setCustomValidity(
            'Contact number must start with 0 and be exactly 11 digits long.'
        );
    } else {
        document.getElementById('contact_number').setCustomValidity('');
    }
}
function updateContactNumber() {
    const countryCodeSelect = document.getElementById('country_code');
    const contactNumberInput = document.getElementById('contact_number');

    // Get the selected option and its data-length attribute
    const selectedOption = countryCodeSelect.options[countryCodeSelect.selectedIndex];
    const countryCode = selectedOption.value;

    // Update the placeholder and maxlength attributes
    contactNumberInput.placeholder = `${countryCode} (e.g., ${countryCode} 123456789)`;
    contactNumberInput.maxLength = 11; // Set max length to 11
    contactNumberInput.pattern = `\\d{1,11}`; // Allow up to 11 digits
    contactNumberInput.title = `Enter up to 11 digits for this country.`;
}

// Initialize the contact number input on page load
document.addEventListener('DOMContentLoaded', updateContactNumber);

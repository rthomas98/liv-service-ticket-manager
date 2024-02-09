function validateServiceTicketForm() {
    let customerName = document.getElementById('customer_name').value;
    let customerCity = document.getElementById('customer_city').value;
    let customerState = document.getElementById('customer_state').value.toUpperCase(); // Force uppercase
    let customerZip = document.getElementById('customer_zip').value;
    let customerPhone = document.getElementById('customer_phone').value;

    let customerEmail = document.getElementById('customer_email').value;
    let dateOfServices = document.getElementById('date_of_services').value;
    let unitPrice = document.getElementById('unit_price').value;

    let hasErrors = false;

    // Name Validation
    let customerNameError = document.querySelector('#customer_name + .error-message');
    if (customerName.trim() === '') {
        customerNameError.textContent = "Customer Name is required.";
        hasErrors = true;
    } else if (!/^[a-zA-Z\s]+$/.test(customerName)) {
        customerNameError.textContent = "Customer Name can only contain letters and spaces.";
        hasErrors = true;
    } else {
        customerNameError.textContent = "";
    }

    // City Validation
    let customerCityError = document.querySelector('#customer_city + .error-message');

    if (customerCity.trim() === '') {
        // 'Required' logic if the field cannot be empty
        customerCityError.textContent = 'City is required.';
        hasErrors = true;
    } else if (!/^[a-zA-Z\s\-']+$/.test(customerCity)) {
        // Adjust regex if other characters should be allowed
        customerCityError.textContent = 'City name can only contain letters, spaces, hyphens, and apostrophes.';
        hasErrors = true;
    } else {
        customerCityError.textContent = ''; // Clear any error
    }

    // State Validation
    let customerStateError = document.querySelector('#customer_state + .error-message');

    if (customerState.trim() === '') {
        customerStateError.textContent = 'State is required.';
        hasErrors = true;
    } else if (!/^[A-Z]{2}$/.test(customerState)) {
        customerStateError.textContent = 'Please enter a valid 2-letter US state abbreviation (e.g., CA).';
        hasErrors = true;
    } else {
        customerStateError.textContent = '';
    }

    // Zip Validation
    let customerZipError = document.querySelector('#customer_zip + .error-message');

    if (customerZip.trim() === '') {
        customerZipError.textContent = 'Zip Code is required.';
        hasErrors = true;
    } else if (!/^\d{5}$/.test(customerZip)) {
        customerZipError.textContent = 'Please enter a valid 5-digit US Zip Code.';
        hasErrors = true;
    } else {
        customerZipError.textContent = '';  // Clear error
    }

    // Phone Validation
    let customerPhoneError = document.querySelector('#customer_phone + .error-message');

    if (customerPhone.trim() === '') {
        customerPhoneError.textContent = 'Phone number is required.';
        hasErrors = true; // Assuming you have some global 'hasErrors' management
    } else if (!/^\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}$/.test(customerPhone)) {
        customerPhoneError.textContent = 'Please enter a valid phone number.';
        hasErrors = true;
    } else {
        customerPhoneError.textContent = ''; // Clear error
    }

    // Email Validation
    let customerEmailError = document.querySelector('#customer_email + .error-message');
    if (customerEmail.trim() === '') {
        customerEmailError.textContent = "Email is required.";
        hasErrors = true;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(customerEmail)) {
        customerEmailError.textContent = "Please enter a valid email address.";
        hasErrors = true;
    } else {
        customerEmailError.textContent = "";
    }


    // Date Validation (Assuming simple text input for now)
    let dateError = document.querySelector('#date_of_services + .error-message');
    if (dateOfServices.trim() === '') {
        dateError.textContent = "Date of Services is required.";
        hasErrors = true;
    } else if (!/^\d{4}-\d{2}-\d{2}$/.test(dateOfServices)) {
        dateError.textContent = "Please enter a valid date (YYYY-MM-DD).";
        hasErrors = true;
    } else {
        dateError.textContent = "";
    }

    // Unit Price Validation
    let unitPriceError = document.querySelector('#unit_price + .error-message');
    if (unitPrice.trim() === '') {
        unitPriceError.textContent = "Unit Price is required.";
        hasErrors = true;
    } else if (!/^\d+(\.\d{1,2})?$/.test(unitPrice)) {
        unitPriceError.textContent = "Please enter a valid price (e.g., 12.99).";
        hasErrors = true;
    } else {
        unitPriceError.textContent = "";
    }
}

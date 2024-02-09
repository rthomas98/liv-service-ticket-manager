// Assuming script linked via 'myPluginData' (adjust if different)
const apiKey = myPluginData.apiKey;

function initializeAutocomplete() {
    let input = document.getElementById('delivery_start');
    let autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.addListener('place_changed', function() {
        let place = autocomplete.getPlace();

        // Extract components (optional - if you need them elsewhere)
        let streetNumber = '';
        let streetName = '';
        let city = '';
        let zip = '';
        let state = ''; // Etc., for other components you might care about

        for (let component of place.address_components) {
            if (component.types.includes('street_number')) {
                streetNumber = component.long_name;
            } else if (component.types.includes('route')) {
                streetName = component.long_name;
            } else if (component.types.includes('locality')) {
                city = component.long_name;
            } else if (component.types.includes('postal_code')) {
                zipCode = component.long_name;
            } else if (component.types.includes('administrative_area_level_1')) {
                state = component.short_name;
            }
        }

        // Address Validation
        validateAddress(place.formatted_address);

    });
}

function validateAddress(address) {
    let errorSpan = document.querySelector('#delivery_start + .error-message');

    if (address.trim() === '') {
        displayError('Delivery Start Location is required.');
    } else if (address.length < 10) { // Customize the minimum length
        displayError('Please provide a more descriptive location.');
    } else {
        clearError();
    }
}

function displayError(errorMessage) {
    let errorSpan = document.querySelector('#delivery_start + .error-message');
    errorSpan.textContent = errorMessage;
}

function clearError() {
    let errorSpan = document.querySelector('#delivery_start + .error-message');
    errorSpan.textContent = '';
}

// Initialization after Google Maps library loads
google.maps.event.addDomListener(window, 'load', initializeAutocomplete);

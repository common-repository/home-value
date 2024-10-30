function getAddressComponent (components, type) {
  const component = components.find((c) => c.types.includes(type));
  return component ? component.long_name : '';
}

window.addressUpdated = false;

function initAutocomplete () {
  const addressForm = document.getElementById('8b-addressForm');
  if (!addressForm) {
    return;
  }

  const addressInput = document.getElementById('plainview_sdk_eightb_home_value_form2_inputs_text_address');
  const autocomplete = new google.maps.places.Autocomplete(addressInput);

  autocomplete.addListener('place_changed', () => {
    const place = autocomplete.getPlace();
    console.log(place);
    if (place && place.address_components) {
      const foundAddressInput = document.getElementById('plainview_sdk_eightb_home_value_form2_inputs_hidden_found_address');

      const streetNumber = getAddressComponent(place.address_components, 'street_number');
      const route = getAddressComponent(place.address_components, 'route');
      const postal_code = getAddressComponent(place.address_components, 'postal_code');

      foundAddressInput.value = `${streetNumber} ${route};${postal_code}`;
      console.log(foundAddressInput.value);
      window.addressUpdated = true;
    } else {
      // Clear the found_address input and reset the addressUpdated flag
      document.getElementById('plainview_sdk_eightb_home_value_form2_inputs_hidden_found_address').value = '';
      window.addressUpdated = false;
    }
  });

}



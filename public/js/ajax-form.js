jQuery(document).ready(function ($) {
  function submitForm (form) {
    // console.log('Ajax URL:', home_values_ajax.ajax_url);
    // console.log('Nonce:', home_values_ajax.nonce);
    // console.log('Form data:', form.find('form').serialize());
    $.ajax({
      type: 'POST',
      dataType: 'html',
      url: home_values_ajax.ajax_url,
      data: {
        action: 'home_value_search_form',
        security: home_values_ajax.nonce,
        form_data: form.find('form').serialize()
      },
      success: function (response) {
        // Remove the 'hv-form-loading' class when the request is successful
        $('#8b-home-value').removeClass('hv-form-loading');

        form.html(response);
        initializeNewForm();
      },
      error: function (e) {
        // Remove the 'hv-form-loading' class when an error occurs
        console.log(e);
        $('#8b-home-value').removeClass('hv-form-loading');

        alert('An error occurred. Please try again.');
      }
    });
  }

  function initializeNewForm () {
    $('body').off('submit', '#8b-home-value form');
    $('body').on('submit', '#8b-home-value form', function (e) {
      e.preventDefault();
      const errorMessage = document.getElementById('8b-errorMessage');
      // Add the 'hv-form-loading' class before making the AJAX request
      $('#8b-home-value').addClass('hv-form-loading');
      setTimeout(function () {
        console.log('submitting form');
        // check if address updated 
        if (window.addressUpdated) {
          if (errorMessage) errorMessage.style.display = 'none';
          // errorMessage.style.display = 'none';
          submitForm($('#8b-home-value'));
        } else {
          // Remove the 'hv-form-loading' class when an error occurs
          $('#8b-home-value').removeClass('hv-form-loading');
          if (errorMessage) {
            errorMessage.innerHTML = 'Please select a valid address from the suggestions.';
            errorMessage.style.display = 'block';
          }
          initializeNewForm();
        }
      }, 1000);
    });
  }

  initializeNewForm();
});

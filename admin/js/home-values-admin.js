function addEventListenerIfExists (elementId, event, listener) {
  const element = document.getElementById(elementId);
  if (element) {
    element.addEventListener(event, listener);
  }
}

function toggleLoading (showLoading) {
  const wpbodyContent = document.getElementById('wpbody-content');
  if (showLoading) {
    wpbodyContent.classList.add('hv-loading');
  } else {
    wpbodyContent.classList.remove('hv-loading');
  }
}

addEventListenerIfExists('generate_api_key', 'click', function () {
  toggleLoading(true);
  const xhr = new XMLHttpRequest();
  xhr.open('POST', ajaxurl);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function () {
    if (xhr.status === 200) {
      const createApiKeyResponse = JSON.parse(xhr.responseText);
      document.getElementById('home_values_api_key').value = createApiKeyResponse.license_key;
      // reload page to show new api key
      location.reload();
    }
  };
  xhr.send(encodeURI('action=home_values_generate_api_key'));
});

addEventListenerIfExists('refresh_status', 'click', function () {
  toggleLoading(true);
  const xhr = new XMLHttpRequest();
  xhr.open('POST', ajaxurl);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function () {
    if (xhr.status === 200) {
      toggleLoading(false);
      const refreshStatusResponse = JSON.parse(xhr.responseText);
      document.getElementById('credits-left').innerHTML = refreshStatusResponse.credits;
      document.getElementById('next-refill-date').innerHTML = refreshStatusResponse.next_refill_date;
      document.getElementById('renewal-url').href = refreshStatusResponse.renewal_url;
    }
  };
  xhr.send(encodeURI('action=home_values_refresh_status'));
});

addEventListenerIfExists('test_api_key', 'click', function () {
  toggleLoading(true);
  const xhr = new XMLHttpRequest();
  xhr.open('POST', ajaxurl);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function () {
    toggleLoading(false);
    if (xhr.status === 200) {
      const testApiKeyResponse = JSON.parse(xhr.responseText);
      document.getElementById('test_api_key_result').innerHTML = testApiKeyResponse.message;
    }
  };
  xhr.send(encodeURI('action=home_values_test_api_key'));
});

addEventListenerIfExists('create_shortcode_page', 'click', function () {
  toggleLoading(true);
  const xhr = new XMLHttpRequest();
  xhr.open('POST', ajaxurl);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function () {
    toggleLoading(false);
    if (xhr.status === 200) {
      const createShortcodePageResponse = JSON.parse(xhr.responseText);
      const linkUrl = createShortcodePageResponse.url;
      const linkText = createShortcodePageResponse.title;
      const link = `A new page has been created with the shortcode <code>[8b_home-values]</code> shortcode on it. View <a href="${linkUrl}" target="_blank">${linkText}</a> page`;
      document.getElementById('create_shortcode_page_result').innerHTML = link;
    }
  };
  xhr.send(encodeURI('action=home_values_create_shortcode_page'));
});

addEventListenerIfExists('test_webhooks', 'click', function () {
  toggleLoading(true);
  const xhr = new XMLHttpRequest();
  xhr.open('POST', ajaxurl);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function () {
    toggleLoading(false);
    if (xhr.status === 200) {
      const testWebhooksResponses = JSON.parse(xhr.responseText);
      if (testWebhooksResponses.length === 0) {
        document.getElementById('test_webhooks_result').innerHTML = 'No webhooks to test.';
        return;
      }
      let testWebhooksResponse = '';
      testWebhooksResponses.forEach(function (response) {
        testWebhooksResponse += response.url + '<br>' + response.status + '<br>' + response.message + '<br>';
      });
      document.getElementById('test_webhooks_result').innerHTML = testWebhooksResponse;
    }
  };
  xhr.send(encodeURI('action=home_values_test_webhooks'));
});

addEventListenerIfExists('home_values_delete_log', 'click', function () {
  toggleLoading(true);
  const xhr = new XMLHttpRequest();
  xhr.open('POST', ajaxurl);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function () {
    if (xhr.status === 200) {
      const deleteLogResponse = JSON.parse(xhr.responseText);
      document.getElementById('home_values_delete_log_result').innerHTML = deleteLogResponse.message;
      location.reload();
    }
  };
  xhr.send(encodeURI('action=home_values_delete_log'));
});


addEventListenerIfExists('general_use_network', 'click', function () {
  toggleLoading(true);
  const xhr = new XMLHttpRequest();
  xhr.open('POST', ajaxurl);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function () {
    if (xhr.status === 200) {
      // reload page to show new values
      location.reload();
    }
  };
  xhr.send(encodeURI('action=home_values_general_use_network'));
});


addEventListenerIfExists('emails_use_network', 'click', function () {
  toggleLoading(true);
  const xhr = new XMLHttpRequest();
  xhr.open('POST', ajaxurl);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function () {
    if (xhr.status === 200) {
      // reload page to show new values
      location.reload();
    }
  };
  xhr.send(encodeURI('action=home_values_emails_use_network'));
});

addEventListenerIfExists('forms_use_network', 'click', function () {
  toggleLoading(true);
  const xhr = new XMLHttpRequest();
  xhr.open('POST', ajaxurl);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function () {
    if (xhr.status === 200) {
      // reload page to show new values
      location.reload();
    }
  };
  xhr.send(encodeURI('action=home_values_forms_use_network'));
});
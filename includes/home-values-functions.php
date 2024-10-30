<?php

/**
 * Log messages to a file
 * 
 * @param string $level - debug, info, notice, warning, error, critical, alert, emergency
 * @param string $message
 * @param array $context
 * @param string $channel
 * 
 * @since 3.0.0
 */
function home_values_custom_logger($level, $message, $context = array(), $channel = 'home-values-server')
{
  $logFile = HV_LOG_FILE;

  $dateTime = new DateTime('now', new DateTimeZone('America/Phoenix'));
  $dateTimeFormatted = $dateTime->format('Y-m-d H:i:s');

  $level = strtoupper($level);

  $context = json_encode($context);

  $logMessage = "[$dateTimeFormatted] $channel.$level: $message $context" . PHP_EOL;

  file_put_contents($logFile, $logMessage, FILE_APPEND);
}

/**
 * Log messages to a file
 * 
 * @param string $level - debug, info, notice, warning, error, critical, alert, emergency
 * @param string $message
 * @param array $context
 * 
 * @since 3.0.0
 */
function home_values_log($level, $message, $context = [])
{
  // check if debug setting is on
  if (!HV_DEBUG) {
    return;
  }

  // Use a switch statement to log messages with different severity levels
  switch ($level) {
    case 'debug':
      home_values_custom_logger($level, $message, $context);
      break;
    case 'info':
      home_values_custom_logger($level, $message, $context);
      break;
    case 'notice':
      home_values_custom_logger($level, $message, $context);
      break;
    case 'warning':
      home_values_custom_logger($level, $message, $context);
      break;
    case 'error':
      home_values_custom_logger($level, $message, $context);
      break;
    case 'critical':
      home_values_custom_logger($level, $message, $context);
      break;
    case 'alert':
      home_values_custom_logger($level, $message, $context);
      break;
    case 'emergency':
      home_values_custom_logger($level, $message, $context);
      break;
    default:
      home_values_custom_logger('error', 'Invalid log level: ' . $level . ' - ' . $message, $context);
      break;
  }
}

/**
 * Start session data.
 *
 * @return void
 */
function start_hv_session()
{
  if (!isset($_COOKIE['hv_session_id']) || empty($_COOKIE['hv_session_id'])) {
    $session_id = uniqid('hv_session_', true);
    try {
      setcookie('hv_session_id', $session_id, time() + (86400 * 30), '/');
    } catch (Exception $e) {
      write_log('Error setting cookie: ' . $e->getMessage());
    }
  } else {
    $session_id = $_COOKIE['hv_session_id'];
  }

  if (false === get_transient($session_id)) {
    set_transient($session_id, json_encode([]), 30 * DAY_IN_SECONDS);
  }
}

/**
 * Clears the session data.
 *
 * @return void
 */
function clear_hv_session()
{
  if (isset($_COOKIE['hv_session_id'])) {
    $session_id = $_COOKIE['hv_session_id'];
    // Check if the transient is deleted successfully
    if (delete_transient($session_id)) {
      // Only clear the cookie if the transient deletion was successful
      setcookie('hv_session_id', '', time() - 3600, '/');
    }
  }
}


/**
 * Gets the session data.
 *
 * @return array
 */
function get_hv_session_data_full()
{
  if (!isset($_COOKIE['hv_session_id'])) {
    return null;
  }

  $session_id = $_COOKIE['hv_session_id'];
  $session_data = get_transient($session_id);
  return hv_is_json($session_data) ? json_decode($session_data, true) : $session_data;
}

/**
 * Add session data.
 *
 * @param string $key
 * @param mixed $value
 */
function add_hv_session_data($key, $value)
{
  if (!isset($_COOKIE['hv_session_id'])) {
    return;
  }

  $session_id = $_COOKIE['hv_session_id'];
  $session_data = get_hv_session_data_full();
  $session_data[$key] = $value;

  $encoded_value = is_array($session_data) ? json_encode($session_data) : $session_data;
  set_transient($session_id, $encoded_value, 30 * DAY_IN_SECONDS);
}

/**
 * Gets the old option value.
 * 
 * @param string $option_name
 * @return mixed
 */
function hv_get_old_option($option_name)
{
  // Get the option
  $value = get_option($option_name);
  // if false or empty and is multisite we check the site option
  if ($value == false || $value == '') {
    $value = is_multisite() ? get_site_option($option_name) : false;
  }
  return $value;
}

/**
 * Gets the session data for the given key.
 *
 * @param string $key
 * @return mixed|false
 */
function get_hv_session_data($key)
{
  if (!isset($_COOKIE['hv_session_id'])) {
    return false;
  }

  $session_id = $_COOKIE['hv_session_id'];
  $session_data = get_transient($session_id);

  if (hv_is_json($session_data)) {
    $session_data = json_decode($session_data, true);
  }

  return isset($session_data[$key]) ? $session_data[$key] : false;
}


function home_values_get_setting($settings_tab, $option_name = '', $default = '')
{
  $plugin_name = 'home_values';
  $full_settings_tab = $plugin_name . '_' . $settings_tab;
  $local_options = get_option($full_settings_tab);
  $site_options = get_site_option($full_settings_tab);

  if (empty($option_name)) {
    if (false !== $local_options && !empty($local_options)) {
      // Merge with custom logic
      foreach ($local_options as $key => $value) {
        if (!empty($value)) {
          $site_options[$key] = $value;
        }
      }
      return $site_options;
    }
    return $site_options;
  } else {
    $local_option = $local_options[$option_name] ?? false;
    if (false !== $local_option && !empty($local_option)) {
      return $local_option;
    }
    $site_option = $site_options[$option_name] ?? $default;
    return $site_option;
  }
  return !empty($default) ? $default : false;
}


function home_values_update_setting($settings_tab, $option_name, $option_value)
{
  $plugin_name = 'home_values';

  // Combine the plugin name and the settings tab.
  $full_settings_tab = $plugin_name . '_' . $settings_tab;
  $local_options = get_option($full_settings_tab, array());

  // Update the local options.
  $local_options[$option_name] = $option_value;

  // global options
  // $global_options = ['api_key', 'next_refill_date', 'credits', 'renewal_url'];
  // if (in_array($option_name, $global_options)) {
  //   delete_site_option($full_settings_tab);
  //   update_site_option($full_settings_tab, $local_options);
  // }

  return update_option($full_settings_tab, $local_options);
}

function home_values_get_site_setting($settings_tab, $option_name = '', $default = '')
{
  $plugin_name = 'home_values';

  // Combine the plugin name and the settings tab.
  $full_settings_tab = $plugin_name . '_' . $settings_tab;
  $site_options = get_site_option($full_settings_tab);

  // if the option name is empty, return the entire settings tab.
  if (empty($option_name)) {
    if (false !== $site_options && !empty($site_options)) {
      return $site_options;
    }
  } else {
    $site_options = get_site_option($full_settings_tab);
    $site_option = $site_options[$option_name] ?? $default;
    return $site_option;
  }

  return false;
}

function home_values_update_site_setting($settings_tab, $option_name, $option_value)
{
  $plugin_name = 'home_values';

  // Combine the plugin name and the settings tab.
  $full_settings_tab = $plugin_name . '_' . $settings_tab;
  $site_options = get_site_option($full_settings_tab, array());

  // Update the site options.
  $site_options[$option_name] = $option_value;
  // delete_site_option($full_settings_tab);

  return update_site_option($full_settings_tab, $site_options);
}



function home_values_get_template_part($slug, $name = null, $args = array())
{
  // Define the template file path
  $template_path = HOME_VALUES_PLUGIN_DIR . 'templates/';

  // Extract the variables from the $args array
  if (!empty($args) && is_array($args)) {
    // Todo: remove once done testing
    $template_args = $args;
    extract($args);
  }

  // If the name is specified, try to load a named template first
  if ($name) {
    $file = "{$slug}-{$name}.php";
    if (file_exists($template_path . $file)) {
      require($template_path . $file);
      return;
    }
  }

  // Load the generic template if a named one wasn't found or not specified
  $file = "{$slug}.php";
  if (file_exists($template_path . $file)) {
    require($template_path . $file);
    return;
  }

  // If no templates were found, return false
  return false;
}


function home_values_get_public_js_url()
{
  return HOME_VALUES_PLUGIN_URL . 'public/js/home-values-public.js';
}

function home_values_get_public_js_files_url()
{
  return HOME_VALUES_PLUGIN_URL . 'public/js/';
}


function home_values_plugin_check_version()
{
  $stored_version = get_option('home_values_plugin_version');

  if (false === $stored_version || version_compare(HOME_VALUES_PLUGIN_VERSION, $stored_version, '>')) {
    // The plugin has been updated
    update_option('home_values_plugin_updated', true);
    // Update the stored version in the database
    update_option('home_values_plugin_version', HOME_VALUES_PLUGIN_VERSION);
  }
}

add_action('plugins_loaded', 'home_values_plugin_check_version');


// write_log function
if (!function_exists('write_log')) {
  function write_log($log)
  {
    if (true === WP_DEBUG) {
      if (is_array($log) || is_object($log)) {
        error_log(print_r($log, true));
      } else {
        error_log($log);
      }
    }
  }
}

// Ensure needed settings are set or default
add_action('admin_init', 'home_values_set_required_defaults');
function home_values_set_required_defaults()
{

  // check if it is a wordpress multisite
  if (is_multisite()) {
    // 1. Sender email
    $sender_email = home_values_get_site_setting('emails', 'sender_email');
    write_log('$sender_email');
    write_log($sender_email);
    if (empty($sender_email)) {
      write_log('Updating Sender email');
      home_values_update_site_setting('emails', 'sender_email', 'noreply@' . parse_url(get_option('siteurl'), PHP_URL_HOST));
    }

    // 2. Sender name
    $sender_name = home_values_get_site_setting('emails', 'sender_name');
    if (empty($sender_name)) {
      write_log('Updating Sender name');
      home_values_update_site_setting('emails', 'sender_name', get_bloginfo('name'));
    }

    // 3. New Lead subject
    $new_lead_subject = home_values_get_site_setting('emails', 'new_lead_subject');
    if (empty($new_lead_subject)) {
      write_log('Updating New Lead subject');
      home_values_update_site_setting('emails', 'new_lead_subject', 'New Home Value Generated for [8b_home_value_first_name] [8b_home_value_last_name]!');
    }
  } else {

    // 1. Sender email
    $sender_email = home_values_get_setting('emails', 'sender_email');
    if (empty($sender_email)) {
      home_values_update_setting('emails', 'sender_email', 'noreply@' . parse_url(get_option('siteurl'), PHP_URL_HOST));
    }

    // 2. Sender name
    $sender_name = home_values_get_setting('emails', 'sender_name');
    if (empty($sender_name)) {
      home_values_update_setting('emails', 'sender_name', get_bloginfo('name'));
    }

    // 3. New Lead subject
    $new_lead_subject = home_values_get_setting('emails', 'new_lead_subject');
    if (empty($new_lead_subject)) {
      home_values_update_setting('emails', 'new_lead_subject', 'New Home Value Generated for [8b_home_value_first_name] [8b_home_value_last_name]!');
    }
  }
}

function hv_do_shortcodes($content, $replacements)
{
  foreach ($replacements as $find => $replacement) {
    $content = str_replace(
      '[8b_home_value_' . $find . ']',
      $replacement,
      $content
    );
  }

  foreach ($replacements as $find => $replacement) {
    $content = str_replace(
      '[' . $find . ']',
      $replacement,
      $content
    );
  }

  return do_shortcode($content);
}

function hv_is_json($string)
{
  json_decode($string);
  return (json_last_error() == JSON_ERROR_NONE);
}


function hv_get_address_details($user_address, $api_key)
{
  // URL encode the user address
  $user_address = urlencode($user_address);

  // Geocoding API request
  $geocode_url = "https://maps.googleapis.com/maps/api/geocode/json?address={$user_address}&key={$api_key}";

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => $geocode_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "cache-control: no-cache"
    ),
  ));

  $geocode_response = curl_exec($curl);
  $geocode_err = curl_error($curl);

  if ($geocode_err) {
    echo "cURL Error #:" . $geocode_err;
    return false;
  } else {
    $geocode_response_array = json_decode($geocode_response, true);

    if (isset($geocode_response_array['results'][0]['place_id'])) {
      $place_id = $geocode_response_array['results'][0]['place_id'];

      // Now use the Place Details API
      $place_details_url = "https://maps.googleapis.com/maps/api/place/details/json?placeid={$place_id}&key={$api_key}";

      curl_setopt_array($curl, array(
        CURLOPT_URL => $place_details_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
          "cache-control: no-cache"
        ),
      ));

      $place_details_response = curl_exec($curl);
      $place_details_err = curl_error($curl);

      if ($place_details_err) {
        echo "cURL Error #:" . $place_details_err;
        return false;
      } else {
        $place_details_response_array = json_decode($place_details_response, true);

        if (isset($place_details_response_array['result']['address_components'])) {
          $address_components = $place_details_response_array['result']['address_components'];

          $street_number = '';
          $route = '';
          $postal_code = '';

          foreach ($address_components as $component) {
            if (in_array('street_number', $component['types'])) {
              $street_number = $component['long_name'];
            }

            if (in_array('route', $component['types'])) {
              $route = $component['long_name'];
            }

            if (in_array('postal_code', $component['types'])) {
              $postal_code = $component['long_name'];
            }
          }

          curl_close($curl);
          return "{$street_number} {$route};{$postal_code}";
        }
      }
    }
  }

  curl_close($curl);
  return false;
}


function hv_check_for_streetview($location, $api_key)
{
  // Construct the URL for the Google Maps Static API request
  $api_url = "https://maps.googleapis.com/maps/api/streetview/metadata?location=" . urlencode($location) . "&size=600x300&key=" . $api_key;

  // Perform the API request and get the response
  $response_json = file_get_contents($api_url);

  // Check if the response is valid JSON
  if ($response_json === false) {
    // Handle API request failure
    return false;
  }

  // Decode the JSON response
  $response_data = json_decode($response_json, true);

  // Check if the response contains a pano_id
  if (isset($response_data['pano_id'])) {
    // The response contains a pano_id
    return true;
  } else {
    // The response does not contain a pano_id
    return false;
  }
}


// Warning message for old api key

// 'AIzaSyAiWYbPJcpcZ95q8HLgHTbGNu7zWLBrDxY'

// Step 1: Add admin notice
add_action('admin_notices', 'hv_update_google_api_admin_notice');
add_action('network_admin_notices', 'hv_update_google_api_admin_notice');

function hv_update_google_api_admin_notice()
{

  // Get the googkle api key from the settings
  $gapikey = home_values_get_setting('general', 'google_api_key');

  $shutdown_date = DateTime::createFromFormat('m/d/Y', '08/15/2023');
  $current_date = new DateTime();

  $interval = $current_date->diff($shutdown_date);
  $days_remaining = $interval->days;

  if ($current_date <= $shutdown_date) {
    // before shutdown date
    $message = 'After 8/15/23, you will need a Google Maps API Key for the Home Value plugin to function correctly. Click <a href="https://developers.google.com/maps/documentation/places/web-service/get-api-key" target="_blank">here</a> to generate yours.';
  } else {
    // after shutdown date
    $message = 'As of 8/15/23, a Google Maps API Key is required for the Home Value plugin to function correctly. Click <a href="https://developers.google.com/maps/documentation/places/web-service/get-api-key" target="_blank">here</a> to generate yours.';
  }

  if ($gapikey == 'AIzaSyAiWYbPJcpcZ95q8HLgHTbGNu7zWLBrDxY') {
    echo '<div class="notice notice-error">
              <p>' . $message . '</p>
         </div>';
  } elseif (empty($gapikey)) {
    echo '<div class="notice notice-error">
            <p>You need a Google Maps API Key for the Home Value plugin to function correctly. Click <a href="https://developers.google.com/maps/documentation/places/web-service/get-api-key" target="_blank">here</a> to generate yours.</p>
          </div>';
  }
}

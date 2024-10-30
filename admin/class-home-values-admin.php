<?php
require_once HOME_VALUES_PLUGIN_DIR . 'includes/class-home-values-api.php';


class Home_Values_Admin
{
  private $plugin_name;
  private $plugin_file;
  private $version;
  private $api;

  public function __construct($plugin_name, $plugin_file, $version)
  {
    $this->plugin_name = $plugin_name;
    $this->plugin_file = $plugin_file;
    $this->version = $version;

    $this->api = new Home_Values_Api();

    // Ajax actions - General Settings
    add_action('wp_ajax_nopriv_home_values_generate_api_key', array($this, 'generate_api_key'));
    add_action('wp_ajax_home_values_generate_api_key', array($this, 'generate_api_key'));
    add_action('wp_ajax_nopriv_home_values_test_api_key', array($this, 'test_api_key'));
    add_action('wp_ajax_home_values_test_api_key', array($this, 'test_api_key'));
    add_action('wp_ajax_nopriv_home_values_refresh_status', array($this, 'refresh_status'));
    add_action('wp_ajax_home_values_refresh_status', array($this, 'refresh_status'));
    add_action('wp_ajax_nopriv_home_values_create_shortcode_page', array($this, 'create_shortcode_page'));
    add_action('wp_ajax_home_values_create_shortcode_page', array($this, 'create_shortcode_page'));
    add_action('wp_ajax_nopriv_home_values_test_webhooks', array($this, 'test_webhooks'));
    add_action('wp_ajax_home_values_test_webhooks', array($this, 'test_webhooks'));
    add_action('wp_ajax_nopriv_home_values_general_use_network', array($this, 'general_use_network'));
    add_action('wp_ajax_home_values_general_use_network', array($this, 'general_use_network'));

    // Ajax actions - Forms
    add_action('wp_ajax_nopriv_home_values_forms_use_network', array($this, 'forms_use_network'));
    add_action('wp_ajax_home_values_forms_use_network', array($this, 'forms_use_network'));

    // Ajax actions - Emails
    add_action('wp_ajax_nopriv_home_values_emails_use_network', array($this, 'emails_use_network'));
    add_action('wp_ajax_home_values_emails_use_network', array($this, 'emails_use_network'));

    // Ajax actions - Debug
    add_action('wp_ajax_home_values_delete_log', array($this, 'delete_log'));

    // Ajax actions - Uninstall
    add_action('wp_ajax_home_values_uninstall', array($this, 'home_values_uninstall'));
  }

  public function enqueue_styles()
  {
    wp_enqueue_style(
      $this->plugin_name,
      HOME_VALUES_PLUGIN_URL . 'admin/css/home-values-admin.css',
      array(),
      $this->version,
      'all'
    );
  }

  public function enqueue_scripts()
  {
    wp_enqueue_script(
      $this->plugin_name,
      HOME_VALUES_PLUGIN_URL . 'admin/js/home-values-admin.js',
      array('jquery'),
      $this->version,
      true
    );
  }

  public function add_plugin_admin_menu()
  {
    add_submenu_page(
      'edit.php?post_type=8b_hv_lead',
      __('Settings', 'home-values'),
      __('Settings', 'home-values'),
      'manage_options',
      $this->plugin_name,
      array($this, 'display_plugin_settings_page')
    );
  }

  public function add_plugin_network_admin_menu()
  {
    add_submenu_page(
      'settings.php',
      __('8b Home Value Settings', 'home-values'),
      __('8b Home Value', 'home-values'),
      'manage_network_options',
      $this->plugin_name,
      array($this, 'display_plugin_settings_page')
    );
  }

  public function display_plugin_settings_page()
  {
    include_once HOME_VALUES_PLUGIN_DIR . 'admin/partials/home-values-admin-display.php';
  }


  public function generate_api_key()
  {
    $plugin_name = 'home_values';
    // get wordpress user email
    $user_email = wp_get_current_user()->user_email;
    // get the sites url
    $site_url = get_site_url();
    // generate a unique key
    $site_key = uniqid();

    $response = $this->api->create_access_key($site_key, $user_email, $site_url);
    write_log($response);

    // Combine the plugin name and the settings tab.
    $full_settings_tab = $plugin_name . '_general';
    // if (is_multisite() && is_network_admin()) {
    $site_options = get_site_option($full_settings_tab, array());
    write_log($site_options);
    // $site_options['google_api_key'] = $response['google_api_key'];
    $site_options['api_key'] = $response['license_key'];
    $site_options['credits'] = $response['credits'];
    $site_options['next_refill_date'] = $response['next_refill_date'];
    $site_options['renewal_url'] = 'https://homevalueplugin.com/renew?key=' . $response['license_key'];
    // delete_site_option($full_settings_tab);
    update_site_option($full_settings_tab, $site_options);
    // } else {
    $local_options = get_option($full_settings_tab, array());
    // $local_options['google_api_key'] = $response['google_api_key'];
    $local_options['api_key'] = $response['license_key'];
    $local_options['credits'] = $response['credits'];
    $local_options['next_refill_date'] = $response['next_refill_date'];
    $local_options['renewal_url'] = 'https://homevalueplugin.com/renew?key=' . $response['license_key'];
    // delete_option($full_settings_tab);
    update_option($full_settings_tab, $local_options);
    // }

    $api_key_response = json_encode($response);

    echo $api_key_response;
    wp_die();
  }

  public function test_api_key()
  {
    $license_key = home_values_get_setting('general', 'api_key');
    $response = $this->api->search_valuations($license_key, '7220 W Bloomfield Rd', 85381);

    if ($response['status'] == 'error' || isset($response['error'])) {
      $response['message'] = 'Invalid API Key';
    } else {
      if ($response['valuation_found']) {
        $response['message'] = 'API Key is valid.';

        home_values_update_setting('general', 'credits', $response['credits_remaining']);
        home_values_update_setting('general', 'next_refill_date', $response['next_refill_date']);
      } else {
        $response['message'] = 'API Key is invalid';
      }
    }

    $test_result = json_encode($response);

    echo $test_result;
    wp_die();
  }

  public function refresh_status()
  {
    // first check for site api_key


    $api_key = home_values_get_site_setting('general', 'api_key') ? home_values_get_site_setting('general', 'api_key') : home_values_get_setting('general', 'api_key');

    $response = $this->api->check_status($api_key);

    write_log($response);

    if (home_values_get_site_setting('general', 'api_key')) {
      home_values_update_site_setting('general', 'credits', $response['credits']);
      home_values_update_site_setting('general', 'next_refill_date', $response['next_refill_date']);
      home_values_update_site_setting('general', 'renewal_url', 'https://homevalueplugin.com/renew?key=' . $api_key);
      home_values_update_site_setting('general', 'cancel_url', 'https://homevalueplugin.com/cancel?key=' . $api_key);
      home_values_update_site_setting('general', 'auto_refill_enabled', $response['auto_refill_enabled']);
      home_values_update_site_setting('general', 'credits_in_package', $response['credits_in_package']);
      home_values_update_site_setting('general', 'credits_low', $response['credits_low']);
    } else {
      home_values_update_setting('general', 'credits', $response['credits']);
      home_values_update_setting('general', 'next_refill_date', $response['next_refill_date']);
      home_values_update_setting('general', 'renewal_url', 'https://homevalueplugin.com/renew?key=' . $api_key);
      home_values_update_setting('general', 'cancel_url', 'https://homevalueplugin.com/cancel?key=' . $api_key);
      home_values_update_setting('general', 'auto_refill_enabled', $response['auto_refill_enabled']);
      home_values_update_setting('general', 'credits_in_package', $response['credits_in_package']);
      home_values_update_setting('general', 'credits_low', $response['credits_low']);
    }

    $status = json_encode(array(
      'credits' => $response['credits'],
      'next_refill_date' => date('Y-m-d', $response['next_refill_date']),
      'status' => $response['status'],
      'timestamp' => $response['timestamp'],
      'renewal_url' => 'https://homevalueplugin.com/renew?key=' . $api_key,
      'auto_refill_enabled' => $response['auto_refill_enabled'],
      'credits_in_package' => $response['credits_in_package'],
      'credits_low' => $response['credits_low'],
      'cancel_url' => 'https://homevalueplugin.com/cancel?key=' . $api_key,
    ));
    echo $status;
    wp_die();
  }

  public function create_shortcode_page()
  {
    $page_title = 'Search Home Values';
    $page_content = '[8b_home_value]';

    // Check if the page already exists
    $page = get_page_by_title($page_title);
    $user_id = get_current_user_id();

    if (!isset($page)) {
      // Create post object
      $page = array(
        'post_title' => $page_title,
        'post_content' => $page_content,
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => $user_id,
      );

      // Insert the post into the database
      $page_id = wp_insert_post($page);
    } else {
      // The page already exists
      $page_id = $page->ID;
    }

    // Return the page id, url and title 
    $new_page = json_encode(array(
      'id' => $page_id,
      'url' => get_page_link($page_id),
      'title' => $page_title,
    ));

    echo $new_page;
    wp_die();
  }

  public function test_webhooks()
  {
    $webhooks_string = home_values_get_setting('general', 'webhooks');

    if (!$webhooks_string) {
      echo json_encode(array());
      wp_die();
    }
    $webhooks = preg_split('/\r\n|\r|\n/', $webhooks_string);

    $test_data = array(
      'lead_first_name' => 'Test',
      'lead_last_name' => 'Lead',
      'lead_phone' => '555-555-5555',
      'lead_email' => 'someone@example.com',
    );
    $test_lead = new Home_Values_Lead(null, $test_data, 'This is a test lead created for testing webhooks');

    $webhook_responses = $this->api->send_lead_to_webhooks($test_lead, $webhooks);

    home_values_log('info', 'Test Webhook responses', $webhook_responses);

    echo json_encode($webhook_responses);

    wp_die();
  }

  public function delete_log()
  {
    $log_file = HV_LOG_FILE;
    if (file_exists($log_file)) {
      $deleted = unlink($log_file);
      if ($deleted) {
        echo json_encode(array(
          'status' => 'success',
          'message' => 'Log file deleted successfully',
        ));
      } else {
        echo json_encode(array(
          'status' => 'error',
          'message' => 'Log file could not be deleted',
        ));
      }
    } else {
      echo json_encode(array(
        'status' => 'error',
        'message' => 'Log file does not exist',
      ));
    }
    wp_die();
  }

  public function home_values_uninstall()
  {
    // Verify nonce
    check_ajax_referer('home_values_uninstall_nonce', 'nonce');

    // Check if the current user has the necessary capability
    if (!current_user_can('activate_plugins')) {
      wp_send_json_error('Insufficient permissions to uninstall the plugin.');
      exit;
    }

    // Remove plugin data from the database
    delete_option('home_values_general');
    delete_option('home_values_forms');
    delete_option('home_values_emails');
    delete_option('home_values_debug');
    delete_option('home_values_plugin_version');

    delete_site_option('home_values_general');
    delete_site_option('home_values_forms');
    delete_site_option('home_values_emails');
    delete_site_option('home_values_debug');

    // Deactivate the plugin
    deactivate_plugins(plugin_basename($this->plugin_file));

    // Send success response with plugins page URL
    wp_send_json_success(array(
      'message' => __('Plugin uninstalled successfully.', 'home-values'),
      'redirect_url' => admin_url('plugins.php'),
    ));

    exit;
  }

  public function general_use_network()
  {
    // delete options for home_values_general
    delete_option('home_values_general');

    wp_send_json_success(array(
      'message' => __('Now using the network options', 'home-values'),
    ));
    wp_die();
  }

  public function forms_use_network()
  {
    // delete options for home_values_forms
    delete_option('home_values_forms');

    wp_send_json_success(array(
      'message' => __('Now using the network options', 'home-values'),
    ));
    wp_die();
  }

  public function emails_use_network()
  {
    // delete options for home_values_emails
    delete_option('home_values_emails');

    wp_send_json_success(array(
      'message' => __('Now using the network options', 'home-values'),
    ));
    wp_die();
  }
}

<?php

use WpOrg\Requests\Session;

require_once HOME_VALUES_PLUGIN_DIR . 'includes/class-home-values-api.php';
require_once HOME_VALUES_PLUGIN_DIR . 'includes/class-home-values-lead.php';

class Home_Values_Shortcodes
{

  private $hv_api;

  public function __construct()
  {
    $this->hv_api = new Home_Values_API();

    // add the shortcode
    add_shortcode('8b_home_value', array($this, 'generate_8b_home_value_shortcode'));

    // Enqueue the ajax scripts for form submission and security
    add_action('wp_enqueue_scripts', array($this, 'enqueue_ajax_scripts'));

    // Ajax action for form submission
    add_action('wp_ajax_home_value_search_form', array($this, 'ajax_generate_8b_home_value_shortcode'));
    add_action('wp_ajax_nopriv_home_value_search_form', array($this, 'ajax_generate_8b_home_value_shortcode'));

    // set cookie
    add_action('plugins_loaded', array($this, 'set_session_cookie'), 1);
  }

  public function set_session_cookie()
  {
    start_hv_session();
  }

  public function enqueue_ajax_scripts()
  {
    wp_enqueue_script('home-values-ajax', home_values_get_public_js_files_url() . 'ajax-form.js', array('jquery'), false, true);
    wp_localize_script('home-values-ajax', 'home_values_ajax', array(
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('home_value_search_form_nonce'),
    ));
  }

  public function ajax_generate_8b_home_value_shortcode()
  {
    check_ajax_referer('home_value_search_form_nonce', 'security');
    $form_data = isset($_POST['form_data']) ? $_POST['form_data'] : null;
    parse_str($form_data, $parsed_form_data);
    echo $this->generate_8b_home_value_shortcode($parsed_form_data['8b_home_value'], true);
    wp_die();
  }


  public function generate_8b_home_value_shortcode($ajax_form_data = null, $ajax = false)
  {

    $form_settings = home_values_get_setting('forms');
    $session_data = get_hv_session_data_full();
    $google_api_key = home_values_get_setting('general', 'google_api_key') ? home_values_get_setting('general', 'google_api_key') : '';

    $args = [
      'google_api_key' => $google_api_key,
      'js'              => home_values_get_public_js_url(),
    ];

    // merge the form settings with the args
    $args = $form_settings ?  array_merge($args, $form_settings) : $args;
    $form_data = $ajax_form_data ? $ajax_form_data : (isset($_POST['8b_home_value']) ? $_POST['8b_home_value'] : null);

    // Determine the current page of the form
    if (empty($form_data)) {
      // clear_hv_session();
      $page = 'initial';
    } else {
      if (!isset($form_data['lead_email']) && (isset($form_data['found_address']) || isset($form_data['address']))) {
        $page = 'lead_info';
      } else {
        $page = 'valuation';
      }
    }

    ob_start();

    switch ($page) {
      case 'initial':
        echo '<div id="8b-home-value">';
        home_values_get_template_part('forms/address-search-page', null, $args);
        echo '</div>';
        break;

      case 'lead_info':
        add_hv_session_data('searched_address', $form_data['address']);

        $foundAddressInput = hv_get_address_details($form_data['address'], $google_api_key);

        if ($foundAddressInput !== false) {
          $valuation = $this->search_for_address($foundAddressInput);
          // echo $foundAddressInput;
        } else {
          $valuation = $this->search_for_address($form_data['found_address']);
        }

        // check if streetview exists
        $streetview_exists = hv_check_for_streetview($valuation['address']['street'] . ', ' . $valuation['address']['zip'], $google_api_key);
        $args['streetview_exists'] = $streetview_exists;

        if (isset($valuation) && $valuation) {
          // Add valuation to session
          add_hv_session_data('valuation', $valuation);
          add_hv_session_data('valuation_found', true);

          write_log($valuation);

          // Update the args for templates
          $args['valuation_found'] = true;
          if ($streetview_exists) {
            $args['address_street_view'] = sprintf(
              'https://maps.googleapis.com/maps/api/streetview?location=%s,%s&size=600x300&key=%s',
              $valuation['address']['street'],
              $valuation['address']['zip'],
              $google_api_key
            );
          } else {
            $args['address_street_view'] = $form_data['address'];
          }

          $args['data_address'] = sprintf('%s, %s', $valuation['address']['street'], $valuation['address']['city']);
        } else {
          // set the cookie to show the no address page
          add_hv_session_data('valuation_found', false);
          $args['valuation_found'] = false;
          $args['address_street_view'] = $form_data['address'];
        }

        $args['lead_form'] = $this->get_lead_form_markup($args);

        home_values_get_template_part('forms/lead-info-page', null, $args);

        break;

      case 'valuation':
        // Check if the valuation was found
        if (!get_hv_session_data('valuation_found')) {
          $args['valuation_found'] = false;
        } else {
          $args['valuation_found'] = true;
          $args['valuation'] = get_hv_session_data('valuation');
        }

        // We got here so we can create the lead and send it to the webhooks
        // Lest get the form data
        $lead_data = [];
        foreach ($form_data as $key => $value) {
          if (strpos($key, 'lead_') === false)
            continue;
          $value = sanitize_text_field($value);
          $value = stripslashes($value);
          $lead_data[$key] = $value;
        }

        // merge the session data with the lead data
        $lead_session_data = $session_data ? array_merge($lead_data, $session_data) : $lead_data;

        // Prepare the lead content using a template
        ob_start();
        home_values_get_template_part('lead-description', null, $lead_session_data);
        $lead_content = do_shortcode(ob_get_clean());

        // Create the lead
        $lead = $this->create_lead($lead_data, $lead_content);
        $args['lead_id'] = $lead->id;

        if ($lead->id) {
          // Tag the lead
          $tag = $args['valuation_found'] ? 'Address found' : 'Address not found';
          $lead->tag_lead($lead->id, $tag);

          // Send the lead to the webhooks
          $this->send_lead_to_webhooks($lead);

          // Broadcast the lead to the 
          $this->broadcast_lead($lead->id);

          // Get template for the lead email
          ob_start();
          home_values_get_template_part('emails/lead-email', null, $lead_session_data);
          $lead_email_content = do_shortcode(ob_get_clean());

          // Send lead email
          $this->send_lead_email($lead_session_data, $lead_email_content);

          do_action('home_values_lead_created', $lead->id);
        }

        // Pass the data to the results page template
        home_values_get_template_part('forms/results-page', null, $args);

        // Clear the session
        clear_hv_session();

        break;

      default:
        break;
    }

    $output = ob_get_contents();
    ob_end_clean();
    return do_shortcode($output);
  }

  public function get_lead_form_markup($args)
  {
    ob_start();
    home_values_get_template_part('forms/lead-form', null, $args);
    return do_shortcode(ob_get_clean());
  }

  /**
   * Creates and saves a lead.
   * 
   * @param array $lead_data
   * @return Lead
   */
  public function create_lead($lead_data, $session_data = null)
  {
    // Pass the form data to the Home_Values_Lead class method
    $lead = new Home_Values_Lead(null, $lead_data, $session_data);

    // if the lead is a wp_error return false
    if (is_wp_error($lead)) {
      return false;
    }

    return $lead;
  }

  /**
   * Searches for the address.
   * 
   * @param string $address
   * @return array
   */
  public function search_for_address($address)
  {

    // Get the license key from the plugin settings
    $license_key = home_values_get_setting('general', 'api_key');

    // Get the street and zipcode from the address
    $street = explode(';', $address)[0];
    $zipcode = explode(';', $address)[1];

    // Use the Home_Values_API class method to search for the address
    $response = $this->hv_api->search_valuations($license_key, $street, $zipcode);

    if (isset($response['error']) || $response['status'] == 400) {
      return false;
    }

    if (isset($response['valuation_found'])) {
      $valuation = $response;
    } else {
      return false;
    }

    // adjust the prices based on the settings
    $adjust_by_percent = home_values_get_setting('general', 'adjust_values');
    if ($adjust_by_percent && $adjust_by_percent != 0) {
      $valuation['valuation']['valuation_emv'] = $valuation['valuation']['valuation_emv'] * (1 + $adjust_by_percent / 100);
      $valuation['valuation']['valuation_high'] = $valuation['valuation']['valuation_high'] * (1 + $adjust_by_percent / 100);
      $valuation['valuation']['valuation_low'] = $valuation['valuation']['valuation_low'] * (1 + $adjust_by_percent / 100);
    }

    return $valuation;
  }

  /**
   * Sends the lead to the webhooks.
   * 
   * @param Lead $lead
   * @return void
   */
  public function send_lead_to_webhooks($lead)
  {
    // Get the webhooks from the plugin settings
    $webhooks_string = home_values_get_setting('general', 'webhooks');

    // If there are no webhooks, return
    if (!$webhooks_string)
      return;

    // Convert the string to an array by splitting it on new lines
    $webhooks = preg_split('/\r\n|\r|\n/', $webhooks_string);

    // send leads to webhooks
    $webhook_responses = $this->hv_api->send_lead_to_webhooks($lead, $webhooks);

    // Save the webhook responses to the log
    home_values_log('info', 'Webhook responses', $webhook_responses);
  }

  /**
   * Sends the lead email.
   * 
   * @param Lead $lead
   * @param string $lead_email_content
   * @return void
   */
  public function send_lead_email($lead_session_data, $lead_email_content)
  {
    $replacement_tags = $this->get_replacement_tags($lead_session_data);
    $this->send_email_to_lead($lead_session_data, $lead_email_content, $replacement_tags);
    try {
      $this->send_email_to_admin($lead_session_data, $replacement_tags);
    } catch (\Throwable $th) {
      write_log($th);
      //throw $th;
    }
  }

  private function get_replacement_tags($lead_session_data)
  {
    $valuation = isset($lead_session_data['valuation']) ? number_format($lead_session_data['valuation']['valuation']['valuation_emv']) : 'N/A';
    return array(
      '[8b_home_value_first_name]' => $lead_session_data['lead_first_name'],
      '[8b_home_value_last_name]' => $lead_session_data['lead_last_name'],
      '[8b_home_value_email]' => $lead_session_data['lead_email'],
      '[8b_home_value_phone]' => $lead_session_data['lead_phone'],
      '[8b_home_value_data_size]' => isset($lead_session_data['valuation']) ? $lead_session_data['valuation']['attributes']['size'] : 'N/A ',
      '[8b_home_value_data_beds]' => isset($lead_session_data['valuation']) ? $lead_session_data['valuation']['attributes']['bedrooms'] : 'N/A',
      '[8b_home_value_data_baths]' => isset($lead_session_data['valuation']) ? $lead_session_data['valuation']['attributes']['bathrooms'] : 'N/A',
      '[8b_home_value_searched_address]' => $lead_session_data['searched_address'],
      '[8b_home_value_data_valuation_medium]' => $valuation,
    );
  }

  private function send_email_to_lead($lead_session_data, $lead_email_content, $replacement_tags)
  {
    $to = do_shortcode($lead_session_data['lead_email']);
    $from = do_shortcode(home_values_get_setting('emails', 'sender_email'));
    $from_name = do_shortcode(home_values_get_setting('emails', 'sender_name'));
    $subject = do_shortcode(home_values_get_setting('emails', 'new_lead_subject'));
    $message = do_shortcode($lead_email_content);

    $subject = do_shortcode($this->process_tags_and_shortcodes($subject, $replacement_tags));
    $message = do_shortcode($this->process_tags_and_shortcodes($message, $replacement_tags));

    $headers = array(
      'From: ' . $from_name . ' <' . $from . '>',
      'Reply-To: ' . $from_name . ' <' . $from . '>',
      'Content-Type: text/html; charset=UTF-8'
    );

    return $this->send_email($to, $subject, $message, $headers);
  }

  private function send_email_to_admin($lead_session_data, $replacement_tags)
  {

    $currentBlogId = get_current_blog_id();

    $to = home_values_get_setting('emails', 'new_lead_recipients');
    $from = do_shortcode(home_values_get_setting('emails', 'sender_email'));
    $from_name = do_shortcode(home_values_get_setting('emails', 'sender_name'));
    $subject = do_shortcode(home_values_get_setting('emails', 'new_lead_subject'));
    $message = do_shortcode(home_values_get_setting('emails', 'new_lead_email'));

    $to = preg_split('/\r\n|\r|\n/', trim($to));

    $subject = $this->process_tags_and_shortcodes($subject, $replacement_tags);
    $message = $this->process_tags_and_shortcodes($message, $replacement_tags);

    $headers = array(
      'From: ' . $from_name . ' <' . $from . '>',
      'Reply-To: ' . $from_name . ' <' . $from . '>',
      'Content-Type: text/html; charset=UTF-8'
    );

    $failures = 0;
    foreach ($to as $email) {
      $email = do_shortcode($email);
      $email = $this->process_tags_and_shortcodes($email, $replacement_tags);
      $email = trim($email);
      if (!$this->send_email($email, $subject, $message, $headers)) {
        $failures++;
      }
    }
    return $failures === 0;
  }


  private function process_tags_and_shortcodes($text, $replacement_tags)
  {
    $text = str_replace(array_keys($replacement_tags), array_values($replacement_tags), $text);
    return do_shortcode($text);
  }

  private function send_email($to, $subject, $message, $headers)
  {

    if (wp_mail($to, $subject, $message, $headers)) {
      home_values_log('info', 'Email sent successfully', [
        'to' => $to,
        'subject' => $subject,
        'message' => $message
      ]);
      return true;
    } else {
      home_values_log('error', 'Email sending failed', [
        'to' => $to,
        'subject' => $subject,
        'message' => $message
      ]);
      write_log('Email sending failed!');
      return false;
    }
  }


  public function broadcast_lead($lead_id)
  {
    if (!function_exists('ThreeWP_Broadcast')) {
      return;
    }

    // Get the lead pool blog id.
    $blog_id = home_values_get_setting('general', 'lead_pool_blog');

    if ($blog_id < 1) {
      return;
    }

    home_values_log('info', 'Broadcasting lead', [
      'lead_id' => $lead_id,
      'blog_id' => $blog_id
    ]);

    ThreeWP_Broadcast()->api()->broadcast_children($lead_id, [$blog_id]);
  }
}

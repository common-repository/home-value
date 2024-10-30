<?php

class Home_Values_Install
{
  public function __construct()
  {
    add_action('plugins_loaded', array($this, 'hv_check_version'));
  }

  public function hv_check_version()
  {
    // Current version of your plugin
    $current_version = HOME_VALUES_PLUGIN_VERSION;

    // Get the stored version
    $stored_version = is_multisite() ? get_site_option('home_values_plugin_version') : get_option('home_values_plugin_version');
    // write_log('stored version: ' . $stored_version);

    // if there is no version stored then it is either fresh install or update from very old version
    if (!$stored_version || empty($stored_version)) {
      // Check if data from old install exists
      $old_key = get_site_option('eightb\home_value\Home_Value_home_value_api_key') || get_option('Home_Value_home_value_api_key');

      // write_log('old key exists: ' . $old_key);

      // If old data exists then it is update from very old version
      if ($old_key !== false) {

        write_log('Migrating Settings');

        // Run your update function
        $this->migrate_settings();

        // Update the stored version
        if (is_multisite()) {
          update_site_option('home_values_plugin_version', $current_version);
        } else {
          update_option('home_values_plugin_version', $current_version);
        }
      } else {

        write_log('Fresh Install');

        // Run your install function for fresh install
        $this->set_default_settings();

        // Update the stored version
        if (is_multisite()) {
          update_site_option('home_values_plugin_version', $current_version);
        } else {
          update_option('home_values_plugin_version', $current_version);
        }
      }

      return;
    }

    // Check if versions do not match
    if ($stored_version != $current_version) {

      $old_key = get_site_option('eightb\home_value\Home_Value_home_value_api_key') || get_option('Home_Value_home_value_api_key');

      // If old data exists then it is update from very old version
      if ($old_key !== false) {

        write_log('Migrating Settings');

        // Run your update function
        $this->migrate_settings();

        // Update the stored version
        if (is_multisite()) {
          update_site_option('home_values_plugin_version', $current_version);
        } else {
          update_option('home_values_plugin_version', $current_version);
        }

        return;
      }



      // Update the stored version
      if (is_multisite()) {
        update_site_option('home_values_plugin_version', $current_version);
      } else {
        update_option('home_values_plugin_version', $current_version);
      }
    }
  }

  public function migrate_settings()
  {

    // if is multisite update site options as well
    if (is_multisite()) {
      // Get all blog ids
      $blog_ids = get_sites(array('fields' => 'ids'));

      foreach ($blog_ids as $blog_id) {
        switch_to_blog($blog_id); // Switch to each blog

        // Check if already configured
        $home_values_general_check = home_values_get_setting('general');
        $api_key = isset($home_values_general_check['api_key']) ? $home_values_general_check['api_key'] : '';
        $google_api_key = isset($home_values_general_check['google_api_key']) ? $home_values_general_check['google_api_key'] : '';
        $adjust_values = isset($home_values_general_check['adjust_values']) ? $home_values_general_check['adjust_values'] : '';
        $lead_pool_blog = isset($home_values_general_check['lead_pool_blog']) ? $home_values_general_check['lead_pool_blog'] : '';
        $webhooks = isset($home_values_general_check['webhooks']) ? $home_values_general_check['webhooks'] : '';
        $credits = isset($home_values_general_check['credits']) ? $home_values_general_check['credits'] : '';
        $next_refill_date = isset($home_values_general_check['next_refill_date']) ? $home_values_general_check['next_refill_date'] : '';
        $renewal_url = isset($home_values_general_check['renewal_url']) ? $home_values_general_check['renewal_url'] : '';
        $cancel_url = isset($home_values_general_check['cancel_url']) ? $home_values_general_check['cancel_url'] : '';


        $home_values_general = array(
          'api_key' => !empty($api_key) ? $api_key : get_option('Home_Value_home_value_api_key'),
          'google_api_key' => !empty($google_api_key) ? $google_api_key : get_option('Home_Value_google_api_key'),
          'adjust_values' => !empty($adjust_values) ? $adjust_values : get_option('Home_Value_home_extra_value'),
          'lead_pool_blog' => !empty($lead_pool_blog) ? $lead_pool_blog : get_option('Home_Value_lead_pool_blog'),
          'load_css' => get_option('Home_Value_load_css') === 'on' || get_option('Home_Value_load_css') == 1 ? 1 : 0,
          'webhooks' => !empty($webhooks) ? $webhooks : get_option('Home_Value_new_lead_webhooks'),
          'credits' => !empty($credits) ? $credits : get_option('Home_Value_results_left'),
          'next_refill_date' => !empty($next_refill_date) ? $next_refill_date : get_option('Home_Value_refill_date'),
          'renewal_url' => !empty($renewal_url) ? $renewal_url : 'https://homevalueplugin.com/renew?key=' . get_option('Home_Value_home_value_api_key', ''),
          'cancel_url' => !empty($cancel_url) ? $cancel_url : 'https://homevalueplugin.com/cancel?key=' . get_option('Home_Value_home_value_api_key', ''),
        );

        update_option('home_values_general', $home_values_general);

        // Check if already configured
        $home_values_forms_check = home_values_get_setting('forms');
        $address_field_placeholder = isset($home_values_forms_check['address_field_placeholder']) ? $home_values_forms_check['address_field_placeholder'] : '';
        $submit_button_text = isset($home_values_forms_check['submit_button_text']) ? $home_values_forms_check['submit_button_text'] : '';
        $email_field_placeholder = isset($home_values_forms_check['email_field_placeholder']) ? $home_values_forms_check['email_field_placeholder'] : '';
        $first_name_field_placeholder = isset($home_values_forms_check['first_name_field_placeholder']) ? $home_values_forms_check['first_name_field_placeholder'] : '';
        $last_name_field_placeholder = isset($home_values_forms_check['last_name_field_placeholder']) ? $home_values_forms_check['last_name_field_placeholder'] : '';
        $phone_number_placeholder = isset($home_values_forms_check['phone_number_placeholder']) ? $home_values_forms_check['phone_number_placeholder'] : '';
        $lead_form_submit_button_text = isset($home_values_forms_check['lead_form_submit_button_text']) ? $home_values_forms_check['lead_form_submit_button_text'] : '';
        $address_found_messaging = isset($home_values_forms_check['address_found_messaging']) ? $home_values_forms_check['address_found_messaging'] : '';
        $address_not_found_messaging = isset($home_values_forms_check['address_not_found_messaging']) ? $home_values_forms_check['address_not_found_messaging'] : '';
        $form_thank_you_message = isset($home_values_forms_check['form_thank_you_message']) ? $home_values_forms_check['form_thank_you_message'] : '';

        $home_values_forms = array(
          'address_field_placeholder' => get_option('Home_Value_address_search_form_address_input_placeholder') === 'No text' ? $address_field_placeholder : get_option('Home_Value_address_search_form_address_input_placeholder'),
          'submit_button_text' => get_option('Home_Value_address_search_form_submit_button_text') === 'No text' ? $submit_button_text : get_option('Home_Value_address_search_form_submit_button_text'),
          'email_field_placeholder' => !empty($email_field_placeholder) ? $email_field_placeholder : get_option('Home_Value_lead_form_email_placeholder'),
          'show_first_name_field' => get_option('Home_Value_lead_form_first_name_visible') === 'on' || get_option('Home_Value_lead_form_first_name_visible') == 1 ? 1 : 0,
          'require_first_name' => get_option('Home_Value_lead_form_first_name_required') === 'on' || get_option('Home_Value_lead_form_first_name_required') == 1 ? 1 : 0,
          'first_name_field_placeholder' => get_option('Home_Value_lead_form_first_name_placeholder') === 'No text' ? $first_name_field_placeholder : get_option('Home_Value_lead_form_first_name_placeholder'),
          'show_last_name_field' => get_option('Home_Value_lead_form_last_name_visible') === 'on' || get_option('Home_Value_lead_form_last_name_visible') == 1 ? 1 : 0,
          'require_last_name_field' => get_option('Home_Value_lead_form_last_name_required') === 'on' || get_option('Home_Value_lead_form_last_name_required') == 1 ? 1 : 0,
          'last_name_field_placeholder' => get_option('Home_Value_lead_form_last_name_placeholder') === 'No text' ? $last_name_field_placeholder : get_option('Home_Value_lead_form_last_name_placeholder'),
          'show_phone_number_field' => get_option('Home_Value_lead_form_phone_visible') === 'on' || get_option('Home_Value_lead_form_phone_visible') == 1 ? 1 : 0,
          'require_phone_number' => get_option('Home_Value_lead_form_phone_required') === 'on' || get_option('Home_Value_lead_form_phone_required') == 1 ? 1 : 0,
          'phone_number_placeholder' => get_option('Home_Value_lead_form_phone_placeholder') === 'No text' ? $phone_number_placeholder : get_option('Home_Value_lead_form_phone_placeholder'),
          'lead_form_submit_button_text' => get_option('Home_Value_lead_form_submit_button_text') === 'No text' ? $lead_form_submit_button_text : get_option('Home_Value_lead_form_submit_button_text'),
          'address_found_messaging' => get_option('Home_Value_lead_form_address_found_text') === 'No text' ? $address_found_messaging : get_option('Home_Value_lead_form_address_found_text'),
          'address_not_found_messaging' => get_option('Home_Value_lead_form_address_not_found_text') === 'No text' ? $address_not_found_messaging : get_option('Home_Value_lead_form_address_not_found_text'),
          'form_thank_you_message' => get_option('Home_Value_no_address_page_text') === 'No text' ? $form_thank_you_message : get_option('Home_Value_no_address_page_text'),
        );

        update_option('home_values_forms', $home_values_forms);

        // Check if the home values emails option exists
        $home_values_emails_check = home_values_get_setting('emails');
        $sender_email = isset($home_values_emails_check['sender_email']) ? $home_values_emails_check['sender_email'] : '';
        $sender_name = isset($home_values_emails_check['sender_name']) ? $home_values_emails_check['sender_name'] : '';
        $new_lead_recipients = isset($home_values_emails_check['new_lead_recipients']) ? $home_values_emails_check['new_lead_recipients'] : '';
        $new_lead_subject = isset($home_values_emails_check['new_lead_subject']) ? $home_values_emails_check['new_lead_subject'] : '';
        $new_lead_email = isset($home_values_emails_check['new_lead_email']) ? $home_values_emails_check['new_lead_email'] : '';


        $home_values_emails = array(
          'sender_email' => !empty($sender_email) ? $sender_email : get_option('Home_Value_email_new_lead_sender_email'),
          'sender_name' => !empty($sender_name) ? $sender_name : get_option('Home_Value_email_new_lead_sender_name'),
          'new_lead_recipients' => !empty($new_lead_recipients) ? $new_lead_recipients : get_option('Home_Value_email_new_lead_recipients'),
          'new_lead_subject' => get_option('Home_Value_email_new_lead_subject') === 'No text' ? $new_lead_subject : get_option('Home_Value_email_new_lead_subject'),
          'new_lead_email' => get_option('Home_Value_email_new_lead_text') === 'No text' ? $new_lead_email : get_option('Home_Value_email_new_lead_text'),
        );

        update_option('home_values_emails', $home_values_emails);

        // Check if the home values debug option exists
        $home_values_debug_check = home_values_get_setting('debug');
        $enable_debugging = isset($home_values_debug_check['enable_debugging']) ? $home_values_debug_check['enable_debugging'] : '';
        $debug_ips = isset($home_values_debug_check['debug_ips']) ? $home_values_debug_check['debug_ips'] : '';

        $home_values_debug = array(
          'enable_debugging' => !empty($enable_debugging) ? $enable_debugging : 0,
          'debug_ips' => !empty($debug_ips) ? $debug_ips : get_option('Home_Value_debug_ips'),
        );

        update_option('home_values_debug', $home_values_debug);
      }
      restore_current_blog(); // Switch back to the current blog


      // Check if already configured
      $home_values_general_check = get_site_option('home_values_general');
      $api_key = isset($home_values_general_check['api_key']) ? $home_values_general_check['api_key'] : '';
      $google_api_key = isset($home_values_general_check['google_api_key']) ? $home_values_general_check['google_api_key'] : '';
      $adjust_values = isset($home_values_general_check['adjust_values']) ? $home_values_general_check['adjust_values'] : '';
      $lead_pool_blog = isset($home_values_general_check['lead_pool_blog']) ? $home_values_general_check['lead_pool_blog'] : '';
      $webhooks = isset($home_values_general_check['webhooks']) ? $home_values_general_check['webhooks'] : '';
      $credits = isset($home_values_general_check['credits']) ? $home_values_general_check['credits'] : '';
      $next_refill_date = isset($home_values_general_check['next_refill_date']) ? $home_values_general_check['next_refill_date'] : '';
      $renewal_url = isset($home_values_general_check['renewal_url']) ? $home_values_general_check['renewal_url'] : '';
      $cancel_url = isset($home_values_general_check['cancel_url']) ? $home_values_general_check['cancel_url'] : '';


      $home_values_general = array(
        'api_key' => !empty($api_key) ? $api_key : get_site_option('eightb\home_value\Home_Value_home_value_api_key'),
        'google_api_key' => !empty($google_api_key) ? $google_api_key : get_site_option('eightb\home_value\Home_Value_google_api_key'),
        'adjust_values' => !empty($adjust_values) ? $adjust_values : get_site_option('eightb\home_value\Home_Value_home_extra_value'),
        'lead_pool_blog' => !empty($lead_pool_blog) ? $lead_pool_blog : get_site_option('eightb\home_value\Home_Value_lead_pool_blog'),
        'load_css' => get_site_option('eightb\home_value\Home_Value_load_css') === 'on' || get_site_option('eightb\home_value\Home_Value_load_css') == 1 ? 1 : 0,
        'webhooks' => !empty($webhooks) ? $webhooks : get_site_option('eightb\home_value\Home_Value_new_lead_webhooks'),
        'credits' => !empty($credits) ? $credits : get_site_option('eightb\home_value\Home_Value_results_left'),
        'next_refill_date' => !empty($next_refill_date) ? $next_refill_date : get_site_option('eightb\home_value\Home_Value_refill_date'),
        'renewal_url' => !empty($renewal_url) ? $renewal_url : 'https://homevalueplugin.com/renew?key=' . get_site_option('eightb\home_value\Home_Value_home_value_api_key', ''),
        'cancel_url' => !empty($cancel_url) ? $cancel_url : 'https://homevalueplugin.com/cancel?key=' . get_site_option('eightb\home_value\Home_Value_home_value_api_key', ''),
      );

      update_site_option('home_values_general', $home_values_general);

      // Check if already configured
      $home_values_forms_check = get_site_option('home_values_forms');
      $address_field_placeholder = isset($home_values_forms_check['address_field_placeholder']) ? $home_values_forms_check['address_field_placeholder'] : '';
      $submit_button_text = isset($home_values_forms_check['submit_button_text']) ? $home_values_forms_check['submit_button_text'] : '';
      $email_field_placeholder = isset($home_values_forms_check['email_field_placeholder']) ? $home_values_forms_check['email_field_placeholder'] : '';
      $first_name_field_placeholder = isset($home_values_forms_check['first_name_field_placeholder']) ? $home_values_forms_check['first_name_field_placeholder'] : '';
      $last_name_field_placeholder = isset($home_values_forms_check['last_name_field_placeholder']) ? $home_values_forms_check['last_name_field_placeholder'] : '';
      $phone_number_placeholder = isset($home_values_forms_check['phone_number_placeholder']) ? $home_values_forms_check['phone_number_placeholder'] : '';
      $lead_form_submit_button_text = isset($home_values_forms_check['lead_form_submit_button_text']) ? $home_values_forms_check['lead_form_submit_button_text'] : '';
      $address_found_messaging = isset($home_values_forms_check['address_found_messaging']) ? $home_values_forms_check['address_found_messaging'] : '';
      $address_not_found_messaging = isset($home_values_forms_check['address_not_found_messaging']) ? $home_values_forms_check['address_not_found_messaging'] : '';
      $form_thank_you_message = isset($home_values_forms_check['form_thank_you_message']) ? $home_values_forms_check['form_thank_you_message'] : '';

      $home_values_forms = array(
        'address_field_placeholder' => get_site_option('eightb\home_value\Home_Value_address_search_form_address_input_placeholder') === 'No text' ? $address_field_placeholder : get_site_option('eightb\home_value\Home_Value_address_search_form_address_input_placeholder'),
        'submit_button_text' => get_site_option('eightb\home_value\Home_Value_address_search_form_submit_button_text') === 'No text' ? $submit_button_text : get_site_option('eightb\home_value\Home_Value_address_search_form_submit_button_text'),
        'email_field_placeholder' => !empty($email_field_placeholder) ? $email_field_placeholder : get_site_option('eightb\home_value\Home_Value_lead_form_email_placeholder'),
        'show_first_name_field' => get_site_option('eightb\home_value\Home_Value_lead_form_first_name_visible') === 'on' || get_site_option('eightb\home_value\Home_Value_lead_form_first_name_visible') == 1 ? 1 : 0,
        'require_first_name' => get_site_option('eightb\home_value\Home_Value_lead_form_first_name_required') === 'on' || get_site_option('eightb\home_value\Home_Value_lead_form_first_name_required') == 1 ? 1 : 0,
        'first_name_field_placeholder' => get_site_option('eightb\home_value\Home_Value_lead_form_first_name_placeholder') === 'No text' ? $first_name_field_placeholder : get_site_option('eightb\home_value\Home_Value_lead_form_first_name_placeholder'),
        'show_last_name_field' => get_site_option('eightb\home_value\Home_Value_lead_form_last_name_visible') === 'on' || get_site_option('eightb\home_value\Home_Value_lead_form_last_name_visible') == 1 ? 1 : 0,
        'require_last_name_field' => get_site_option('eightb\home_value\Home_Value_lead_form_last_name_required') === 'on' || get_site_option('eightb\home_value\Home_Value_lead_form_last_name_required') == 1 ? 1 : 0,
        'last_name_field_placeholder' => get_site_option('eightb\home_value\Home_Value_lead_form_last_name_placeholder') === 'No text' ? $last_name_field_placeholder : get_site_option('eightb\home_value\Home_Value_lead_form_last_name_placeholder'),
        'show_phone_number_field' => get_site_option('eightb\home_value\Home_Value_lead_form_phone_visible') === 'on' || get_site_option('eightb\home_value\Home_Value_lead_form_phone_visible') == 1 ? 1 : 0,
        'require_phone_number' => get_site_option('eightb\home_value\Home_Value_lead_form_phone_required') === 'on' || get_site_option('eightb\home_value\Home_Value_lead_form_phone_required') == 1 ? 1 : 0,
        'phone_number_placeholder' => get_site_option('eightb\home_value\Home_Value_lead_form_phone_placeholder') === 'No text' ? $phone_number_placeholder : get_site_option('eightb\home_value\Home_Value_lead_form_phone_placeholder'),
        'lead_form_submit_button_text' => get_site_option('eightb\home_value\Home_Value_lead_form_submit_button_text') === 'No text' ? $lead_form_submit_button_text : get_site_option('eightb\home_value\Home_Value_lead_form_submit_button_text'),
        'address_found_messaging' => get_site_option('eightb\home_value\Home_Value_lead_form_address_found_text') === 'No text' ? $address_found_messaging : get_site_option('eightb\home_value\Home_Value_lead_form_address_found_text'),
        'address_not_found_messaging' => get_site_option('eightb\home_value\Home_Value_lead_form_address_not_found_text') === 'No text' ? $address_not_found_messaging : get_site_option('eightb\home_value\Home_Value_lead_form_address_not_found_text'),
        'form_thank_you_message' => get_site_option('eightb\home_value\Home_Value_no_address_page_text') === 'No text' ? $form_thank_you_message : get_site_option('eightb\home_value\Home_Value_no_address_page_text'),
      );

      update_site_option('home_values_forms', $home_values_forms);

      // Check if the home values emails option exists
      $home_values_emails_check = get_site_option('home_values_emails');
      $sender_email = isset($home_values_emails_check['sender_email']) ? $home_values_emails_check['sender_email'] : '';
      $sender_name = isset($home_values_emails_check['sender_name']) ? $home_values_emails_check['sender_name'] : '';
      $new_lead_recipients = isset($home_values_emails_check['new_lead_recipients']) ? $home_values_emails_check['new_lead_recipients'] : '';
      $new_lead_subject = isset($home_values_emails_check['new_lead_subject']) ? $home_values_emails_check['new_lead_subject'] : '';
      $new_lead_email = isset($home_values_emails_check['new_lead_email']) ? $home_values_emails_check['new_lead_email'] : '';


      $home_values_emails = array(
        'sender_email' => !empty($sender_email) ? $sender_email : get_site_option('eightb\home_value\Home_Value_email_new_lead_sender_email'),
        'sender_name' => !empty($sender_name) ? $sender_name : get_site_option('eightb\home_value\Home_Value_email_new_lead_sender_name'),
        'new_lead_recipients' => !empty($new_lead_recipients) ? $new_lead_recipients : get_site_option('eightb\home_value\Home_Value_email_new_lead_recipients'),
        'new_lead_subject' => get_site_option('eightb\home_value\Home_Value_email_new_lead_subject') === 'No text' ? $new_lead_subject : get_site_option('eightb\home_value\Home_Value_email_new_lead_subject'),
        'new_lead_email' => get_site_option('eightb\home_value\Home_Value_email_new_lead_text') === 'No text' ? $new_lead_email : get_site_option('eightb\home_value\Home_Value_email_new_lead_text'),
      );

      update_site_option('home_values_emails', $home_values_emails);

      // Check if the home values debug option exists
      $home_values_debug_check = get_site_option('home_values_debug');
      $enable_debugging = isset($home_values_debug_check['enable_debugging']) ? $home_values_debug_check['enable_debugging'] : '';
      $debug_ips = isset($home_values_debug_check['debug_ips']) ? $home_values_debug_check['debug_ips'] : '';

      $home_values_debug = array(
        'enable_debugging' => !empty($enable_debugging) ? $enable_debugging : 0,
        'debug_ips' => !empty($debug_ips) ? $debug_ips : get_site_option('eightb\home_value\Home_Value_debug_ips'),
      );

      update_site_option('home_values_debug', $home_values_debug);
    } else {


      // Migrate options

      // Check if already configured
      $home_values_general_check = home_values_get_setting('general');
      $api_key = isset($home_values_general_check['api_key']) ? $home_values_general_check['api_key'] : '';
      $google_api_key = isset($home_values_general_check['google_api_key']) ? $home_values_general_check['google_api_key'] : '';
      $adjust_values = isset($home_values_general_check['adjust_values']) ? $home_values_general_check['adjust_values'] : '';
      $lead_pool_blog = isset($home_values_general_check['lead_pool_blog']) ? $home_values_general_check['lead_pool_blog'] : '';
      $webhooks = isset($home_values_general_check['webhooks']) ? $home_values_general_check['webhooks'] : '';
      $credits = isset($home_values_general_check['credits']) ? $home_values_general_check['credits'] : '';
      $next_refill_date = isset($home_values_general_check['next_refill_date']) ? $home_values_general_check['next_refill_date'] : '';
      $renewal_url = isset($home_values_general_check['renewal_url']) ? $home_values_general_check['renewal_url'] : '';
      $cancel_url = isset($home_values_general_check['cancel_url']) ? $home_values_general_check['cancel_url'] : '';


      $home_values_general = array(
        'api_key' => !empty($api_key) ? $api_key : get_option('Home_Value_home_value_api_key'),
        'google_api_key' => !empty($google_api_key) ? $google_api_key : get_option('Home_Value_google_api_key'),
        'adjust_values' => !empty($adjust_values) ? $adjust_values : get_option('Home_Value_home_extra_value'),
        'lead_pool_blog' => !empty($lead_pool_blog) ? $lead_pool_blog : get_option('Home_Value_lead_pool_blog'),
        'load_css' => get_option('Home_Value_load_css') === 'on' || get_option('Home_Value_load_css') == 1 ? 1 : 0,
        'webhooks' => !empty($webhooks) ? $webhooks : get_option('Home_Value_new_lead_webhooks'),
        'credits' => !empty($credits) ? $credits : get_option('Home_Value_results_left'),
        'next_refill_date' => !empty($next_refill_date) ? $next_refill_date : get_option('Home_Value_refill_date'),
        'renewal_url' => !empty($renewal_url) ? $renewal_url : 'https://homevalueplugin.com/renew?key=' . get_option('Home_Value_home_value_api_key', ''),
        'cancel_url' => !empty($cancel_url) ? $cancel_url : 'https://homevalueplugin.com/cancel?key=' . get_option('Home_Value_home_value_api_key', ''),
      );

      update_option('home_values_general', $home_values_general);

      // Check if already configured
      $home_values_forms_check = home_values_get_setting('forms');
      $address_field_placeholder = isset($home_values_forms_check['address_field_placeholder']) ? $home_values_forms_check['address_field_placeholder'] : '';
      $submit_button_text = isset($home_values_forms_check['submit_button_text']) ? $home_values_forms_check['submit_button_text'] : '';
      $email_field_placeholder = isset($home_values_forms_check['email_field_placeholder']) ? $home_values_forms_check['email_field_placeholder'] : '';
      $first_name_field_placeholder = isset($home_values_forms_check['first_name_field_placeholder']) ? $home_values_forms_check['first_name_field_placeholder'] : '';
      $last_name_field_placeholder = isset($home_values_forms_check['last_name_field_placeholder']) ? $home_values_forms_check['last_name_field_placeholder'] : '';
      $phone_number_placeholder = isset($home_values_forms_check['phone_number_placeholder']) ? $home_values_forms_check['phone_number_placeholder'] : '';
      $lead_form_submit_button_text = isset($home_values_forms_check['lead_form_submit_button_text']) ? $home_values_forms_check['lead_form_submit_button_text'] : '';
      $address_found_messaging = isset($home_values_forms_check['address_found_messaging']) ? $home_values_forms_check['address_found_messaging'] : '';
      $address_not_found_messaging = isset($home_values_forms_check['address_not_found_messaging']) ? $home_values_forms_check['address_not_found_messaging'] : '';
      $form_thank_you_message = isset($home_values_forms_check['form_thank_you_message']) ? $home_values_forms_check['form_thank_you_message'] : '';

      $home_values_forms = array(
        'address_field_placeholder' => get_option('Home_Value_address_search_form_address_input_placeholder') === 'No text' ? $address_field_placeholder : get_option('Home_Value_address_search_form_address_input_placeholder'),
        'submit_button_text' => get_option('Home_Value_address_search_form_submit_button_text') === 'No text' ? $submit_button_text : get_option('Home_Value_address_search_form_submit_button_text'),
        'email_field_placeholder' => !empty($email_field_placeholder) ? $email_field_placeholder : get_option('Home_Value_lead_form_email_placeholder'),
        'show_first_name_field' => get_option('Home_Value_lead_form_first_name_visible') === 'on' || get_option('Home_Value_lead_form_first_name_visible') == 1 ? 1 : 0,
        'require_first_name' => get_option('Home_Value_lead_form_first_name_required') === 'on' || get_option('Home_Value_lead_form_first_name_required') == 1 ? 1 : 0,
        'first_name_field_placeholder' => get_option('Home_Value_lead_form_first_name_placeholder') === 'No text' ? $first_name_field_placeholder : get_option('Home_Value_lead_form_first_name_placeholder'),
        'show_last_name_field' => get_option('Home_Value_lead_form_last_name_visible') === 'on' || get_option('Home_Value_lead_form_last_name_visible') == 1 ? 1 : 0,
        'require_last_name_field' => get_option('Home_Value_lead_form_last_name_required') === 'on' || get_option('Home_Value_lead_form_last_name_required') == 1 ? 1 : 0,
        'last_name_field_placeholder' => get_option('Home_Value_lead_form_last_name_placeholder') === 'No text' ? $last_name_field_placeholder : get_option('Home_Value_lead_form_last_name_placeholder'),
        'show_phone_number_field' => get_option('Home_Value_lead_form_phone_visible') === 'on' || get_option('Home_Value_lead_form_phone_visible') == 1 ? 1 : 0,
        'require_phone_number' => get_option('Home_Value_lead_form_phone_required') === 'on' || get_option('Home_Value_lead_form_phone_required') == 1 ? 1 : 0,
        'phone_number_placeholder' => get_option('Home_Value_lead_form_phone_placeholder') === 'No text' ? $phone_number_placeholder : get_option('Home_Value_lead_form_phone_placeholder'),
        'lead_form_submit_button_text' => get_option('Home_Value_lead_form_submit_button_text') === 'No text' ? $lead_form_submit_button_text : get_option('Home_Value_lead_form_submit_button_text'),
        'address_found_messaging' => get_option('Home_Value_lead_form_address_found_text') === 'No text' ? $address_found_messaging : get_option('Home_Value_lead_form_address_found_text'),
        'address_not_found_messaging' => get_option('Home_Value_lead_form_address_not_found_text') === 'No text' ? $address_not_found_messaging : get_option('Home_Value_lead_form_address_not_found_text'),
        'form_thank_you_message' => get_option('Home_Value_no_address_page_text') === 'No text' ? $form_thank_you_message : get_option('Home_Value_no_address_page_text'),
      );

      update_option('home_values_forms', $home_values_forms);

      // Check if the home values emails option exists
      $home_values_emails_check = home_values_get_setting('emails');
      $sender_email = isset($home_values_emails_check['sender_email']) ? $home_values_emails_check['sender_email'] : '';
      $sender_name = isset($home_values_emails_check['sender_name']) ? $home_values_emails_check['sender_name'] : '';
      $new_lead_recipients = isset($home_values_emails_check['new_lead_recipients']) ? $home_values_emails_check['new_lead_recipients'] : '';
      $new_lead_subject = isset($home_values_emails_check['new_lead_subject']) ? $home_values_emails_check['new_lead_subject'] : '';
      $new_lead_email = isset($home_values_emails_check['new_lead_email']) ? $home_values_emails_check['new_lead_email'] : '';


      $home_values_emails = array(
        'sender_email' => !empty($sender_email) ? $sender_email : get_option('Home_Value_email_new_lead_sender_email'),
        'sender_name' => !empty($sender_name) ? $sender_name : get_option('Home_Value_email_new_lead_sender_name'),
        'new_lead_recipients' => !empty($new_lead_recipients) ? $new_lead_recipients : get_option('Home_Value_email_new_lead_recipients'),
        'new_lead_subject' => get_option('Home_Value_email_new_lead_subject') === 'No text' ? $new_lead_subject : get_option('Home_Value_email_new_lead_subject'),
        'new_lead_email' => get_option('Home_Value_email_new_lead_text') === 'No text' ? $new_lead_email : get_option('Home_Value_email_new_lead_text'),
      );

      update_option('home_values_emails', $home_values_emails);

      // Check if the home values debug option exists
      $home_values_debug_check = home_values_get_setting('debug');
      $enable_debugging = isset($home_values_debug_check['enable_debugging']) ? $home_values_debug_check['enable_debugging'] : '';
      $debug_ips = isset($home_values_debug_check['debug_ips']) ? $home_values_debug_check['debug_ips'] : '';

      $home_values_debug = array(
        'enable_debugging' => !empty($enable_debugging) ? $enable_debugging : 0,
        'debug_ips' => !empty($debug_ips) ? $debug_ips : get_option('Home_Value_debug_ips'),
      );

      update_option('home_values_debug', $home_values_debug);
    }
  }


  private function set_default_settings()
  {
    write_log('set_default_settings');
    $current_user = wp_get_current_user();

    $address_found_messaging = '<p>We found your address!</p>';
    $address_not_found_messaging = '<p>We could not find your address.</p>';

    $lead_email = `<table width="100%" border="0" cellpadding="25" cellspacing="0" style="text-align:center;font-family: Helvetica Neue, Helvetica, Arial,' sans-serif';">
    <tbody>
    <tr>
    <td>
      <table width="400" border="0" cellpadding="0" cellspacing="0">
      <tbody>
      <tr style="background:#2969b0;">
      <td style="padding:20px; font-size: 22px;color:#fff;font-weight:bold; text-align:center;">New Value Request!</td>
      </tr>
      <tr>
      <td style="text-align:center; background:#fafafa; border:1px solid #ddd; border-width: 0 1px"><p style="margin-top:40px">From:<br />
        <span style="font-size: 18px;font-weight:bold;">[8b_home_value_first_name] [8b_home_value_last_name]</span></p>
      <p>Email:<br />
        <span style="font-size: 18px;font-weight:bold;">[8b_home_value_email]</span></p>
        <p>Phone:<br />
        <span style="font-weight:bold;">[8b_home_value_phone]</span></p>
        <p>Property Address:<br />
        <span style="font-weight:bold;">[8b_home_value_searched_address]</span></p>
        <p>Median Value:<br />
        <span style="font-weight:bold;">$[8b_home_value_data_valuation_medium]</span></p>
        <p style="margin-bottom: 50px">Home Specs:<br />
        <span style="font-weight:bold;">[8b_home_value_data_size]sqft | [8b_home_value_data_beds] bed | [8b_home_value_data_baths] bath</p>
        
    </td>
      </tr>
      <tr style="background:#2969b0; height: 5px">
      <td>&nbsp;</td>
      </tr>
      </tbody>
      </table>
    </td>
    </tr>
    </tbody>
    </table>
    `;

    $lead_admin_email = '<p style="font-family: Arial;">New Value Requested from [8b_home_value_first_name] [8b_home_value_last_name]!</p>
<p style="font-family: Arial; line-height: 1.5;">Additional Details:</p>
<p style="font-family: Arial; line-height: 1.5;">Phone: [8b_home_value_phone]</p>
<p style="font-family: Arial; line-height: 1.5;">Email: [8b_home_value_email]</p>
<p style="font-family: Arial; line-height: 1.5;">Property Address: [8b_home_value_searched_address]</p>
<p style="font-family: Arial; line-height: 1.5;">Suggested Value: $[8b_home_value_data_valuation_medium]</p>
<p style="font-family: Arial; line-height: 1.5;">Home Specs: [8b_home_value_data_size]sqft   [8b_home_value_data_beds] bed  [8b_home_value_data_baths] bath</p>';


    $home_values_general = array(
      'api_key' => '',
      'google_api_key' => '',
      'adjust_values' => 0,
      'lead_pool_blog' => 0,
      'load_css' => 1,
      'webhooks' => '',
      'credits' => 0,
      'next_refill_date' => '',
      'renewal_url' => 'https://homevalueplugin.com/renew',
      'contact_url' => '',
    );

    $home_values_forms = array(
      'address_field_placeholder' => 'Address',
      'submit_button_text' => 'Submit',
      'email_field_placeholder' => 'Email Address',
      'show_first_name_field' => 1,
      'require_first_name' => 1,
      'first_name_field_placeholder' => 'First Name',
      'show_last_name_field' => 1,
      'require_last_name_field' => 1,
      'last_name_field_placeholder' => 'Last Name',
      'show_phone_number_field' => 1,
      'require_phone_number' => 1,
      'phone_number_placeholder' => 'Phone Number',
      'lead_form_submit_button_text' => 'Get My Values!',
      'address_found_messaging' => $address_found_messaging,
      'address_not_found_messaging' => $address_not_found_messaging,
      'form_thank_you_message' => '<h4>Thank you very much for your submission!</h4>',
    );

    $home_values_emails = array(
      'sender_email' => $current_user->user_email,
      'sender_name' => get_bloginfo('name'),
      'new_lead_recipients' => $current_user->user_email,
      'new_lead_subject' => 'New Home Value Request from [8b_home_value_first_name] [8b_home_value_last_name]!',
      'new_lead_email' => $lead_admin_email,
    );

    $home_values_debug = array(
      'enable_debugging' => 0,
      'debug_ips' => '',
    );


    // if is multisite update site options as well
    if (is_multisite()) {
      // Get all blog ids
      $blog_ids = get_sites(array('fields' => 'ids'));

      foreach ($blog_ids as $blog_id) {
        switch_to_blog($blog_id); // Switch to each blog

        // Update options
        update_option('home_values_general', $home_values_general);
        update_option('home_values_forms', $home_values_forms);
        update_option('home_values_emails', $home_values_emails);
        update_option('home_values_debug', $home_values_debug);

        write_log('Setting Defaults for blog: ' . $blog_id . '');
        write_log($blog_id);
        write_log($home_values_general);
        write_log($home_values_forms);
        write_log($home_values_emails);
      }
      restore_current_blog(); // Switch back to the current blog

      // Update Site Options
      update_site_option('home_values_general', $home_values_general);
      update_site_option('home_values_forms', $home_values_forms);
      update_site_option('home_values_emails', $home_values_emails);
      update_site_option('home_values_debug', $home_values_debug);
    } else {

      // Update Options
      update_option('home_values_general', $home_values_general);
      update_option('home_values_forms', $home_values_forms);
      update_option('home_values_emails', $home_values_emails);
      update_option('home_values_debug', $home_values_debug);
    }
  }
}

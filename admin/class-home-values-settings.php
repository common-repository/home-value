<?php

class Home_Values_Settings
{
  private $plugin_name;

  public function __construct($plugin_name)
  {
    $this->plugin_name = $plugin_name;
  }

  public function register_settings()
  {
    // Register settings for each section.
    // register_setting($this->plugin_name . '_general', $this->plugin_name . '_general', array($this, 'sanitize_general_settings'));
    register_setting($this->plugin_name . '_general', $this->plugin_name . '_general');
    register_setting($this->plugin_name . '_forms', $this->plugin_name . '_forms');
    register_setting($this->plugin_name . '_emails', $this->plugin_name . '_emails');
    register_setting($this->plugin_name . '_debug', $this->plugin_name . '_debug');
  }

  public function sanitize_general_settings($input)
  {
    $new_input = array();

    if (isset($input['api_key'])) {
      $new_input['api_key'] = sanitize_text_field($input['api_key']);
    }

    if (isset($input['google_api_key'])) {
      $new_input['google_api_key'] = sanitize_text_field($input['google_api_key']);
    }

    if (isset($input['credits'])) {
      $new_input['credits'] = sanitize_text_field($input['credits']);
    }

    if (isset($input['next_refill_date'])) {
      $new_input['next_refill_date'] = sanitize_text_field($input['next_refill_date']);
    }

    if (isset($input['renewal_url'])) {
      $new_input['renewal_url'] = sanitize_text_field($input['renewal_url']);
    }

    if (isset($input['lead_pool_blog'])) {
      $new_input['lead_pool_blog'] = absint($input['lead_pool_blog']);
    }

    if (isset($input['adjust_values'])) {
      $new_input['adjust_values'] = absint($input['adjust_values']);
    }

    if (isset($input['load_css'])) {
      $new_input['load_css'] = absint($input['load_css']);
    }

    if (isset($input['webhooks'])) {
      $new_input['webhooks'] = sanitize_textarea_field($input['webhooks']);
    }

    return $new_input;
  }


  public function save_network_settings()
  {
    // Todo: Check what this is sending
    write_log('Saving Network Settings: ');
    write_log($_POST);
    write_log($this->plugin_name . '_general');

    // General settings.
    if (isset($_POST[$this->plugin_name . '_general'])) {
      check_admin_referer($this->plugin_name . '_general-options');
      $input = $_POST[$this->plugin_name . '_general'];
      $sanitized_input = $this->sanitize_general_settings($input);

      // if is a multisite network, save the google_api_key to each of child sites
      if (is_multisite()) {
        $sites = get_sites();
        foreach ($sites as $site) {
          switch_to_blog($site->blog_id);
          $child_options = get_option($this->plugin_name . '_general');
          $child_options['google_api_key'] = $sanitized_input['google_api_key'];
          update_option($this->plugin_name . '_general', $child_options);
          restore_current_blog();
        }
      }

      delete_site_option($this->plugin_name . '_general');
      write_log(update_site_option($this->plugin_name . '_general', $input));
      wp_redirect(add_query_arg(array('page' => $this->plugin_name, 'updated' => 'true'), network_admin_url('settings.php')));
      exit;
    }

    // Forms settings.
    if (isset($_POST[$this->plugin_name . '_forms'])) {
      check_admin_referer($this->plugin_name . '_forms-options');
      $input = $_POST[$this->plugin_name . '_forms'];
      delete_site_option($this->plugin_name . '_forms');
      update_site_option($this->plugin_name . '_forms', $input);
      wp_redirect(add_query_arg(array('page' => $this->plugin_name, 'tab' => 'forms', 'updated' => 'true'), network_admin_url('settings.php')));
      exit;
    }

    // Emails settings.
    if (isset($_POST[$this->plugin_name . '_emails'])) {
      check_admin_referer($this->plugin_name . '_emails-options');
      $input = $_POST[$this->plugin_name . '_emails'];
      update_site_option($this->plugin_name . '_emails', $input);
      wp_redirect(add_query_arg(array('page' => $this->plugin_name, 'tab' => 'emails', 'updated' => 'true'), network_admin_url('settings.php')));
      exit;
    }

    // Debug settings.
    if (isset($_POST[$this->plugin_name . '_debug'])) {
      check_admin_referer($this->plugin_name . '_debug-options');
      $input = $_POST[$this->plugin_name . '_debug'];
      update_site_option($this->plugin_name . '_debug', $input);
      wp_redirect(add_query_arg(array('page' => $this->plugin_name, 'tab' => 'debug', 'updated' => 'true'), network_admin_url('settings.php')));
      exit;
    }
  }
}

<?php
class Home_Values_API
{

  private $api_base_url;
  private $api_version;


  public function __construct()
  {
    $this->api_base_url = 'https://homevalueplugin.com';
    // $this->api_base_url = 'https://wordpress-55445-3356071.cloudwaysapps.com';
    $this->api_version = 'v2';
  }

  private function call_api($endpoint, $params)
  {
    $url = $this->api_base_url . '/wp-json/home-values-server/v1' . $endpoint;
    $params['api_version'] = $this->api_version;

    $response = wp_remote_post($url, array(
      'body' => $params,
      'timeout' => 120,
      'httpversion' => '1.0',
      'blocking' => true,
      'headers' => array(),
      'cookies' => array(),
    ));

    if (is_wp_error($response)) {
      return array('error' => $response->get_error_message());
    } else {
      return json_decode(wp_remote_retrieve_body($response), true);
    }
  }

  public function check_status($license_key)
  {
    $params = array(
      'license_key' => $license_key,
    );

    return $this->call_api('/check-status', $params);
  }

  public function create_access_key($site_key, $email, $url)
  {
    $params = array(
      'site_key' => $site_key,
      'email' => $email,
      'url' => $url,
    );

    return $this->call_api('/create-access-key', $params);
  }

  public function search_valuations($license_key, $street, $zipcode)
  {
    $params = array(
      'license_key' => $license_key,
      'street' => $street,
      'zipcode' => $zipcode,
    );

    $response =  $this->call_api('/search-valuations', $params);

    return $response;
  }

  public function send_lead_to_webhooks($lead, $webhooks)
  {
    $webhook_responses = array();

    foreach ($webhooks as $webhook_url) {
      $webhook_url = trim($webhook_url);
      if (empty($webhook_url)) {
        continue;
      }

      $response = wp_remote_post($webhook_url, array(
        'body' => (array) $lead->meta,
        'timeout' => 45,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'cookies' => array(),
      ));

      if (is_wp_error($response)) {
        write_log('Error sending lead to webhook');
        write_log($response->get_error_message());
        write_log($lead->meta);
        home_values_log('error', 'Error sending lead to webhook');
        $webhook_responses[] = array('url' => $webhook_url, 'status' => 'error', 'message' => $response->get_error_message());
      } else {
        $webhook_responses[] = array('url' => $webhook_url, 'status' => 'success', 'message' => wp_remote_retrieve_body($response));
      }
    }

    return $webhook_responses;
  }
}

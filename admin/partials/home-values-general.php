<?php

// Check if the current user is a network admin in a multisite environment.
$is_network_admin = (is_multisite() && is_network_admin());

// Refresh the status before loading page. This so that the general page is always accurate.
$api = new Home_Values_API();
if ($is_network_admin) {
  $api_key = home_values_get_site_setting('general', 'api_key');
} else {
  $api_key = home_values_get_setting('general', 'api_key');
}

$response = $api->check_status($api_key);
$is_key_good = false;

if ($response['status'] == 'OK') {
  $is_key_good = true;
  if ($is_network_admin) {
    home_values_update_site_setting('general', 'credits', $response['credits']);
    home_values_update_site_setting('general', 'next_refill_date', $response['next_refill_date']);
    home_values_update_site_setting('general', 'renewal_url', 'https://homevalueplugin.com/renew?key=' . $api_key);
    home_values_update_site_setting('general', 'cancel_url', 'https://homevalueplugin.com/cancel?key=' . $api_key);
    home_values_update_site_setting('general', 'auto_refill_enabled', $response['auto_refill_enabled']);
    home_values_update_site_setting('general', 'credits_in_package', $response['credits_in_package']);
    home_values_update_site_setting('general', 'credits_low', $response['credits_low']);
    home_values_update_setting('general', 'api_key', $api_key);
  } else {
    home_values_update_setting('general', 'credits', $response['credits']);
    home_values_update_setting('general', 'next_refill_date', $response['next_refill_date']);
    home_values_update_setting('general', 'renewal_url', 'https://homevalueplugin.com/renew?key=' . $api_key);
    home_values_update_setting('general', 'cancel_url', 'https://homevalueplugin.com/cancel?key=' . $api_key);
    home_values_update_setting('general', 'auto_refill_enabled', $response['auto_refill_enabled']);
    home_values_update_setting('general', 'credits_in_package', $response['credits_in_package']);
    home_values_update_setting('general', 'credits_low', $response['credits_low']);
  }
}


$options = $is_network_admin ? get_site_option('home_values_general', array()) : get_option('home_values_general', array());
$options = !$options ? get_site_option('home_values_general', array()) : $options;

?>

<?php
// if is network admin display global message
if ($is_network_admin) : ?>
  <div class="notice notice-info">
    <p><?php _e('These settings are global and will be applied to all sites in the network. Local sites may override these settings in their dashboards.', 'home-values'); ?></p>
  </div>
<?php endif;
?>

<table class="form-table">
  <!-- If multisite these go to network admin -->
  <?php if (is_multisite() && !$is_network_admin) : ?>
    <tr>
      <th scope="row"><label for="home_values_api_key"><?php _e('API Key', 'home-values'); ?></label></th>
      <td>
        <p>Please visit the <a href="<?php echo network_admin_url('admin.php?page=home_values'); ?>"> Home Value network settings</a> page to configure your API key.</p>
      </td>
    </tr>
    <tr>
      <th scope="row"><label for="home_values_google_api_key"><?php _e('Google API Key', 'home-values'); ?></label></th>
      <td>
        <p>Please visit the <a href="<?php echo network_admin_url('admin.php?page=home_values'); ?>"> Home Value network settings</a> page to configure your Google API key.</p>
      </td>
    </tr>
  <?php else : ?>
    <tr>
      <th scope="row"><label for="home_values_api_key"><?php _e('Home Value API Key', 'home-values'); ?></label></th>
      <td>
        <input type="text" id="home_values_api_key" name="home_values_general[api_key]" value="<?php echo esc_attr($options['api_key']); ?>" class="regular-text" />
        <button type="button" id="generate_api_key" class="button"><?php _e('Generate API Key', 'home-values'); ?></button>
        <input type="hidden" id="home_values_next_refill_date" name="home_values_general[next_refill_date]" value="<?php echo esc_attr($options['next_refill_date']); ?>" class="regular-text" />
        <input type="hidden" id="home_values_credits" name="home_values_general[credits]" value="<?php echo esc_attr($options['credits']); ?>" class="regular-text" />
        <input type="hidden" id="home_values_renewal_url" name="home_values_general[renewal_url]" value="<?php echo esc_attr($options['renewal_url']); ?>" class="regular-text" />


        <input type="hidden" id="home_values_auto_refill_enabled" name="home_values_general[auto_refill_enabled]" value="<?php echo esc_attr($options['auto_refill_enabled']); ?>" class="regular-text" />
        <input type="hidden" id="home_values_credits_in_package" name="home_values_general[credits_in_package]" value="<?php echo esc_attr($options['credits_in_package']); ?>" class="regular-text" />
        <input type="hidden" id="home_values_credits_low" name="home_values_general[credits_low]" value="<?php echo esc_attr($options['credits_low']); ?>" class="regular-text" />
        <input type="hidden" id="home_values_next_refill_date" name="home_values_general[next_refill_date]" value="<?php echo esc_attr($options['next_refill_date']); ?>" class="regular-text" />
      </td>
    </tr>


    <!-- Show refill and credits box only if api key is set and is a premium package -->
    <?php if (!empty($options['api_key']) && $is_key_good && $options['credits_in_package'] > 10) : ?>
      <tr>
        <th scope="row"><label for="refresh_status"><?php _e('', 'home-values'); ?></label></th>
        <td>
          <div class="wrap">
            <div class="postbox">
              <div class="inside">
                <p><strong>Credits:</strong> You have <?php echo $options['credits']; ?> credits remaining.</p>
                <p><strong>Premium:</strong> You last purchased a premium package for <?php echo $options['credits_in_package']; ?> credits.</p>
                <!-- If auto refill enabled display message -->
                <?php if ($options['auto_refill_enabled']) : ?>
                  <p>
                    <!-- auto refill once credits is below 10% of total -->
                    <strong>Auto Refill:</strong> Your credits will be refilled once your balance goes below <?php $lower_limit = $options['credits_in_package'] * 0.10;
                                                                                                              echo $lower_limit; ?>.
                  </p>
                  <p> <a href="<?php echo $options['cancel_url']; ?>" class="button">Cancel Renewal</a></p>
                  <p><button type="button" id="refresh_status" class="button"><?php _e('Refresh Status', 'home-values'); ?></button></p>
                <?php else : ?>
                  <!-- meassage with renewal link -->
                  <p><strong>Auto Refill:</strong> Disabled</p>

                  <p><a href="<?php echo $options['renewal_url']; ?>" class="button-primary">Enable Auto-Refill</a></p>
                  <p><button type="button" id="refresh_status" class="button"><?php _e('Refresh Status', 'home-values'); ?></button></p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="home_values_test_api_key"><?php _e('', 'home-values'); ?></label></th>
        <td>
          <button type="button" id="test_api_key" class="button"><?php _e('Test The API Key', 'home-values'); ?></button>
          <div id="test_api_key_result"></div>
        </td>
      </tr>


    <?php else : // no package purchased so show the regular status box 
    ?>
      <!-- Show status box only if api key is set info available -->
      <?php if (!empty($options['api_key']) && $is_key_good) : ?>
        <tr>
          <th scope="row"><label for="refresh_status"><?php _e('', 'home-values'); ?></label></th>
          <td>
            <div class="wrap">
              <div class="postbox">
                <div class="inside">
                  <p>
                    <strong>Credits Left:</strong> <span id="credits-left"><?php echo $options['credits']; ?></span>
                  </p>
                  <?php if (isset($options['next_refill_date']) && !empty($options['next_refill_date'])) : ?>
                    <p>
                      <strong>Refill Date:</strong> <span id="next-refill-date"><?php echo date('Y-m-d', $options['next_refill_date']);
                                                                                ?></span>
                    </p>
                  <?php endif; ?>
                  <p>
                    <a href="<?php echo $options['renewal_url']; ?>" id="renewal-url" class="button-primary">Get More Credits</a>
                    <button type="button" id="refresh_status" class="button"><?php _e('Refresh Status', 'home-values'); ?></button>
                  </p>
                </div>
              </div>
            </div>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="home_values_test_api_key"><?php _e('', 'home-values'); ?></label></th>
          <td>
            <button type="button" id="test_api_key" class="button"><?php _e('Test The API Key', 'home-values'); ?></button>
            <div id="test_api_key_result"></div>
          </td>
        </tr>

      <?php endif; ?>
      <!--  / End status box -->
    <?php endif; ?>
    <!--  / End refill and credits box -->



    <!-- Show if Invalid API Key -->
    <?php if (!$is_key_good) : ?>
      <tr>
        <th scope="row"><label for="home_values_test_api_key"><?php _e('', 'home-values'); ?></label></th>
        <td>
          <div class="notice notice-error">
            <p><?php _e('Invalid API Key', 'home-values'); ?></p>
          </div>
        </td>
      </tr>
    <?php endif; ?>
    <!--  / End Invalid API Key -->


    <?php if ($is_network_admin) : ?>
      <tr>
        <th scope="row"><label for="home_values_lead_pool_blog"><?php _e('Lead Pool Blog', 'home-values'); ?></label></th>
        <td>
          <select id="home_values_lead_pool_blog" name="home_values_general[lead_pool_blog]">
            <option <?php selected($options['lead_pool_blog'], 0); ?> value="0"><?php _e('Lead pooling disabled', 'home-values'); ?></option>
            <?php foreach (get_sites() as $site) : ?>
              <option value="<?php echo esc_attr($site->blog_id); ?>" <?php selected($options['lead_pool_blog'], $site->blog_id); ?>>
                <?php echo esc_html($site->blogname); ?> (<?php echo esc_attr($site->blog_id); ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </td>
      </tr>
    <?php endif; ?>

  <?php endif; ?>

  <?php if (is_multisite() && is_network_admin()) : ?>
    <tr>
      <th scope="row"><label for="home_values_google_api_key"><?php _e('Google Places API Key (Required)', 'home-values'); ?></label></th>
      <td>
        <input type="text" id="home_values_google_api_key" name="home_values_general[google_api_key]" value="<?php echo esc_attr($options['google_api_key']); ?>" class="regular-text" />
        <p>Generate your Places API key here <a href="https://developers.google.com/maps/documentation/places/web-service/get-api-key" target="_blank"> https://developers.google.com/maps/documentation/places/web-service/get-api-key)</a></p>
      </td>
    </tr>
  <?php endif; ?>

  <?php if (!is_multisite()) : ?>
    <tr>
      <th scope="row"><label for="home_values_google_api_key"><?php _e('Google Places API Key (Required)', 'home-values'); ?></label></th>
      <td>
        <input type="text" id="home_values_google_api_key" name="home_values_general[google_api_key]" value="<?php echo esc_attr($options['google_api_key']); ?>" class="regular-text" />
        <p>Generate your Places API key here <a href="https://developers.google.com/maps/documentation/places/web-service/get-api-key" target="_blank"> https://developers.google.com/maps/documentation/places/web-service/get-api-key)</a></p>
      </td>
    </tr>
  <?php endif; ?>

  <tr>
    <th scope="row"><label for="home_values_adjust_values"><?php _e('Adjust Home Values', 'home-values'); ?></label></th>
    <td>
      <input type="range" id="home_values_adjust_values" name="home_values_general[adjust_values]" value="<?php echo esc_attr($options['adjust_values']); ?>" min="-100" max="100" />
      <span id="home_values_adjust_values_display"><?php echo esc_html($options['adjust_values']); ?>%</span>
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="home_values_shortcode_page"><?php _e('Create shortcode on new page', 'home-values'); ?></label></th>
    <td>
      <button type="button" id="create_shortcode_page" class="button"><?php _e('Create Shortcode Page', 'home-values'); ?></button>
      <div id="create_shortcode_page_result"></div>
    </td>
  </tr>
  <tr>
    <th scope="row"><?php _e('Load plugin CSS', 'home-values'); ?></th>
    <td>
      <input type="checkbox" id="home_values_load_css" name="home_values_general[load_css]" value="1" <?php checked($options['load_css'], 1); ?> />
      <label for="home_values_load_css"><?php _e('Load plugin CSS on the frontend', 'home-values'); ?></label>
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="home_values_webhooks"><?php _e('Webhooks', 'home-values'); ?></label></th>
    <td>
      <textarea id="home_values_webhooks" name="home_values_general[webhooks]" rows="5" cols="50" class="large-text code"><?php echo esc_textarea($options['webhooks']); ?></textarea>
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="test_webhooks"><?php _e('', 'home-values'); ?></label></th>
    <td>
      <button type="button" id="test_webhooks" class="button"><?php _e('Test Webhooks', 'home-values'); ?></button>
      <div id="test_webhooks_result"></div>
    </td>
  </tr>

  <?php
  // if is multisite and not network admin display button to use network settings
  if (is_multisite() && !$is_network_admin) :
  ?>
    <tr>
      <th scope="row"><label for="home_values_general_use_network"><?php _e('Use Network Settings', 'home-values'); ?></label></th>
      <td>
        <button type="button" id="general_use_network" class="button"><?php _e('Use Network Settings', 'home-values'); ?></button>
      </td>
    </tr>
  <?php endif; ?>
</table>

<script>
  document.getElementById('home_values_adjust_values').addEventListener('input', function() {
    document.getElementById('home_values_adjust_values_display').textContent = this.value + '%';
  });
  jQuery(document).ready(function() {
    // Listen for the form submission event
    jQuery("form").submit(function(e) {
      // Check if the field exists
      if (jQuery("#home_values_google_api_key").length) {
        var googleAPIKey = jQuery("#home_values_google_api_key").val();

        // If the Google API key field is empty, show the alert and prevent form submission
        if (googleAPIKey === "") {
          if (!confirm("Google places API key is required for the home value address autocomplete to work. Are you sure you want to continue?")) {
            e.preventDefault();
          }
        }
      }
    });
  });
</script>
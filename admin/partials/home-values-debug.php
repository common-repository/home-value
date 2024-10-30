<?php
$options = $is_network_admin ? get_site_option('home_values_debug', array()) : get_option('home_values_debug', array());

$my_ip_address = $_SERVER['REMOTE_ADDR'];
?>
<h3 class="title"><?php _e('Debugging', 'home-values'); ?></h3>
<p><?php _e('Debugging is only available to administrators.', 'home-values'); ?></p>
<table class="form-table">
  <tr>
    <th scope="row"><?php _e('Enable Debugging', 'home-values'); ?></th>
    <td>
      <input type="checkbox" id="home_values_enable_debugging" name="home_values_debug[enable_debugging]" value="1" <?php checked($options['enable_debugging'], 1); ?> />
      <label for="home_values_enable_debugging"><?php _e('Enable Debugging', 'home-values'); ?></label>
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="home_values_debug_ips"><?php _e('Debug IPs', 'home-values'); ?></label></th>
    <td>
      <textarea id="home_values_debug_ips" name="home_values_debug[debug_ips]" rows="5" cols="50" class="large-text code"><?php echo esc_textarea($options['debug_ips']); ?></textarea>
      <p class="description"><?php printf(__('Only show debugging info to specific IP addresses. Use spaces between IPs. You can also specify part of an IP address. Your address is %s', 'home-values'), $my_ip_address); ?></p>
    </td>
  </tr>
  <?php
  if (current_user_can('manage_options') && $options['enable_debugging'] == 1) {


    $log_file = HV_LOG_FILE;

    echo '<div class="wrap">';
    echo '<h4>Logs:</h4>';

    if (file_exists($log_file)) {
      $log_content = file_get_contents($log_file);
      echo '<pre style="max-height: 300px; overflow: scroll;">' . esc_html($log_content) . '</pre>';
    } else {
      echo '<p>No logs found.</p>';
    }
    echo '</div>';
  }
  ?>
  <tr>
    <th scope="row"><?php _e('Delete Log', 'home-values'); ?></th>
    <td>
      <button type="button" id="home_values_delete_log" class="button"><?php _e('Delete Log', 'home-values'); ?></button>
      <div id="home_values_delete_log_result"></div>
    </td>
  </tr>
</table>
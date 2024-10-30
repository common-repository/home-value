<?php
$options = get_option('home_values_uninstall', array());
$uninstall_nonce = wp_create_nonce('home_values_uninstall_nonce');
?>


<div class="wrap">
  <p><?php _e('This page will remove all the plugin tables and settings from the database and then deactivate the plugin.', 'home-values'); ?></p>
</div>

<table class="form-table">
  <tr>
    <th scope="row"><?php _e('Confirm Uninstall', 'home-values'); ?></th>
    <td>
      <input type="hidden" id="home_values_uninstall_nonce" value="<?php echo esc_attr($uninstall_nonce); ?>" />
      <input type="checkbox" id="home_values_uninstall_confirm" name="home_values_uninstall[uninstall_confirm]" value="1" />
      <label for="home_values_uninstall_confirm"><?php _e("Yes, I'm sure I want to remove all the plugin tables and settings.", 'home-values'); ?></label>
    </td>
  </tr>
  <tr>
    <th scope="row"><?php _e('Uninstall Plugin', 'home-values'); ?></th>
    <td>
      <button type="button" id="home_values_uninstall_plugin" class="button"><?php _e('Uninstall Plugin', 'home-values'); ?></button>
    </td>
  </tr>
</table>

<script>
  document.getElementById('home_values_uninstall_plugin').addEventListener('click', function() {
    if (document.getElementById('home_values_uninstall_confirm').checked) {
      const data = {
        'action': 'home_values_uninstall',
        'nonce': document.getElementById('home_values_uninstall_nonce').value
      };

      jQuery.post(ajaxurl, data, function(response) {
        if (response.success) {
          // Redirect to plugins page
          window.location.href = response.data.redirect_url;
        } else {
          alert(response.data.message);
          $('#home_values_uninstall').removeAttr('disabled');
        }
      });
    } else {
      alert('Please confirm that you want to remove all plugin tables and settings.');
    }
  });
</script>
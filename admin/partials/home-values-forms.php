<?php
// Check if the current user is a network admin in a multisite environment.
$is_network_admin = (is_multisite() && is_network_admin());

$options = $is_network_admin ? get_site_option('home_values_forms', array()) : get_option('home_values_forms', array());
$options = !$options ? get_site_option('home_values_forms', array()) : $options;
?>
<?php
// if is network admin display global message
if ($is_network_admin) : ?>
  <div class="notice notice-info">
    <p><?php _e('These settings are global and will be applied to all sites in the network. Local sites may override these settings in their dashboards.', 'home-values'); ?></p>
  </div>
<?php endif;
?>

<h3><?php _e('Address form', 'home-values'); ?></h3>

<table class="form-table">
  <tr>
    <th scope="row"><label for="address_field_placeholder"><?php _e('Address Field Placeholder', 'home-values'); ?></label></th>
    <td>
      <input type="text" id="address_field_placeholder" name="home_values_forms[address_field_placeholder]" value="<?php echo esc_attr($options['address_field_placeholder']); ?>" class="regular-text" />
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="submit_button_text"><?php _e('Submit Button Text', 'home-values'); ?></label></th>
    <td>
      <input type="text" id="submit_button_text" name="home_values_forms[submit_button_text]" value="<?php echo esc_attr($options['submit_button_text']); ?>" class="regular-text" />
    </td>
  </tr>
</table>


<h3><?php _e('Lead Forms', 'home-values'); ?></h3>
<table class="form-table">
  <tr>
    <th scope="row"><label for="email_field_placeholder"><?php _e('Email Field Placeholder', 'home-values'); ?></label></th>
    <td>
      <input type="text" id="email_field_placeholder" name="home_values_forms[email_field_placeholder]" value="<?php echo esc_attr($options['email_field_placeholder']); ?>" class="regular-text" />
    </td>
  </tr>
  <tr>
    <th scope="row"><?php _e('Show First Name Field', 'home-values'); ?></th>
    <td>
      <input type="checkbox" id="show_first_name_field" name="home_values_forms[show_first_name_field]" value="1" <?php checked($options['show_first_name_field'], 1); ?> />
      <label for="show_first_name_field"><?php _e('Show First Name Field', 'home-values'); ?></label>
    </td>
  </tr>
  <tr>
    <th scope="row"><?php _e('Require First Name', 'home-values'); ?></th>
    <td>
      <input type="checkbox" id="require_first_name" name="home_values_forms[require_first_name]" value="1" <?php checked($options['require_first_name'], 1); ?> />
      <label for="require_first_name"><?php _e('Require First Name', 'home-values'); ?></label>
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="first_name_field_placeholder"><?php _e('First Name Field Placeholder', 'home-values'); ?></label></th>
    <td>
      <input type="text" id="first_name_field_placeholder" name="home_values_forms[first_name_field_placeholder]" value="<?php echo esc_attr($options['first_name_field_placeholder']); ?>" class="regular-text" />
    </td>
  </tr>
  <tr>
    <th scope="row"><?php _e('Show Last Name Field', 'home-values'); ?></th>
    <td>
      <input type="checkbox" id="show_last_name_field" name="home_values_forms[show_last_name_field]" value="1" <?php checked($options['show_last_name_field'], 1); ?> />
      <label for="show_last_name_field"><?php _e('Show Last Name Field', 'home-values'); ?></label>
    </td>
  </tr>
  <tr>
    <th scope="row"><?php _e('Require Last Name Field', 'home-values'); ?></th>
    <td>
      <input type="checkbox" id="require_last_name_field" name="home_values_forms[require_last_name_field]" value="1" <?php checked($options['require_last_name_field'], 1); ?> />
      <label for="require_last_name_field"><?php _e('Require Last Name Field', 'home-values'); ?></label>
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="last_name_field_placeholder"><?php _e('Last Name Field Placeholder', 'home-values'); ?></label></th>
    <td>
      <input type="text" id="last_name_field_placeholder" name="home_values_forms[last_name_field_placeholder]" value="<?php echo esc_attr($options['last_name_field_placeholder']); ?>" class="regular-text" />
    </td>
  </tr>
  <tr>
    <th scope="row"><?php _e('Show Phone Number Field', 'home-values'); ?></th>
    <td>
      <input type="checkbox" id="show_phone_number_field" name="home_values_forms[show_phone_number_field]" value="1" <?php checked($options['show_phone_number_field'], 1); ?> />
      <label for="show_phone_number_field"><?php _e('Show Phone Number Field', 'home-values'); ?></label>
    </td>
  </tr>
  <tr>
    <th scope="row"><?php _e('Require Phone Number', 'home-values'); ?></th>
    <td>
      <input type="checkbox" id="require_phone_number" name="home_values_forms[require_phone_number]" value="1" <?php checked($options['require_phone_number'], 1); ?> />
      <label for="require_phone_number"><?php _e('Require Phone Number', 'home-values'); ?></label>
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="phone_number_placeholder"><?php _e('Phone Number Placeholder', 'home-values'); ?></label></th>
    <td>
      <input type="text" id="phone_number_placeholder" name="home_values_forms[phone_number_placeholder]" value="<?php echo esc_attr($options['phone_number_placeholder']); ?>" class="regular-text" />
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="submit_button_text"><?php _e('Submit Button Text', 'home-values'); ?></label></th>
    <td>
      <input type="text" id="submit_button_text" name="home_values_forms[submit_button_text]" value="<?php echo esc_attr($options['submit_button_text']); ?>" class="regular-text" />
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="submit_button_text"><?php _e('Lead Form Submit Button Text', 'home-values'); ?></label></th>
    <td>
      <input type="text" id="submit_button_text" name="home_values_forms[lead_form_submit_button_text]" value="<?php echo esc_attr($options['lead_form_submit_button_text']); ?>" class="regular-text" />
    </td>
  </tr>

</table>


<h3><?php _e('Lead Form Messages', 'home-values'); ?></h3>
<table class="form-table">
  <tr>
    <th scope="row"><label for="address_found_messaging"><?php _e('Address Found Messaging Above Lead Form', 'home-values'); ?></label></th>
    <td>
      <?php
      wp_editor($options['address_found_messaging'], 'address_found_messaging', array(
        'textarea_name' => 'home_values_forms[address_found_messaging]',
        'textarea_rows' => 10,
        'media_buttons' => true,
        'teeny' => true,
        'quicktags' => true,
        'wpautop' => false
      ));
      ?>
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="address_not_found_messaging"><?php _e('Address NOT Found Messaging Above Lead Form', 'home-values'); ?></label></th>
    <td>
      <?php
      wp_editor($options['address_not_found_messaging'], 'address_not_found_messaging', array(
        'textarea_name' => 'home_values_forms[address_not_found_messaging]',
        'textarea_rows' => 10,
        'media_buttons' => true,
        'teeny' => true,
        'quicktags' => true,
        'wpautop' => false
      ));
      ?>
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="form_thank_you_message"><?php _e('Form Thank You Message When Address is Not Found', 'home-values'); ?></label></th>
    <td>
      <?php
      wp_editor($options['form_thank_you_message'], 'form_thank_you_message', array(
        'textarea_name' => 'home_values_forms[form_thank_you_message]',
        'textarea_rows' => 10,
        'media_buttons' => true,
        'teeny' => true,
        'quicktags' => true,
        'wpautop' => false
      ));
      ?>
    </td>
  </tr>
  <?php
  // if is multisite and not network admin display button to use network settings
  if (is_multisite() && !$is_network_admin) :
  ?>
    <tr>
      <th scope="row"><label for="home_values_forms_use_network"><?php _e('Use Network Settings', 'home-values'); ?></label></th>
      <td>
        <button type="button" id="forms_use_network" class="button"><?php _e('Use Network Settings', 'home-values'); ?></button>
      </td>
    </tr>
  <?php endif; ?>

</table>
<?php
// Check if the current user is a network admin in a multisite environment.
$is_network_admin = (is_multisite() && is_network_admin());

$options = $is_network_admin ? get_site_option('home_values_emails', array()) : get_option('home_values_emails', array());
$options = !$options ? get_site_option('home_values_emails', array()) : $options;

$default_sender_email = 'noreply@' . parse_url(get_option('siteurl'), PHP_URL_HOST);
$default_sender_name = '[company]';
$default_new_lead_recipients = '[email]';
$default_new_lead_subject = 'New Home Value Requested!';
?>
<?php
// if is network admin display global message
if ($is_network_admin) : ?>
  <div class="notice notice-info">
    <p><?php _e('These settings are global and will be applied to all sites in the network. Local sites may override these settings in their dashboards.', 'home-values'); ?></p>
  </div>
<?php endif;
?>

<h3 class="title"><?php _e('Lead E-mail Settings', 'home-values'); ?></h3>
<p><?php _e('These are the settings for the e-mail sent when a new lead is created.', 'home-values'); ?></p>
<table class="form-table">
  <tr>
    <th scope="row"><label for="home_values_sender_email"><?php _e('Sender e-mail', 'home-values'); ?></label></th>
    <td>
      <input type="text" id="home_values_sender_email" name="home_values_emails[sender_email]" value="<?php echo esc_attr($options['sender_email'] ?? $default_sender_email); ?>" class="regular-text" />
      <p class="description"><?php _e('Send the e-mail from this e-mail address. Note that this value may be restricted by your webhost.', 'home-values'); ?></p>
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="home_values_sender_name"><?php _e('Sender name', 'home-values'); ?></label></th>
    <td>
      <input type="text" id="home_values_sender_name" name="home_values_emails[sender_name]" value="<?php echo esc_attr($options['sender_name'] ?? $default_sender_name); ?>" class="regular-text" />
      <p class="description"><?php _e('Send the e-mail with this sender name.', 'home-values'); ?></p>
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="home_values_new_lead_recipients"><?php _e('New lead recipients', 'home-values'); ?></label></th>
    <td>
      <textarea id="home_values_new_lead_recipients" name="home_values_emails[new_lead_recipients]" rows="5" cols="50" class="large-text code"><?php echo esc_textarea($options['new_lead_recipients'] ?? $default_new_lead_recipients); ?></textarea>
      <p class="description"><?php _e('To which e-mail addresses shall new leads be sent? One e-mail address per line. Shortcodes allowed.', 'home-values'); ?></p>
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="home_values_new_lead_subject"><?php _e('New lead subject', 'home-values'); ?></label></th>
    <td>
      <input type="text" id="home_values_new_lead_subject" name="home_values_emails[new_lead_subject]" value="<?php echo esc_attr($options['new_lead_subject'] ?? $default_new_lead_subject); ?>" class="regular-text" />
      <p class="description"><?php _e('Subject of the new lead e-mail. Valid shortcodes are [8b_home_value_first_name], [8b_home_value_last_name], [8b_home_value_email] and [8b_home_value_phone].', 'home-values'); ?></p>
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="home_values_new_lead_email"><?php _e('New lead e-mail', 'home-values'); ?></label></th>
    <td>
      <?php
      wp_editor($options['new_lead_email'] ?? '', 'home_values_new_lead_email', array(
        'textarea_name' => 'home_values_emails[new_lead_email]',
        'textarea_rows' => 10,
        'media_buttons' => false,
        'teeny' => true,
      ));
      ?>
      <p class="description"><?php _e('This is the text of the e-mail for new leads that is sent to the new lead e-mail recipients. Valid shortcodes are [8b_home_value_first_name], [8b_home_value_last_name], [8b_home_value_email], [8b_home_value_phone], [8b_home_value_searched_address], [8b_home_value_data_valuation_medium], [8b_home_value_data_size], [8b_home_value_data_beds] and [8b_home_value_data_baths].', 'home-values'); ?></p>
    </td>
  </tr>
  <?php
  // if is multisite and not network admin display button to use network settings
  if (is_multisite() && !$is_network_admin) :
  ?>
    <tr>
      <th scope="row"><label for="home_values_emails_use_network"><?php _e('Use Network Settings', 'home-values'); ?></label></th>
      <td>
        <button type="button" id="emails_use_network" class="button"><?php _e('Use Network Settings', 'home-values'); ?></button>
      </td>
    </tr>
  <?php endif; ?>
</table>
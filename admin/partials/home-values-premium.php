<?php
// Check if the current user is a network admin in a multisite environment.
$is_network_admin = (is_multisite() && is_network_admin());

$options = $is_network_admin ? get_site_option('home_values_general', array()) : get_option('home_values_general', array());
$api_key = isset($options['api_key']) ? $options['api_key'] : '';

$image_url = 'https://8blocks.s3.amazonaws.com/plugins/home-value/website/images/premium.png';
$linked_url = "https://homevalueplugin.com/renew?key={$api_key}";
?>

<div class="wrap">
  <h2><?php _e('Premium', 'home-values'); ?></h2>
  <p><?php _e('Upgrade to the premium version of Home Values for additional features and benefits.', 'home-values'); ?></p>
  <a href="<?php echo esc_url($linked_url); ?>" target="_blank">
    <img src="<?php echo esc_url($image_url); ?>" alt="<?php _e('Home Values Premium', 'home-values'); ?>" />
  </a>
</div>
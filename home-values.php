<?php

/**
 * Plugin Name: Home Value
 * Description: Home Value provides your website visitors the ability to get accurate home price valuations of their applicable property(s).
 * Version: 3.1.5
 * Author: 8blocks
 * Author Email:	support@8blocks.com
 * Author URI:		http://8blocks.com
 * License: GPL-2.0+
 * Text Domain: home-values
 */

if (!defined('WPINC')) {
  die;
}

// Plugin directory path and URL.
define('HOME_VALUES_PLUGIN_VERSION', '3.1.5');
define('HOME_VALUES_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HOME_VALUES_PLUGIN_URL', plugin_dir_url(__FILE__));
$site_id = is_multisite() ? get_current_blog_id() : 1;
define('HV_LOG_FILE', HOME_VALUES_PLUGIN_DIR . 'logs/log-' . $site_id . '.txt');
define('HV_DEBUG', true);

// Include the helper functions
require_once HOME_VALUES_PLUGIN_DIR . 'includes/home-values-functions.php';

// Include the core class.
require_once HOME_VALUES_PLUGIN_DIR . 'includes/class-home-values.php';
require_once HOME_VALUES_PLUGIN_DIR . 'includes/class-home-values-install.php';

new Home_Values_Install();

// Run the plugin.
$home_values = new Home_Values(plugin_basename(__FILE__));
$home_values->run();

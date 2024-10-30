<?php
global $wp_version;

$home_values_version = '1.0.0'; // Replace this with your plugin's actual version
$php_version = phpversion();
$wp_upload_dir = wp_upload_dir();

$abspath = ABSPATH;
$wp_plugin_dir = WP_PLUGIN_DIR;
$plugin_dir_path = plugin_dir_path(__FILE__);
$plugin_dir_url = plugin_dir_url(__FILE__);
$plugin_basename = plugin_basename(__FILE__);
$plugin_file_path = __FILE__;

$relative_plugin_path = str_replace($wp_plugin_dir, '', $plugin_dir_path);

$paths = (object) array(
  '__FILE__' => $plugin_file_path,
  'name' => 'home_values',
  'filename' => basename($plugin_file_path),
  'filename_from_plugin_directory' => $relative_plugin_path . basename($plugin_file_path),
  'path_from_plugin_directory' => $relative_plugin_path,
  'path_from_base_directory' => 'wp-content/plugins' . $relative_plugin_path,
  'url' => $plugin_dir_url,
  'ABSPATH' => $abspath,
  'WP_PLUGIN_DIR' => $wp_plugin_dir,
);

$max_execution_time = ini_get('max_execution_time');
$php_memory_limit = ini_get('memory_limit');
$wp_memory_limit = WP_MEMORY_LIMIT . "
This can be increased by adding the following to your wp-config.php:
define('WP_MEMORY_LIMIT', '512M');";

$debug_code = "Add the following lines to your wp-config.php to help find out why errors or blank screens are occurring:
ini_set('display_errors','On');
define('WP_DEBUG', true);";
$allow_url_fopen = ini_get('allow_url_fopen') ? 'Enabled' : 'Disabled';
?>

<div class="wrap">
  <h2><?php _e('System Info', 'home-values'); ?></h2>
  <table class="widefat">
    <thead>
      <tr>
        <th><?php _e('Setting', 'home-values'); ?></th>
        <th><?php _e('Value', 'home-values'); ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?php _e('Home Values Plugin Version', 'home-values'); ?></td>
        <td><?php echo esc_html($home_values_version); ?></td>
      </tr>
      <tr>
        <td><?php _e('WordPress Version', 'home-values'); ?></td>
        <td><?php echo esc_html($wp_version); ?></td>
      </tr>
      <tr>
        <td><?php _e('PHP Version', 'home-values'); ?></td>
        <td><?php echo esc_html($php_version); ?></td>
      </tr>
      <tr>
        <td><?php _e('WordPress Upload Directory Array', 'home-values'); ?></td>
        <td>
          <pre><?php print_r($wp_upload_dir); ?></pre>
        </td>
      </tr>
      <tr>
        <td><?php _e('Plugin Paths', 'home-values'); ?></td>
        <td>
          <pre><?php echo print_r($paths); ?></pre>
        </td>
      </tr>
      <tr>
        <td><?php _e('PHP Maximum Execution Time', 'home-values'); ?></td>
        <td><?php echo esc_html($max_execution_time); ?> seconds</td>
      </tr>
      <tr>
        <td><?php _e('PHP Memory Limit', 'home-values'); ?></td>
        <td><?php echo esc_html($php_memory_limit); ?></td>
      </tr>
      <tr>
        <td><?php _e('WordPress Memory Limit', 'home-values'); ?></td>
        <td>
          <pre><?php echo esc_html($wp_memory_limit); ?></pre>
        </td>
      </tr>
      <tr>
        <td><?php _e('Debug Code', 'home-values'); ?></td>
        <td>
          <pre><?php echo esc_html($debug_code); ?></pre>
        </td>
      </tr>
      <tr>
        <td><?php _e('allow_url_fopen', 'home-values'); ?></td>
        <td><?php echo esc_html($allow_url_fopen); ?></td>
      </tr>
    </tbody>
  </table>
</div>
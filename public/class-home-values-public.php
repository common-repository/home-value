<?php
class Home_Values_Public
{
  private $plugin_name;
  private $version;
  private $shortcodes;

  public function __construct($plugin_name, $version)
  {
    $this->plugin_name = $plugin_name;
    $this->version = $version;

    require_once plugin_dir_path(__FILE__) . 'class-home-values-shortcodes.php';
    $this->shortcodes = new Home_Values_Shortcodes();
  }

  public function enqueue_styles()
  {
    if (!home_values_get_setting('general', 'load_css')) return;
    wp_enqueue_style(
      $this->plugin_name,
      HOME_VALUES_PLUGIN_URL . 'public/css/home-values-public.css',
      array(),
      $this->version,
      'all'
    );
  }

  public function enqueue_scripts()
  {
    // wp_enqueue_script(
    //   $this->plugin_name,
    //   HOME_VALUES_PLUGIN_URL . 'public/js/home-values-public.js',
    //   array('jquery'),
    //   $this->version,
    //   false
    // );
  }

  // Add your public-facing methods here.
}

<?php

require_once HOME_VALUES_PLUGIN_DIR . 'includes/class-home-values-loader.php';
require_once HOME_VALUES_PLUGIN_DIR . 'admin/class-home-values-admin.php';
require_once HOME_VALUES_PLUGIN_DIR . 'admin/class-home-values-settings.php';
require_once HOME_VALUES_PLUGIN_DIR . 'public/class-home-values-public.php';


class Home_Values
{
  private $loader;
  private $plugin_name;
  private $plugin_file;
  private $version;
  private $cpt;

  public function __construct($plugin_file)
  {
    $this->plugin_name = 'home_values';
    $this->plugin_file = $plugin_file;
    $this->version = '1.0.0';
    $this->load_dependencies();
    $this->define_admin_hooks();
    $this->define_public_hooks();

    require_once plugin_dir_path(__FILE__) . 'class-home-values-cpt.php';
    $this->cpt = new Home_Values_CPT();
  }

  private function load_dependencies()
  {
    $this->loader = new Home_Values_Loader();
  }

  private function define_admin_hooks()
  {
    $plugin_admin = new Home_Values_Admin($this->plugin_name, $this->plugin_file, $this->version);
    $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
    $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
    $this->loader->add_action('network_admin_menu', $plugin_admin, 'add_plugin_network_admin_menu');

    $plugin_settings = new Home_Values_Settings($this->plugin_name);
    // Register settings for the admin page
    $this->loader->add_action('admin_init', $plugin_settings, 'register_settings');
    // Save settings for network admin pages
    $this->loader->add_action('network_admin_edit_' . $this->plugin_name . '_options', $plugin_settings, 'save_network_settings');
  }

  private function define_public_hooks()
  {
    $plugin_public = new Home_Values_Public($this->plugin_name, $this->version);
    $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
    $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
  }

  public function run()
  {
    $this->loader->run();
  }
}

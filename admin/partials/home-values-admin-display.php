<?php
// Get the current active tab.
$active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
$is_network_admin = (is_multisite() && is_network_admin());


// write_log('Attempting to load the ' . $active_tab . ' tab.');

// Available tabs
$tabs = array(
  'general' => __('General', 'home-values'),
  'forms' => __('Forms', 'home-values'),
  'emails' => __('Emails', 'home-values'),
  'premium' => __('Premium', 'home-values'),
  'system_info' => __('System Info', 'home-values'),
  'debug' => __('Debug', 'home-values'),
  'uninstall' => __('Uninstall', 'home-values'),
);

$subpage = $is_network_admin ? '' : 'post_type=8b_hv_lead&';
?>

<div class="wrap">
  <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

  <h2 class="nav-tab-wrapper">
    <?php foreach ($tabs as $tab_slug => $tab_title) : ?>
      <a href="?<?php echo $subpage; ?>page=<?php echo urlencode($this->plugin_name); ?>&tab=<?php echo urlencode($tab_slug); ?>" class="nav-tab <?php echo $active_tab === $tab_slug ? 'nav-tab-active' : ''; ?>">
        <?php echo esc_html($tab_title); ?>
      </a>
    <?php endforeach; ?>
  </h2>

  <?php if ($is_network_admin) : ?>
    <form method="post" action="<?php echo network_admin_url('edit.php?action=' . $this->plugin_name . '_options'); ?>">
    <?php else : ?>
      <form method="post" action="options.php">
      <?php endif; ?>

      <?php
      // Load the content of the active tab.
      switch ($active_tab) {
        case 'general':
          settings_fields($this->plugin_name . '_general');
          do_settings_sections($this->plugin_name . '_general');
          include HOME_VALUES_PLUGIN_DIR . 'admin/partials/home-values-general.php';
          break;

        case 'forms':
          settings_fields($this->plugin_name . '_forms');
          do_settings_sections($this->plugin_name . '_forms');
          include HOME_VALUES_PLUGIN_DIR . 'admin/partials/home-values-forms.php';
          break;

        case 'emails':
          settings_fields($this->plugin_name . '_emails');
          do_settings_sections($this->plugin_name . '_emails');
          include HOME_VALUES_PLUGIN_DIR . 'admin/partials/home-values-emails.php';
          break;

        case 'premium':
          settings_fields($this->plugin_name . '_premium');
          do_settings_sections($this->plugin_name . '_premium');
          include HOME_VALUES_PLUGIN_DIR . 'admin/partials/home-values-premium.php';
          break;

        case 'system_info':
          settings_fields($this->plugin_name . '_system_info');
          do_settings_sections($this->plugin_name . '_system_info');
          include HOME_VALUES_PLUGIN_DIR . 'admin/partials/home-values-system-info.php';
          break;

        case 'debug':
          settings_fields($this->plugin_name . '_debug');
          do_settings_sections($this->plugin_name . '_debug');
          include HOME_VALUES_PLUGIN_DIR . 'admin/partials/home-values-debug.php';
          break;

        case 'uninstall':
          settings_fields($this->plugin_name . '_uninstall');
          do_settings_sections($this->plugin_name . '_uninstall');
          include HOME_VALUES_PLUGIN_DIR . 'admin/partials/home-values-uninstall.php';
          break;
      }

      // Display the save settings button for every tab except System Info.
      if ($active_tab !== 'system_info' && $active_tab !== 'uninstall' && $active_tab !== 'premium') {
        submit_button();
      }
      ?>
      </form>
</div>
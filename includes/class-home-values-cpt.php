<?php

class Home_Values_CPT
{
  public function __construct()
  {
    add_action('init', array($this, 'register_lead_post_type'));
    add_action('init', array($this, 'register_lead_tag_taxonomy'));

    add_action('admin_menu', array($this, 'add_admin_menu_items'));

    add_filter('manage_8b_hv_lead_posts_columns', array($this, 'customize_lead_columns'));
    add_action('manage_8b_hv_lead_posts_custom_column', array($this, 'populate_lead_columns'), 10, 2);

    add_action('admin_post_home_values_export_csv', array($this, 'export_csv'));
  }

  public function register_lead_post_type()
  {
    // Custom post type arguments
    $args = array(
      'labels' => array(
        'name' => __('Leads', 'home-values'),
        'singular_name' => __('Lead', 'home-values'),
        'all_items' => __('All Leads', 'home-values'),
      ),
      'public' => true,
      'supports' => array('title', 'editor', 'custom-fields'),
      'menu_icon' => 'dashicons-admin-users',
      'has_archive' => true,
      'show_in_rest' => true,
      'show_in_menu' => false, // We'll add the menu manually later
    );

    register_post_type('8b_hv_lead', $args);
  }

  public function register_lead_tag_taxonomy()
  {
    // Taxonomy arguments
    $args = array(
      'label' => __('Tags', 'home-values'),
      'public' => true,
      'hierarchical' => false,
      'show_in_rest' => true,
    );

    register_taxonomy('8b_hv_lead_tag', '8b_hv_lead', $args);
  }

  public function add_admin_menu_items()
  {
    $menu_slug = 'edit.php?post_type=8b_hv_lead';

    add_menu_page(
      __('8b Home Value', 'home-values'),
      __('8b Home Value', 'home-values'),
      'manage_options',
      $menu_slug,
      '',
      'dashicons-admin-home',
      25
    );

    add_submenu_page(
      $menu_slug,
      __('All Leads', 'home-values'),
      __('All Leads', 'home-values'),
      'manage_options',
      $menu_slug
    );

    add_submenu_page(
      $menu_slug,
      __('Tags', 'home-values'),
      __('Tags', 'home-values'),
      'manage_options',
      'edit-tags.php?taxonomy=8b_hv_lead_tag&post_type=8b_hv_lead'
    );

    add_submenu_page(
      $menu_slug,
      __('Export', 'home-values'),
      __('Export', 'home-values'),
      'manage_options',
      '8b_hv_export',
      array($this, 'export_page_callback') // Replace with your export page callback
    );
  }


  public function export_page_callback()
  {
    echo '<h1>' . __('Export', 'home-values') . '</h1>';
    echo '<form method="POST" action="' . admin_url('admin-post.php') . '">';
    echo '<input type="hidden" name="action" value="home_values_export_csv">';
    submit_button(__('Export Leads as CSV', 'home-values'));
    echo '</form>';
  }

  public function export_csv()
  {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.', 'home-values'));
    }

    // Set the CSV headers
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="leads_export.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $file = fopen('php://output', 'w');

    // Write the CSV column names
    fputcsv($file, array('First Name', 'Last Name', 'Email', 'Phone'));

    // Fetch the leads
    $args = array(
      'post_type' => '8b_hv_lead',
      'post_status' => 'publish',
      'posts_per_page' => -1,
    );
    $leads_query = new WP_Query($args);

    // Write the leads data to the CSV file
    if ($leads_query->have_posts()) {
      while ($leads_query->have_posts()) {
        $leads_query->the_post();
        $lead_id = get_the_ID();
        $first_name = get_post_meta($lead_id, 'lead_first_name', true);
        $last_name = get_post_meta($lead_id, 'lead_last_name', true);
        $phone = get_post_meta($lead_id, 'lead_phone', true);
        $email = get_post_meta($lead_id, 'lead_email', true);
        // $content = get_the_content();

        fputcsv($file, array($first_name, $last_name, $email, $phone,));
      }
      wp_reset_postdata();
    }

    fclose($file);
    exit;
  }

  public function settings_page_callback()
  {
    // Redirect to the General Settings tab
    wp_safe_redirect(admin_url('admin.php?page=home_values'));
    exit;
  }

  public function customize_lead_columns($columns)
  {
    $new_columns = array(
      'cb' => $columns['cb'],
      'name' => __('Name', 'home-values'),
      'info' => __('Info', 'home-values'),
      'tag' => __('Tag', 'home-values'),
      'submitted_on' => __('Submitted On', 'home-values'),
    );

    return $new_columns;
  }

  public function populate_lead_columns($column, $post_id)
  {
    switch ($column) {
      case 'name':
        $first_name = get_post_meta($post_id, 'lead_first_name', true);
        $last_name = get_post_meta($post_id, 'lead_last_name', true);
        $edit_link = get_edit_post_link($post_id);
        echo '<a href="' . $edit_link . '">' . $first_name . ' ' . $last_name . '</a>';
        break;

      case 'info':
        $phone = get_post_meta($post_id, 'lead_phone', true);
        $email = get_post_meta($post_id, 'lead_email', true);
        // mailto link
        $email = '<a href="mailto:' . $email . '">' . $email . '</a>';
        echo $email . '<br>' . $phone;
        break;

      case 'tag':
        $tags = get_the_terms($post_id, '8b_hv_lead_tag');
        if ($tags) {
          $tag_names = array();
          foreach ($tags as $tag) {
            $tag_names[] = $tag->name;
          }
          echo implode(', ', $tag_names);
        } else {
          echo '—';
        }
        break;

      case 'submitted_on':
        echo get_the_date('', $post_id);
        break;
    }
  }
}

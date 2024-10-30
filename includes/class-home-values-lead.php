<?php

class Home_Values_Lead
{
  public $id;
  public $first_name;
  public $last_name;
  public $phone;
  public $email;
  public $description;
  public $meta;
  public $post;

  public function __construct($lead_id = null, $data = null, $description = null)
  {
    if ($lead_id) {
      $this->id = $lead_id;
      $this->post = get_post($lead_id);
      $this->first_name = get_post_meta($lead_id, 'lead_first_name', true);
      $this->last_name = get_post_meta($lead_id, 'lead_last_name', true);
      $this->phone = get_post_meta($lead_id, 'lead_phone', true);
      $this->email = get_post_meta($lead_id, 'lead_email', true);
      $this->meta = get_post_meta($lead_id);
      $this->description = $this->post->post_content;
    } elseif ($data && $description) {
      $this->create_lead($data, $description);
    }

    return $this;
  }

  public function create_lead($data, $description = '')
  {
    $lead_title = $data['lead_first_name'] . ' ' . $data['lead_last_name'] . ' ' . $data['lead_phone'] . ' ' . $data['lead_email'];

    // check if lead already exists
    $lead_query = new WP_Query(array(
      'post_type' => '8b_hv_lead',
      'post_status' => 'publish',
      'posts_per_page' => 1,
      'meta_query' => array(
        'relation' => 'AND',
        array(
          'key' => 'lead_first_name',
          'value' => $data['lead_first_name'],
        ),
        array(
          'key' => 'lead_last_name',
          'value' => $data['lead_last_name'],
        ),
        array(
          'key' => 'lead_phone',
          'value' => $data['lead_phone'],
        ),
        array(
          'key' => 'lead_email',
          'value' => $data['lead_email'],
        ),
      ),
    ));

    if ($lead_query->have_posts()) {
      $lead_query->the_post();
      $lead_id = get_the_ID();
      $lead_description = get_the_content();
      wp_reset_postdata();

      if ($lead_description === $description) {
        return new Home_Values_Lead($lead_id);
      }
    }



    // Create a new lead as a custom post
    $lead_id = wp_insert_post(array(
      'post_title' => $lead_title,
      'post_content' => $description,
      'post_type' => '8b_hv_lead',
      'post_status' => 'publish',
      'meta_input' => $data,
    ));

    // Add the post meta data
    foreach ($data as $key => $value) {
      if (!add_post_meta($lead_id, $key, $value, true)) {
        write_log('error adding post meta');
      }
    }

    // Save the post
    wp_update_post(array(
      'ID' => $lead_id,
      'post_title' => $lead_title,
    ));

    // Set the properties of the current instance
    $this->id = $lead_id;
    $this->post = get_post($lead_id);
    $this->first_name = $data['lead_first_name'];
    $this->last_name = $data['lead_last_name'];
    $this->phone = $data['lead_phone'];
    $this->email = $data['lead_email'];
    $this->meta = get_post_meta($lead_id);
    $this->description = $description;
  }

  public function tag_lead($lead_id, $tag)
  {
    // Check if the given tag is valid
    if ($tag === 'Address found' || $tag === 'Address not found') {
      // Assign the tag to the lead
      wp_set_object_terms($lead_id, $tag, '8b_hv_lead_tag', false);
    } else {
      // Throw an exception if an invalid tag is provided
      throw new InvalidArgumentException("Invalid tag provided: '{$tag}'. Allowed tags are 'Address found' and 'Address not found'.");
    }
  }

  public function update_lead($lead_id, $data)
  {
    $lead_title = $data['lead_first_name'] . ' ' . $data['lead_last_name'] . ' ' . $data['lead_phone'] . ' ' . $data['lead_email'];

    // Update the post meta data
    foreach ($data as $key => $value) {
      if (!update_post_meta($lead_id, $key, $value, true)) {
        write_log('error adding post meta');
      }
    }

    // Update the lead post
    wp_update_post(array(
      'ID' => $lead_id,
      'post_title' => $lead_title,
    ));
  }

  public function delete_lead($lead_id)
  {
    // Delete the lead post
    wp_delete_post($lead_id, true);

    // Optionally, delete any related post meta data if needed
  }

  public function save_lead($lead)
  {
    // This method can be used to create or update a lead based on the presence of an ID
    if (isset($lead['ID']) && !empty($lead['ID'])) {
      $this->update_lead($lead['ID'], $lead);
    } else {
      $this->create_lead($lead);
    }
  }
}

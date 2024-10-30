<?php  ?>
<form action="/" enctype="multipart/form-data" method="post">
  <div class="form_item lead_first_name required">
    <label for="lead_first_name"><?php echo $first_name_field_placeholder; ?></label>
    <input aria-required="true" class="text required does_not_validate linput input1" id="lead_first_name" name="8b_home_value[lead_first_name]" placeholder="<?php echo $first_name_field_placeholder; ?>" required="true" type="text">

  </div>
  <div class="form_item lead_last_name required">
    <label for="lead_last_name"><?php echo $last_name_field_placeholder; ?></label>
    <input aria-required="true" class="text required does_not_validate rinput input2" id="lead_last_name" name="8b_home_value[lead_last_name]" placeholder="<?php echo $last_name_field_placeholder; ?>" required="true" type="text" value="">

  </div>
  <div class="form_item lead_phone required">
    <label for="lead_phone"><?php echo $phone_number_placeholder; ?></label>
    <input aria-required="true" class="text required does_not_validate linput input3" id="lead_phone" name="8b_home_value[lead_phone]" placeholder="<?php echo $phone_number_placeholder; ?>" required="true" type="text" value="">

  </div>
  <div class="form_item lead_email required">
    <label for="lead_email"><?php echo $email_field_placeholder; ?></label>
    <input aria-required="true" class="text required does_not_validate rinput input4" id="lead_email" name="8b_home_value[lead_email]" placeholder="<?php echo $email_field_placeholder; ?>" required="true" type="text" value="">

  </div>
  <div class="form_item hv_submit button-primary">

    <input class="button-primary submit" id="button_submit" name="8b_home_value[submit]" type="submit" value="<?php $submit_label = !empty($lead_form_submit_button_text) ? $lead_form_submit_button_text : 'Get My Values!';
                                                                                                              echo $submit_label; ?>">

  </div>
  <?php do_action('home_values_after_lead_form'); ?>
</form>
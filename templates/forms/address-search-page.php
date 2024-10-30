<?php

/**
 * Available template variables:
 * 
 * 1. $google_api_key: The Google API key for your application.
 * 2. $js: The URL of the home-values-public JavaScript file.
 * 3. $address_field_placeholder: The placeholder text for the Address field.
 * 4. $submit_button_text: The text to display on the submit button.
 * 5. $email_field_placeholder: The placeholder text for the Email Address field.
 * 6. $show_first_name_field: A boolean indicating if the First Name field should be shown (1 for true, 0 for false).
 * 7. $first_name_field_placeholder: The placeholder text for the First Name field.
 * 8. $show_last_name_field: A boolean indicating if the Last Name field should be shown (1 for true, 0 for false).
 * 9. $last_name_field_placeholder: The placeholder text for the Last Name field.
 * 10. $show_phone_number_field: A boolean indicating if the Phone Number field should be shown (1 for true, 0 for false).
 * 11. $phone_number_placeholder: The placeholder text for the Phone Number field.
 * 12. $address_found_messaging: The messaging to display when an address is found.
 * 13. $address_not_found_messaging: The messaging to display when an address is not found.
 * 14. $form_thank_you_message: The messaging to display after submitting the form.
 */

?>

<div id="8b-errorMessage" style="display: none; color: red;"></div>
<div id="8b-home-value" class="8b_home_value home-value">
  <div class="ask_for_address">
    <form id="8b-addressForm" action="" enctype="multipart/form-data" method="post">
      <div class="form_item form_item_plainview_sdk_eightb_home_value_form2_inputs_text_address form_item_text form_item_input address required">
        <label for="plainview_sdk_eightb_home_value_form2_inputs_text_address">Enter your address</label>
        <input aria-required="true" class="address text required" id="plainview_sdk_eightb_home_value_form2_inputs_text_address" maxlength="128" name="8b_home_value[address]" placeholder="<?php echo $address_field_placeholder ? $address_field_placeholder : 'Enter your address'; ?>" required="true" size="32" type="text" />
      </div>
      <!-- This hidden input stores the address Google gives us. -->
      <div class="form_item form_item_plainview_sdk_eightb_home_value_form2_inputs_hidden_found_address form_item_hidden form_item_input found_address" hidden="hidden">
        <input class="found_address hidden" hidden="hidden" id="plainview_sdk_eightb_home_value_form2_inputs_hidden_found_address" name="8b_home_value[found_address]" type="hidden" />
      </div>
      <div class="form_item form_item_plainview_sdk_eightb_home_value_wordpress_form2_inputs_primary_button_search_for_address form_item_submit form_item_input button-primary">
        <input class="button-primary submit" id="plainview_sdk_eightb_home_value_wordpress_form2_inputs_primary_button_search_for_address" name="8b_home_value[search_for_address]" type="submit" value="<?php echo $submit_button_text ? $submit_button_text : 'Search'; ?>" />
      </div>
    </form>
  </div>
</div>
<script src="<?php echo $js; ?>"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_api_key ? $google_api_key : ''; ?>&libraries=places&callback=initAutocomplete"></script>
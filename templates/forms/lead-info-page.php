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
 * 15. $valuation_found: A boolean indicating if a valuation was found (1 for true, 0 for false).
 * 16. $valuation: An array containing information about the valuation, such as source, address, valuation amounts (estimated, high, low), property attributes, comparables, and other details.
 *
 * For the $valuation array, here is a breakdown of the nested variables:
 *
 * - $valuation_found: A boolean indicating if a valuation was found (1 for true, 0 for false).
 * - $source: The source of the valuation data.
 * - $address: An array containing address details like street, city, state, zip, latitude, and longitude.
 * - $valuation: An array containing the estimated market value (valuation_emv), high value (valuation_high), and low value (valuation_low).
 * - $attributes: An array containing property attributes such as bedrooms, bathrooms, size, lot size, year built, stories, property type, taxes, sale date, and sale price.
 * - $comparables: An array of comparable properties, each with their own address, valuation, and attributes arrays.
 * - $credits_remaining: The number of credits remaining for valuation requests.
 * - $next_refill_date: The timestamp for the next refill date of credits.
 * - $street: The full street address of the property.
 * - $zipcode: The zipcode of the property.
 *
 * Please note that the $comparables array contains multiple properties, each with their own set of variables similar to the $valuation array (address, valuation, and attributes).
 */

if ($valuation_found) : ?>

  <div id="8b-home-value" class="8b_home_value home-value">
    <div class="address_found">
      <?php echo $address_found_messaging; ?>
      <div class="street_view">
        <?php if ($streetview_exists) : ?>
          <img src="<?php echo $address_street_view; ?>" data-url="<?php echo $address_street_view; ?>" />
        <?php else : ?>
          <iframe src="https://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=<?php echo urlencode($address_street_view); ?>&z=14&output=embed" width="1280" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
        <?php endif; ?>
      </div>

      <div class="user_info_form">
        <?php echo $lead_form; ?>
      </div>
    </div>
  </div>

<?php else : ?>

  <div id="8b-home-value" class="8b_home_value home-value">
    <div class="address_not_found">
      <?php echo $address_not_found_messaging; ?>
      <div class="street_view">
        <iframe src="https://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=<?php echo urlencode($address_street_view); ?>&z=14&output=embed" width="1280" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
      </div>
      <div class="user_info_form">
        <?php echo $lead_form; ?>
      </div>
    </div>
  </div>


<?php endif; ?>
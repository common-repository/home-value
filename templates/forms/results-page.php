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

// No Valuations found
if (!isset($valuation)) { ?>
<div class="no_address">
  <?php echo do_shortcode($form_thank_you_message); ?>
</div>
<?php } else {
  // Valuation found
?>

  <div class="8b_home_value home-value">
    <div class="show_value">
      <p class="hv_address"><?php echo $valuation['address']['street']; ?></p>
      <p class="hv_value">
        <?php
        echo isset($valuation['valuation']['valuation_emv']) ? '$' . number_format($valuation['valuation']['valuation_emv'], 0, '.', ',') : 'N/A';
        ?>
      </p>
      <div class="hv_range"></div>
      <p class="hv_half hv_low">
        <?php
        echo isset($valuation['valuation']['valuation_low']) ? '$' . number_format($valuation['valuation']['valuation_low'], 0, '.', ',') : 'N/A';
        ?>
      </p>
      <p class="hv_half hv_high">
        <?php
        echo isset($valuation['valuation']['valuation_high']) ? '$' . number_format($valuation['valuation']['valuation_high'], 0, '.', ',') : 'N/A';
        ?>
      </p>
      <p class="hv_third hv_baths"><?php echo isset($valuation['attributes']['bedrooms']) ? $valuation['attributes']['bedrooms'] : 'N/A'; ?><br /><span>bed</span></p>
      <p class="hv_third hv_sqft"><?php echo isset($valuation['attributes']['size']) ? number_format($valuation['attributes']['size'], 0, '.', ',') : 'N/A'; ?><br /><span>sqft</span></p>
      <p class="hv_third hv_stories"><?php echo isset($valuation['attributes']['bathrooms']) ? $valuation['attributes']['bathrooms'] : 'N/A'; ?><br /><span>bath</span></p>
    </div>
    <?php
    if (isset($valuation['comparables']) && $valuation['comparables']) {
      home_values_get_template_part('comparables', null, ['comparables' => $valuation['comparables']]);
    }
    ?>
  </div>

<?php } ?>
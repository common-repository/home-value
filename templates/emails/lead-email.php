<?php

/**
 * Available template variables:
 * 
 * The array contains multiple elements, each with the following variables:
 *
 * 1. $lead_first_name: The first name of the lead.
 * 2. $lead_last_name: The last name of the lead.
 * 3. $lead_phone: The phone number of the lead.
 * 4. $lead_email: The email address of the lead.
 * 5. $searched_address: The searched address in the format "street, city, state, country".
 * 6. $valuation: An array containing information about the valuation, such as source, address, valuation amounts (estimated, high, low), property attributes, comparables, and other details.
 *
 * For the $valuation array, here is a breakdown of the nested variables:
 *
 * - $valuation_found: A boolean indicating if a valuation was found (1 for true, 0 for false).
 * - $source: The source of the valuation data.
 * - $address: An array containing address details like street, city, state, zip, latitude, and longitude.
 * - $valuation: An array containing the estimated market value (valuation_emv), high value (valuation_high), and low value (valuation_low).
 * - $attributes: An array containing property attributes such as bedrooms, bathrooms, size, lot size, year built, stories, property type, taxes, sale date, and sale price.
 * - $comparables: An array of comparable properties, each with their own address, valuation, and attributes arrays.
 * - $street: The full street address of the property.
 * - $zipcode: The zipcode of the property.
 *
 * Please note that the $comparables array contains multiple properties, each with their own set of variables similar to the $valuation array (address, valuation, and attributes).
 */

?>

<p style="font-family: Arial;">New Value Requested for <?php echo __($searched_address, 'home-values'); ?></p>
<p style="font-family: Arial; line-height: 1.5;">Suggested Value: <?php
                    if ($valuation['valuation_found']) {
                      echo '$' . number_format($valuation['valuation']['valuation_emv'], 0, '.', ',');
                    } else {
                      echo 'Property was not found in our dataset so we are manually creating a valuation for you.';
                    }
                    ?>
</p>
<p style="font-family: Arial; line-height: 1.5;">Home Specs: <?php
                    if ($valuation['valuation_found']) {
                       echo __($valuation['attributes']['size']). 'sqft ' .__($valuation['attributes']['bedrooms']). 'bed ' .__($valuation['attributes']['bathrooms']). 'bath';
                    } else {
                        echo 'N/A';
                    }
                    ?>     
</p>                    
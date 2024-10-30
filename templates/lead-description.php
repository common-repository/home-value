<?php if (isset($valuation)) { ?>
  <h3>Value Found!</h3><a href="mailto:<?php echo $lead_email; ?>"><?php echo $lead_first_name; ?> <?php echo $lead_last_name; ?></a> searched for <em><strong><?php echo $searched_address; ?></strong></em> and was given a median value of <strong>$<?php echo $valuation['valuation']['valuation_emv']; ?></strong>
<?php
} else {
?>

  <h3>Value Not Found</h3><a href="mailto:<?php echo $lead_email; ?>"><?php echo $lead_first_name; ?> <?php echo $lead_last_name; ?></a> searched for <em><strong><?php echo $searched_address; ?></strong></em> but no value was found.

<?php } ?>
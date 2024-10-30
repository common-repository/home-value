<?php
$lastMonth = new DateTime();
$lastMonth->modify('-2 year');

// Filter properties sold in the last month
$recentSales = array_filter($comparables, function ($property) use ($lastMonth) {
  $saleDate = DateTime::createFromFormat('Y-m-d', $property['attributes']['sale_date']);
  return $saleDate > $lastMonth;
});

// Sort properties by sale date, most recent first
usort($recentSales, function ($a, $b) {
  return strcmp($b['attributes']['sale_date'], $a['attributes']['sale_date']);
});

// Limit to 10 properties
$recentSales = array_slice($recentSales, 0, 10);

// if there are recent sales, show the title
if ($recentSales) {
  $comparables_text .= '<h5 class="hv-recent-sales">Recent Local Sales</h5>';
}


$map_location_data = [];
$map_location_center_data = "";
setlocale(LC_MONETARY, 'en_US');

foreach ($recentSales as $property) {
  if ($map_location_center_data == "") {
    $map_location_center_data = "{ lat: {$property['address']['latitude']}, lng: {$property['address']['longitude']} }";
  }

  $formattedPrice = number_format($property['attributes']['sale_price']);
  // convert Y-m-d to m/d/Y
  $formattedDate = date("m/d/Y", strtotime($property['attributes']['sale_date']));
  $map_location_data[] = [
    "<div class=\"show_value\"><p class=\"hv_address\">{$propert['address']['street']}</p><p class=\"hv_address\">Sale Price: \${$formattedPrice}</p><p class=\"hv_address\">Sale Date: {$formattedDate}</p></div>",
    $property['address']['latitude'],
    $property['address']['longitude'],
  ];
}

$map_location_data_json = json_encode($map_location_data);
$comparables_text .= <<<HTML
<div id="home_value_map" class="hv-map"></div>
<script>
initMap();
function initMap() {
    var map = new google.maps.Map(document.getElementById("home_value_map"), {
        zoom: 15,
        center: {$map_location_center_data},
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    setMarkers(map);
}
function setMarkers(map) {
    var locations = {$map_location_data_json};
    var image = {
        url: "https://maps.gstatic.com/mapfiles/ms2/micons/red-dot.png",
        size: new google.maps.Size(20, 32),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(0, 32)
    };
    var infowindow = new google.maps.InfoWindow();
    for (var i = 0; i < locations.length; i++) {  
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            map: map
        });
        google.maps.event.addListener(marker, "click", (function(marker, i) {
            return function() {
              infowindow.setContent(locations[i][0]);
              infowindow.open(map, marker);
            }
        })(marker, i));
    }
}
</script>
HTML;

echo $comparables_text;

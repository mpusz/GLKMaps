<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
   <head>
      <title>LKMAPS World</title>
      <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
     
      <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
      <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>

      <style type="text/css">
        body { height: 100%; margin: 0px; padding: 0px }
        #map_canvas { height: 800px; width: 800px; }
      </style>

<script type="text/javascript">
  var myState = {
    map: null,
    infoWindow: null
  };

  /**
   * Called when clicking anywhere on the map and closes the info window.
   */
  function closeInfoWindow() {
    myState.infoWindow.close();
  }

  
  /**
   * Opens the shared info window, anchors it to the specified overlay, and
   * displays the marker's position as its content.
   */
  function openInfoWindow(item) {
    var lat = (parseFloat(item.latmin) + parseFloat(item.latmax)) / 2;
    var lon = (parseFloat(item.lonmin) + parseFloat(item.lonmax)) / 2;
    var content = "<center><b>" + item.name + "</b></center><br/>" +
  "Longitude: " + item.lonmin + " - " + item.lonmax + "<br/>" +
  "Latitude: " + item.latmin + " - " + item.latmax + "<br/>" +
/*   '<img src="http://www.bware.it/listing/LKMAPS/' + item.mapzone + '/' + item.name + '.JPG" border="0" /><br/>'*/
  "Terrain:<br/>";
  if (item.res["1000"] == true) 
	content += "<a href=\"#\">1000</a><br/>";
  if (item.res["500"] == true) 
	content += "<a href=\"#\">500</a><br/>";
  if (item.res["250"] == true) 
	content += "<a href=\"#\">250</a><br/>";
  if (item.res["90"] == true) 
	content += "<a href=\"#\">90</a><br/>";

  if (item.topology == "YES")
	content += "Topology: <a target=\"_blank\" href=\"http://www.bware.it/listing/LKMAPS/" + item.mapzone + "/" + item.dir + ".DIR/" + item.name + ".LKM\">" + item.name + ".LKM";
    
    myState.infoWindow.setContent(content);
    myState.infoWindow.setPosition(new google.maps.LatLng(lat, lon));
    myState.infoWindow.open(myState.map);
  }


  function initialize() {
    
    var myOptions = {
      zoom: 4,
      center: new google.maps.LatLng(49, 17),
      mapTypeId: google.maps.MapTypeId.TERRAIN,
      streetViewControl: false,
      panControl: false,
      zoomControlOptions: {
        style: google.maps.ZoomControlStyle.DEFAULT
      },
      mapTypeControlOptions: {
        style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
      }
    };
    myState.map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
     
    // Create a single instance of the InfoWindow object which will be shared
    // by all Map objects to display information to the user.
    myState.infoWindow = new google.maps.InfoWindow();
  
    // Make the info window close when clicking anywhere on the map.
    google.maps.event.addListener(myState.map, 'click', closeInfoWindow);
  
  
        $.getJSON('LK8000Data.php?getJSON=1<?php if (isset($_REQUEST['filter'])) echo "&filter=".$_REQUEST['filter']; ?>', function(data)
        {
                $.each(data, function(i,item)
                {
                        coords = [
                                new google.maps.LatLng(item.latmin, item.lonmin),        
                                new google.maps.LatLng(item.latmax, item.lonmin),
                                new google.maps.LatLng(item.latmax, item.lonmax),
                                new google.maps.LatLng(item.latmin, item.lonmax),
                                new google.maps.LatLng(item.latmin, item.lonmin),
                        ];
                        var overlay = new google.maps.Polygon({
                                paths: coords,
                                strokeColor: item.color,
                                strokeOpacity: 0.8,
                                strokeWeight: 2,
                                fillColor: item.color,
                                fillOpacity: 0.35
                        });
                        overlay.setMap(myState.map);

                        google.maps.event.addListener(overlay, 'click', function() {
                                openInfoWindow(item);
                        });
                })        
        });
  }
  
  $(function()
  {
        initialize();
  })

</script>

   </head>
   <body>   
        <div id="map_canvas"></div>
   </body>
</html>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
   <head>
      <title>XHTML 1.0 Strict</title>
      <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
      <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<style type="text/css">
  body { height: 100%; margin: 0px; padding: 0px }
  #map_canvas { height: 800px; width: 800px; }
</style>

<script type="text/javascript">
  function initialize() {
    
    var myOptions = {
      zoom: 4,
      center: new google.maps.LatLng(49, 17),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
     
  
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
                                strokeColor: "#FF0000",
                                strokeOpacity: 0.8,
                                strokeWeight: 2,
                                fillColor: item.color,
                                fillOpacity: 0.35
                        });
                        overlay.setMap(map);
                        var infowindow = new google.maps.InfoWindow({
                                content: "file:<b>" + item.file + "</b>",                                 
                        });

                        google.maps.event.addListener(overlay, 'click', function(a) {
                                infowindow.open(map,overlay);
                                alert(infowindow.content);
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

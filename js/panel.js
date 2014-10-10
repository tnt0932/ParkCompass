google.maps.event.addDomListener(window, 'load', function() {
  var map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
  var mapOptions = {
            zoom: 11,
            center: new google.maps.LatLng(49.3, -123),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        
        
    var infoWindow = new google.maps.InfoWindow();
    
    var panelDiv = document.getElementById('sidebar');
    
    var data = new ParksDataSource();
    
    var view = new storeLocator.View(map, data, {
        geolocation: false,
        features: data.getFeatures()
    });
    
    new storeLocator.Panel(panelDiv, {
        view: view
  });
});

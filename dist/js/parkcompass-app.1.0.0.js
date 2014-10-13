var map;
var xml;
var markers = [];
var userMarker = [];
var infoWindow;
var search_result_list;
var userMarkerPosition = new google.maps.LatLng(49.25, -123.133333);
var parkIcon = 'img/park_icon.png';
var parkIconShadowURL = 'img/park_icon_shadow.png';
var parkIconShadowSize = new google.maps.Size(31, 32);
var parkIconShadowOrigin = new google.maps.Point(0, 0);
var parkIconShadowAnchor = new google.maps.Point(0, 31);
var parkIconShadow = new google.maps.MarkerImage(parkIconShadowURL, parkIconShadowSize, parkIconShadowOrigin, parkIconShadowAnchor);
var userIcon = 'img/user_icon.png';
var userIconShadowURL = 'img/user_icon_shadow.png';
var userIconShadowSize = new google.maps.Size(30, 34);
var userIconShadowOrigin = new google.maps.Point(0, 0);
var userIconShadowAnchor = new google.maps.Point(3, 34);
var userIconShadow = new google.maps.MarkerImage(userIconShadowURL, userIconShadowSize, userIconShadowOrigin, userIconShadowAnchor);
var markerClusterExists = false;
var initialLocation;
var browserSupportFlag;
var clickedFilters = [];



function load(lat, lng) {
    userMarkerPosition = new google.maps.LatLng(lat,lng);
    map = new google.maps.Map(document.getElementById("map_canvas"), {
        center: userMarkerPosition,
        zoom: 12,
        mapTypeId: 'roadmap',
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
        }
    });
    infoWindow = new google.maps.InfoWindow();
    search_result_list = $('#search_results_list');

    createUserMarker(map, userMarkerPosition);
    searchLocationsNear(userMarkerPosition);
    
}


function searchLocations() {
    var address = document.getElementById("location_search_input").value;
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({
        address: address
    }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
        userMarkerPosition = results[0].geometry.location;
            searchLocationsNear();
            clearUserMarker();
            createUserMarker(map, results[0].geometry.location);
            map.setCenter(results[0].geometry.location);
        } else {
            alert(address + ' not found');
        }
    });
}

function searchLocationsNear(args) {
    clearLocations();
    var radius = 100; //return all results in a 100km radius - basically, return all results
    var jsonResponseURL = 'json_gen.php?lat=' + userMarkerPosition.lat() + '&lng=' + userMarkerPosition.lng() + '&radius=' + radius + '&filters=' + JSON.stringify(clickedFilters);

    // Query generated JSON
    $.getJSON(jsonResponseURL, function(jsonResponseData) {
        parseParksJSON(jsonResponseData);
    });
}

function parseParksJSON(jsonResponseData) {
    var bounds = new google.maps.LatLngBounds();
    var parks = jsonResponseData.parks;

    if (parks.length === 0) {
        userMarkerPosition = new google.maps.LatLng(49.25, -123.133333);
        alert('No Metro Vancouver parks found in that area. We\'re going to move your marker back to the heart of Vancouver!');
        clearUserMarker();
        createUserMarker(map, userMarkerPosition);
        searchLocationsNear();
        map.setCenter(userMarkerPosition);
        return;
    }
    for (var i = 0; i < parks.length; i++)  {
        
        var facilitiesList = [];
        var facilities = parks[i].facilities;

        // for each facility, find their type & quantity and push it to facilitiesList
        for (var x = 0; x < facilities.length; x++) {
            facilitiesList.push([facilities[x].fType, facilities[x].fQuan]);
        }

        var name = parks[i].pName;
        var address = parks[i].pAddress;
        var neighbourhood = parks[i].nName;
        var url = parks[i].slug;
        var pID = parks[i].pID;
        var latlng = new google.maps.LatLng( parseFloat(parks[i].pLat), parseFloat(parks[i].pLng));
        var distance = parseFloat(parks[i].distance);

        createResults(name, distance, i);

        createMarker(latlng, name, address, neighbourhood, facilitiesList, url, pID);

        bounds.extend(latlng);
    }

    // Marker Clusterer
    var mcOptions = {
        maxZoom: 14,
        minimumClusterSize: 4
    };

    markerCluster = new MarkerClusterer(map, markers, mcOptions);
    markerClusterExists = true;

    search_result_list.click(function(e) {
        var markerNum = e.target.parentNode.id;
        google.maps.event.trigger(markers[markerNum], 'click');
    }); 
}



// Create the list of nearby parks
function createResults(name, distance, num) {
    var results = '<li id="'+num+'" class="search_result"><h2>' + name + '</h2><h2>' + distance.toFixed(1) + 'km</h2></li>';
    search_result_list.append(results);
}


function showingResultsFor() {
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({'latLng': userMarkerPosition}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        if (results[1]) {
          $('#showing_results_for_span').html(results[0].formatted_address);
        }
      } else {
        alert("Geocoder failed due to: " + status);
      }
    });
}


// ===========================================
//
//             FILTERS
//
// ===========================================


    $(document).ready(function() {
        $('#facilities_flyout').click(function(e) {
            // don't register event if user clicks on containing div, only directly on facilities
            if ($(e.target).attr('id') != 'facilities_flyout') {
                var target_id = $(e.target).attr('id').substr(6);
                if (!$(e.target).hasClass('facility_selected')) {
                    //console.log(e.target);
                    $(e.target).addClass('facility_selected');
                    clickedFilters.push(target_id);
                    //console.log(clickedFilters);
                } else {
                    $(e.target).removeClass('facility_selected');
                    for (var i = 0; i < clickedFilters.length; i++) {
                        if (clickedFilters[i] == target_id) {
                            clickedFilters.splice(i,1);
                        }
                    }
                    
                    //console.log(clickedFilters);
                }
                searchLocationsNear();
            }
        });
        $('#remove_all_filters_btn').click(function(e) {
            clickedFilters.length = 0;
            $('#facilities_flyout a').removeClass('facility_selected');
            searchLocationsNear();
        });
    });

// ===========================================
//
//             PARK MARKERS
//
// ===========================================

function createMarker(latlng, name, address, neighbourhood, facilitiesList, url, pID) {
    var directions = 'http://maps.google.com/maps?saddr='+ userMarkerPosition +'&daddr='+ latlng;
    var link = 'http://parkcompass.com/'+url;
    var listHtml = '<ul>';
    for (var i=0; i < facilitiesList.length; i++) {
        listHtml += '<li>'+facilitiesList[i][0]+'<span>'+facilitiesList[i][1]+'</span></li>';
    }
    listHtml += '</ul>';
    var html = '<div class="infowindow"><div id="photo'+pID+'"></div><h2>' + name + "</h2><br/><p>Address: <b>" + address + "</b></p><br/><p>Neighbourhood: <b>" + neighbourhood + "</b></p><br>" + listHtml + "<br><p>Share:<br><input type='text' value='"+link+"' onclick='this.select()' class='parkLink'><a href='" + directions + "' target='_blank'>Directions</a>";
    var marker = new google.maps.Marker({
        map: map,
        position: latlng,
        icon: parkIcon,
        shadow: parkIconShadow
    });
    google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
    });
    markers.push(marker);
}

function clearLocations() {
    infoWindow.close();
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(null);
    }
    markers.length = 0;
    search_result_list.empty();
    if (markerClusterExists) {
        markerCluster.clearMarkers();
        markerClusterExists = false;
    }
}



// ===========================================
//
//             USER MARKER
//
// ===========================================

function createUserMarker(map, center) {
    showingResultsFor(center);
    var usermarker = new google.maps.Marker({
        map: map,
        position: center,
        icon: userIcon,
        shadow: userIconShadow,
        draggable: true,
        zIndex: 99999
    });
    google.maps.event.addListener(usermarker, 'dragend', function() {
        userMarkerPosition = usermarker.getPosition();
        searchLocationsNear();
        showingResultsFor();
    });
    userMarker.push(usermarker);
    
}

function clearUserMarker() {
    for (var i = 0; i < userMarker.length; i++) {
        userMarker[i].setMap(null);
    }
    userMarker.length = 0;
}

// ===========================================
//
//                GEOLOCATION
//
// ===========================================

function geolocation() {
// Try W3C Geolocation (Preferred)
  if(navigator.geolocation) {
    browserSupportFlag = true;
    navigator.geolocation.getCurrentPosition(function(position) {
      userMarkerPosition = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
      searchLocationsNear();
      clearUserMarker();
      createUserMarker(map, userMarkerPosition);
      map.setCenter(userMarkerPosition);
    }, function() {
      handleNoGeolocation(browserSupportFlag);
    });
  }
  // Browser doesn't support Geolocation
  else {
    browserSupportFlag = false;
    handleNoGeolocation(browserSupportFlag);
  }
  
  function handleNoGeolocation(errorFlag) {
    if (errorFlag === true) {
      alert("Geolocation service failed. We've placed you in Downtown Vancouver.");
      //initialLocation = new google.maps.LatLng(userMarkerPosition);
    } else {
      alert("Your browser doesn't support geolocation so we've placed you in the heart of Vancouver!");
      //initialLocation = new google.maps.LatLng(userMarkerPosition);
    }
    map.setCenter(userMarkerPosition);
  }
}

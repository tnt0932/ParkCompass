/**
 * @implements storeLocator.DataFeed
 * @constructor
 */
function ParksDataSource() {
}

ParksDataSource.prototype.getStores = function(bounds, features, callback) {
  var center = bounds.getCenter();
  var that = this;
  var audioFeature = this.FEATURES_.getById('Audio-YES');
  var wheelchairFeature = this.FEATURES_.getById('Wheelchair-YES');
  
  downloadUrl("parkcompass.xml", function(data) {
        var xml = data.responseXML;
        var parks = xml.documentElement.getElementsByTagName("park");
        for (var i = 0; i < parks.length; i++) {
            var name = parks[i].getAttribute("pName");
            var address = parks[i].getAttribute("pAddress");
            var point = new google.maps.LatLng(
                parseFloat(parks[i].getAttribute("pLat")),
                parseFloat(parks[i].getAttribute("pLng")));
            var html = "<b>" + name + "</b> <br/>" + address;
            //var icon = customIcons[type] || {};
            var park = new google.maps.Marker({
              map: map,
              position: point,
              //icon: icon.icon,
              //shadow: icon.shadow
            });
            bindInfoWindow(park, map, infoWindow, html);
        }
    });

  $.getJSON('https://storelocator-go-demo.appspot.com/query?callback=?', {
    n: bounds.getNorthEast().lat(),
    e: bounds.getNorthEast().lng(),
    s: bounds.getSouthWest().lat(),
    w: bounds.getSouthWest().lng(),
    audio: features.contains(audioFeature) || '',
    access: features.contains(wheelchairFeature) || ''
  }, function(resp) {
    console.log(resp.strips.length, resp.strips[0].length, resp.time, resp.data.length);
    callback(that.parse_(resp.data));
  });
};

ParksDataSource.prototype.parse_ = function(data) {
  var stores = [];
  for (var i = 0, row; row = data[i]; i++) {
    var features = new storeLocator.FeatureSet;
    features.add(this.FEATURES_.getById('Wheelchair-' + row.Wheelchair));
    features.add(this.FEATURES_.getById('Audio-' + row.Audio));

    var position = new google.maps.LatLng(row.Ycoord, row.Xcoord);

    var shop = this.join_([row.Shp_num_an, row.Shp_centre], ', ');
    var locality = this.join_([row.Locality, row.Postcode], ', ');

    var store = new storeLocator.Store(row.uuid, position, features, {
      title: row.Fcilty_nam,
      address: this.join_([shop, row.Street_add, locality], '<br>'),
      hours: row.Hrs_of_bus
    });
    stores.push(store);
  }
  return stores;
};

/**
 * @const
 * @type {!storeLocator.FeatureSet}
 * @private
 */
ParksDataSource.prototype.FEATURES_ = new storeLocator.FeatureSet(
  new storeLocator.Feature('Wheelchair-YES', 'Wheelchair access'),
  new storeLocator.Feature('Audio-YES', 'Audio')
);

/**
 * @return {!storeLocator.FeatureSet}
 */
ParksDataSource.prototype.getFeatures = function() {
  return this.FEATURES_;
};


/**
 * Joins elements of an array that are non-empty and non-null.
 * @private
 * @param {!Array} arr array of elements to join.
 * @param {string} sep the separator.
 * @return {string}
 */
ParksDataSource.prototype.join_ = function(arr, sep) {
  var parts = [];
  for (var i = 0, ii = arr.length; i < ii; i++) {
    arr[i] && parts.push(arr[i]);
  }
  return parts.join(sep);
};

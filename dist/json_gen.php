<?php
require("db_config.php");
require("db_connect.php");

// Get parameters from URL
$center_lat = $_GET["lat"];
$center_lng = $_GET["lng"];
$radius = $_GET["radius"];
$filters = isset($_GET['filters']) ? json_decode($_GET['filters']) : array();
$filterloop = '';
if (count($filters) > 0) {
    $filterloop = 'WHERE ParkFacilities.ParkID IN (SELECT ParkFacilities.ParkID FROM ParkFacilities WHERE';
    $fcounter = 0;
    $or = '';
    foreach ($filters as $fvalue) {
        if ($fcounter) {
            $or = ' OR';
        }
        $filterloop .= $or . " ParkFacilities.FacilityID=$fvalue";
        $fcounter++;
    }
    $filterloop .= ")";
}

//Select all the rows in the parks table
$query = sprintf("SELECT Parks.ParkID as pID, slug, Parks.ParkName as pName, Parks.ParkURL as pURL, Parks.ParkAddress as pAddress, Parks.ParkLat as pLat, Parks.ParkLng as pLng, Parks.ParkHectares as pHectares, Neighbourhood.NeighbourhoodID as nID, Neighbourhood.NeighbourhoodName as nName, Neighbourhood.NeighbourhoodURL as nURL, Facilities.FacilityID as fID, Facilities.FacilityType as fType, Facilities.FacilityURL as fURL, ParkFacilities.FacilityQuantity as fQuan, ParkFacilities.ParkFacilityID as pfID,  ( 3959 * acos( cos( radians('%s') ) * cos( radians( Parks.ParkLat ) ) * cos( radians( Parks.ParkLng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( Parks.ParkLat ) ) ) ) AS distance 
FROM ParkFacilities 
INNER JOIN Parks ON (Parks.ParkID = ParkFacilities.ParkID) 
INNER JOIN Facilities ON (Facilities.FacilityID = ParkFacilities.FacilityID) 
INNER JOIN Neighbourhood ON (Parks.NeighbourhoodID = Neighbourhood.NeighbourhoodID)
$filterloop
HAVING distance < '%s' ORDER BY distance LIMIT 0 , 300",
mysql_real_escape_string($center_lat),
mysql_real_escape_string($center_lng),
mysql_real_escape_string($center_lat),
mysql_real_escape_string($radius)
);

// Store the result of the query
$result = mysql_query($query);
if (!$result) {
  die('Invalid query: ' . mysql_error());
}

header("Content-type: application/json");

$parkID;
//Create an array
$json_response = array();
$facilities = array();

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	if ($row['pID'] != $parkID){

	    $json_response['parks'][$row['pID']] = array(
	    	'pID'       => $row['pID'],
		    'pName'     => $row['pName'],
		    'pURL'      => $row['pURL'],
		    'pAddress'  => $row['pAddress'],
		    'pLat'      => $row['pLat'],
		    'pLng'      => $row['pLng'],
            'distance'  => $row['distance'],
            'pHectares' => $row['pHectares'],
            'slug'      => $row['slug'],
            'nID'       => $row['nID'],
            'nName'     => $row['nName'],
            'nURL'      => $row['nURL'],
	    );
        $facilities = array(
            'fID'       => $row['fID'],
            'fType'     => $row['fType'],
            'fURL'      => $row['fURL'],
            'fQuan'     => $row['fQuan'],
            'pfID'      => $row['pfID'],
        );
		

	} else {
        $facilities = array(
            'fID'       => $row['fID'],
            'fType'     => $row['fType'],
            'fURL'      => $row['fURL'],
            'fQuan'     => $row['fQuan'],
            'pfID'      => $row['pfID'],
        );
	}
	$json_response['parks'][$row['pID']]['facilities'][] = $facilities;
	$parkID = $row['pID'];
    

}

echo json_encode($json_response, JSON_PRETTY_PRINT);

?>



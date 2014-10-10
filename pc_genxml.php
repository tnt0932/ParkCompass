<?php

require("db_config.php");


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
//echo "<h1>$filterloop</h1>";

// Start XML file, create parent node
$dom = new DOMDocument("1.0");
$node = $dom->createElement("parks");
$parnode = $dom->appendChild($node); 

// Open a connection to the MySQL server
$connection = mysql_connect($hostname, $username, $password);
if (!$connection) {  die('Not connected : ' . mysql_error());} 

//Set the active MySQL database
$db_selected = mysql_select_db($database, $connection);
if (!$db_selected) {
  die ('Can\'t use db : ' . mysql_error());
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
//echo $query;
$result = mysql_query($query);
if (!$result) {
  die('Invalid query: ' . mysql_error());
}



header("Content-type: text/xml");

$parkID = '';

//Iterate through the rows, adding XML nodes for each
while ($row = @mysql_fetch_assoc($result)){

    if ($row['pID'] != $parkID){
        // ADD TO XML DOCUMENT NODE
        $node = $dom->createElement("park");
        $newnode = $parnode->appendChild($node);
            
            $newnode->setAttribute("pID", $row['pID']);
            $newnode->setAttribute("pName", $row['pName']);
            $newnode->setAttribute("pURL", $row['pURL']);
            $newnode->setAttribute("pAddress", $row['pAddress']);
            $newnode->setAttribute("pLat", $row['pLat']);
            $newnode->setAttribute("pLng", $row['pLng']);
            $newnode->setAttribute("distance", $row['distance']);
            $newnode->setAttribute("pHectares", $row['pHectares']);
            $newnode->setAttribute("slug", $row['slug']);
            $newnode->setAttribute("nID", $row['nID']);
            $newnode->setAttribute("nName", $row['nName']);
            $newnode->setAttribute("nURL", $row['nURL']);
        
        $facility = $dom->createElement("facility");
        $childnode = $newnode->appendChild($facility);

            $childnode->setAttribute("fID", $row['fID']);
            $childnode->setAttribute("fType", $row['fType']);
            $childnode->setAttribute("fURL", $row['fURL']);
            $childnode->setAttribute("fQuan", $row['fQuan']);
            $childnode->setAttribute("pfID", $row['pfID']);

    } else {
        
        $facility = $dom->createElement("facility");
        $childnode = $newnode->appendChild($facility);
            $childnode->setAttribute("fID", $row['fID']);
            $childnode->setAttribute("fType", $row['fType']);
            $childnode->setAttribute("fURL", $row['fURL']);
            $childnode->setAttribute("fQuan", $row['fQuan']);
            $childnode->setAttribute("pfID", $row['pfID']);
    }

    $parkID = $row['pID'];
}

echo $dom->saveXML();

?>

<?php
require("pc_dbinfo.php");


// Open a connection to the MySQL server
$connection = mysql_connect($hostname, $username, $password);
if (!$connection) {  die('Not connected : ' . mysql_error());} 

//Set the active MySQL database
$db_selected = mysql_select_db($database, $connection);
if (!$db_selected) {
  die ('Can\'t use db : ' . mysql_error());
} 

//Select all the rows in the parks table
$query = sprintf("SELECT Parks.ParkID as pID, slug, Parks.ParkName as pName, Parks.ParkURL as pURL, Parks.ParkAddress as pAddress, Parks.ParkLat as pLat, Parks.ParkLng as pLng, Parks.ParkHectares as pHectares, Neighbourhood.NeighbourhoodID as nID, Neighbourhood.NeighbourhoodName as nName, Neighbourhood.NeighbourhoodURL as nURL, Facilities.FacilityID as fID, Facilities.FacilityType as fType, Facilities.FacilityURL as fURL, ParkFacilities.FacilityQuantity as fQuan, ParkFacilities.ParkFacilityID as pfID
FROM ParkFacilities 
INNER JOIN Parks ON (Parks.ParkID = ParkFacilities.ParkID) 
INNER JOIN Facilities ON (Facilities.FacilityID = ParkFacilities.FacilityID) 
INNER JOIN Neighbourhood ON (Parks.NeighbourhoodID = Neighbourhood.NeighbourhoodID)"
);
//echo $query;
$result = mysql_query($query);
if (!$result) {
  die('Invalid query: ' . mysql_error());
}


$parkID = '';

$model = array();
$i = 0;
$f = 0;
$x = 0;
while ($row = @mysql_fetch_assoc($result)){
    
    

    if ($row['pID'] != $parkID){
        $model[$i]['pID'] = $row['pID'];
        $model[$i]['pName'] = $row['pName'];
        $model[$i]['facility'][$f]['fID'] = $row['fID'];
        $model[$i]['facility'][$f]['fType'] = $row['fType'];
        $x = $i;


    } else {
        $model[$x]['facility'][$f]['fID'] = $row['fID'];
        $model[$x]['facility'][$f]['fType'] = $row['fType'];
        $f++;

    }
    $parkID = $row['pID'];
    $i++;
};

    // $i = 0;
    // while($e = mysql_fetch_assoc($result)) {
    //     $model[$i]['title']       = $e['title'];
    //     $model[$i]['content']     = $e['content'];
    //     $model[$i]['lat']         = $e['lat'];
    //     $model[$i]['lng']         = $e['lng'];
    //     $i++;
    // }
echo json_encode($model, JSON_PRETTY_PRINT);

//Iterate through the rows, adding XML nodes for each
// while ($row = @mysql_fetch_assoc($result)){

//     if ($row['pID'] != $parkID){
//         // ADD TO XML DOCUMENT NODE
//         $node = $dom->createElement("park");
//         $newnode = $parnode->appendChild($node);
            
//             $newnode->setAttribute("pID", $row['pID']);
//             $newnode->setAttribute("pName", $row['pName']);
//             $newnode->setAttribute("pURL", $row['pURL']);
//             $newnode->setAttribute("pAddress", $row['pAddress']);
//             $newnode->setAttribute("pLat", $row['pLat']);
//             $newnode->setAttribute("pLng", $row['pLng']);
//             $newnode->setAttribute("distance", $row['distance']);
//             $newnode->setAttribute("pHectares", $row['pHectares']);
//             $newnode->setAttribute("slug", $row['slug']);
//             $newnode->setAttribute("nID", $row['nID']);
//             $newnode->setAttribute("nName", $row['nName']);
//             $newnode->setAttribute("nURL", $row['nURL']);
        
//         $facility = $dom->createElement("facility");
//         $childnode = $newnode->appendChild($facility);

//             $childnode->setAttribute("fID", $row['fID']);
//             $childnode->setAttribute("fType", $row['fType']);
//             $childnode->setAttribute("fURL", $row['fURL']);
//             $childnode->setAttribute("fQuan", $row['fQuan']);
//             $childnode->setAttribute("pfID", $row['pfID']);

//     } else {
        
//         $facility = $dom->createElement("facility");
//         $childnode = $newnode->appendChild($facility);
//             $childnode->setAttribute("fID", $row['fID']);
//             $childnode->setAttribute("fType", $row['fType']);
//             $childnode->setAttribute("fURL", $row['fURL']);
//             $childnode->setAttribute("fQuan", $row['fQuan']);
//             $childnode->setAttribute("pfID", $row['pfID']);
//     }

//     $parkID = $row['pID'];
// }

// echo $dom->saveXML();

?>

<?php

//****************************************************************************************************
//	PROJECT				: 	Visualization of Taxonomic Interactions
//	Developer			: 	Rathachai CHAWUTHAI		(National Institute of Informatics,Japan)
//	Supervisor			:	Hideaki TAKEDA			(National Institute of Informatics,Japan)
//	Product Mananger	:	Tsuyoshi HOSOYA			(National Museum of Nature and Science, Japan)
//	Created 			:	2015
//****************************************************************************************************

header('Content-Type: application/json');
include('../common/first.php');
include('../localization.php');
include('../config.php');
include('../common/helpers.php');
include('../lib/sparql/sparql.php');

$qr = $_GET["q"];

//language
$lang = $GLOBAL["lang"];
if(!in_array($lang, $DATA_LANGUAGE["possible"])){
	$lang = $DATA_LANGUAGE["default"];
}

// QUERY
$db = sparql_connect( $SERVICES["sparql"] );
	if( !$db ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

$data = array();
$data["nodes"][] = createTaxon($db, $qr, $lang) ;
$data["links"] = array();

$json = json_encode($data);
echo $json;

?>
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


// DATA
if(strlen($qr)>2){
	$db = sparql_connect( $SERVICES["sparql"] );
	if( !$db ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
	
	//sparql_ns( "lodac",$NAMESPACES["lodac"] );

	 
	$sparql = "SELECT DISTINCT ?u ?l ?us ?ur
				WHERE { 
					?u rdfs:label ?l .
					OPTIONAL { ?u <http://www.w3.org/2004/02/skos/core#subject> ?us }
					OPTIONAL { ?u <http://lod.ac/ns/species#hasTaxonRank> ?ur }
					FILTER regex(?l, '$qr', 'i'). 
				} LIMIT 20 ";

	$result = sparql_query( $sparql ); 
	if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
	 
	$fields = sparql_field_array( $result );

	$data = array();
	$nodes = array();

	while( $row = sparql_fetch_array( $result ) )
	{
		$label = $row["l"] ;
		$uri = $row["u"] ;
		$subject = getUriName($row["us"]) ;
		$rank = getUriName($row["ur"]) ;

		if(!in_array($uri, $nodes)){ 
			$data[] = array("id"=>$uri, "label"=>$label, "subject"=>$subject, "rank"=>$rank ); 
			$nodes[] = $uri; //remove duplicated
		}

	}

	//$json = join("," , $arr);
}

//echo "[$json]";
$json = json_encode($data);
echo $json;

?>
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

//DATA
$json = "";

//-- Ignore --
$ignoreProps = array_merge($PROPSET["noise"]);
if(!isset($_GET["taxonomy"])){
	$ignoreProps = array_merge($ignoreProps, $PROPSET["taxonomy"]);
}
if(!isset($_GET["interaction"])){
	$ignoreProps = array_merge($ignoreProps, $PROPSET["interaction"]);
}
$filterOut = "?p != <".implode("> && ?p != <", $ignoreProps).">" ;

//echo $filterOut;

if(strlen($qr)>2){
	$db = sparql_connect( $SERVICES["sparql"] );
	if( !$db ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
	 
	$sparql = "SELECT ?s ?p ?o 
				WHERE {
				  ?s ?p ?o .
				  FILTER   (isIRI(?o) && isIRI(?s) && ?s!=?o)
				  FILTER   ( $filterOut )
				  FILTER   (?s = <$qr> || ?o = <$qr> )
				}
				LIMIT 5
			";

	$result = sparql_query( $sparql ); 
	if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
	 
	$fields = sparql_field_array( $result );

	//$arr = array();
	$data = array();
	$nodes = array();
	$links = array();


	while( $row = sparql_fetch_array( $result ) )
	{
		//print_r($row); echo "<br/>";

		$subj  = $row["s"] ;
		$obj  = $row["o"] ;
		$pred    = $row["p"] ;

		if(!in_array($subj, $nodes)){
			$data["nodes"][] = $data["nodes"][] = createTaxon($db, $subj, $lang) ;
			$nodes[]=$subj;
		}

		if(!in_array($obj, $nodes)){
			$data["nodes"][] = $data["nodes"][] = createTaxon($db, $obj, $lang) ;
			$nodes[]=$obj;
		}

		$linkid = hashLink($subj, $obj);
		if(!in_array($linkid, $links)){
			$data["links"][] = createLink($subj,$pred,$obj, $linkid,$INVERSEPROPS, $LINKTYPES);
			$links[] = $linkid;
		}


	}
}

$json = json_encode($data);
echo $json;

?>
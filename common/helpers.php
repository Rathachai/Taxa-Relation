<?php

//****************************************************************************************************
//	PROJECT				: 	Visualization of Taxonomic Interactions
//	Developer			: 	Rathachai CHAWUTHAI		(National Institute of Informatics,Japan)
//	Supervisor			:	Hideaki TAKEDA			(National Institute of Informatics,Japan)
//	Product Mananger	:	Tsuyoshi HOSOYA			(National Museum of Nature and Science, Japan)
//	Created 			:	2015
//****************************************************************************************************

function getUriName($uri){
	if(strpos($uri, '#')){
		$name = substr(strrchr($uri, "#"), 1);
	}else{
		$name = substr(strrchr($uri, "/"), 1);
	}

	return $name;
}

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

function queryUriName($db, $uri){
	//Default
	$label = getUriName($uri);

	//Sparql
	$sparql = "SELECT ?l where { <$uri> rdfs:label ?l } limit 1";
	$result = sparql_query( $sparql ); 
	if( !$result ) { return $label; }

	//Attract
	$fields = sparql_field_array( $result );
	while( $row = sparql_fetch_array( $result ) )
	{
		$label  = $row["l"] ;
	}

	return $label;
}

function queryTaxon($db, $uri){
	//Default
	$label = getUriName($uri);
	$rank = "";
	$subject = "";

	//Sparql
	$sparql = "SELECT ?l ?r ?s 
				where { <$uri>  rdfs:label ?l; 
								<http://lod.ac/ns/species#hasTaxonRank> ?r ;  
								<http://www.w3.org/2004/02/skos/core#subject> ?s 
						} limit 1";

	$result = sparql_query( $sparql ); 
	if( !$result ) { return $label; }

	//Attract
	$fields = sparql_field_array( $result );
	while( $row = sparql_fetch_array( $result ) )
	{
		$label  = $row["l"] ;
		$rank  = getUriName($row["r"]) ;
		$subject  = getUriName($row["s"]) ;
	}

	return array("label"=>$label,"rank"=>$rank,"subject"=>$subject) ;
}


function hashLink($s,$t){
	$arr = array($s,$t);
	sort($arr);
	$str = implode(" ", $arr);
	return hash('ripemd160', $str);
}

function tryInverseTriple(&$subj, &$pred,&$obj,$INVERSEPROPS)
{
    if(isset($INVERSEPROPS[$pred])){
    	$temp = $subj;
    	$subj = $obj;
    	$obj  = $temp;
    	$pred = $INVERSEPROPS[$pred];
    }

    return 1;
}

function createLink($subj, $pred, $obj, $linkid,$INVERSEPROPS, $LINKTYPES){
	tryInverseTriple($subj,$pred,$obj,$INVERSEPROPS);

	$ltype = $LINKTYPES["default"];
	if(isset($LINKTYPES[$pred])){
		$ltype = $LINKTYPES[$pred];
	}

	return array("id"=>$linkid , "source" => $subj, "target" => $obj, "predicate" => $pred, "label" => getUriName($pred), "type" => $ltype);
}

function createTaxon($db, $uri, $lang){
	//Default
	$label = getUriName($uri);
	$rank = "";
	$subject = "";

	//Sparql
	$sparql = "SELECT ?l ?r ?s ?en
				where { <$uri>  rdfs:label ?l; 
								rdfs:label ?en;
								<http://lod.ac/ns/species#hasTaxonRank> ?r ;  
								<http://www.w3.org/2004/02/skos/core#subject> ?s 
								FILTER langMatches( lang(?l), '$lang' )
								FILTER langMatches( lang(?en), 'en' )
						} limit 1";

	$result = sparql_query( $sparql ); 
	if( !$result ) { return $label; }

	//Attract
	$fields = sparql_field_array( $result );
	while( $row = sparql_fetch_array( $result ) )
	{
		$label  = $row["l"] ;
		$altLabel  = $row["en"] ;
		$rank  = getUriName($row["r"]) ;
		$subject  = getUriName($row["s"]) ;
	}

	return array("id"=> $uri, "label"=>$label, "altlabel"=>$altLabel , "rank"=>$rank,"subject"=>$subject) ;
}



?>
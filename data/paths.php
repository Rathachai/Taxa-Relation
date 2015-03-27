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
include('../lib/relfinder/RelationFinder.php');

include('../lib/simplerdf/simplerdf.php');
include('../lib/simplerdf/simplerdfnode.php');
include('../lib/simplerdf/simplerdfresource.php');
include('../lib/simplerdf/simplerdfliteral.php');
include('../lib/simplerdf/simplerdftriple.php');

//--------BEGIN -----//

$qr1 = $_GET["q1"];
$qr2 = $_GET["q2"];
$hops = (int)$_GET["hops"];

//language
$lang = $GLOBAL["lang"];
if(!in_array($lang, $DATA_LANGUAGE["possible"])){
	$lang = $DATA_LANGUAGE["default"];
}

//Data

$data = array();
$nodes = array();
$links = array();

$db = sparql_connect( $SERVICES["sparql"] );
if( !$db ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }

//----- ignore -----//

$ignoreProps = array_merge($PROPSET["noise"]);
if(!isset($_GET["taxonomy"])){
	$ignoreProps = array_merge($ignoreProps, $PROPSET["taxonomy"]);
}
if(!isset($_GET["interaction"])){
	$ignoreProps = array_merge($ignoreProps, $PROPSET["interaction"]);
}

//----- RelFinder ----//

$rf = new RelationFinder();
$rf->endpointURI = $SERVICES["sparql"];

$QRS = $rf->getQueries($qr1, $qr2, $hops, $SYSTEM["query-limit"]*$hops, array(), $ignoreProps, true);
//$QRS = $rf->getQueries($qr1, $qr2, $hops, 10, array(), array(), true);


//----- RDF----//

$triples = array();

//for( $i=1 ; $i<=$hops; $i++){
for( $i=$hops ; $i<=$hops; $i++){

	//echo "\n\n\n=======================  HOP = $i  =================================\n\n\n";

	foreach($QRS[$i] as $query){
		//echo $query;
		try{
			$hAccept = $SYSTEM["header-accept"];
			$result = sparql_construct( $query, $hAccept ); 
			//echo "$query \n $result \n------\n";
			if(!empty($result)){
				//echo $result;
				$rdf=new SimpleRdf();
				if($hAccept=="Accept: application/rdf+xml"){	
					$rdf->loadXml($result);
				}else{
					$rdf->loadNt($result);
				}
				$tps=$rdf->getStatements();
				foreach($tps as $tp){
					$triples[] = $tp;
				}

				if(count($tps)>1) { break;}
			}
		}catch(Exception $e){echo 'Caught exception: ',  $e->getMessage(), "\n";}

		//echo "\n\n\n                      ------------------------\n\n\n"; 
	}

}



$rdf=new SimpleRdf();
$rdf->setStatements($triples);

$subjs = $rdf->find(FALSE, FALSE, FALSE, RDF_RETURN_SUBJECT  |  RDF_RETURN_VALUE);
foreach($subjs as $subj){
	if(!startsWith($subj,"_:SimpleRDF")){

		$preds = $rdf->find($subj, FALSE, FALSE, RDF_RETURN_PREDICATE  |  RDF_RETURN_VALUE);
		foreach($preds as $pred){
			$objs = $rdf->find($subj, $pred, FALSE, RDF_RETURN_OBJECT  |  RDF_RETURN_VALUE);
			foreach($objs as $obj){

				//$subjTaxon = queryTaxon($db, $subj);
				//$objTaxon = queryTaxon($db, $obj);

				if(!in_array($subj, $nodes)){
					//$data["nodes"][] = array("id"=>$subj, "name"=>$subjTaxon["label"], "subject"=>$subjTaxon["subject"], "rank"=>$subjTaxon["rank"]); 
					$data["nodes"][] = createTaxon($db, $subj, $lang) ;
					$nodes[]=$subj;
				}

				if(!in_array($obj, $nodes)){
					//$data["nodes"][] = array("id"=>$obj, "name"=>$objTaxon["label"], "subject"=>$objTaxon["subject"], "rank"=>$objTaxon["rank"] ); 
					$data["nodes"][] = createTaxon($db, $obj, $lang) ;
					$nodes[]=$obj;
				}

				$linkid = hashLink($subj, $obj);
				if( !in_array($linkid, $links) && $subj!=$obj){
					$data["links"][] = createLink($subj,$pred,$obj, $linkid,$INVERSEPROPS, $LINKTYPES);
					$links[]=$linkid;
				}
			}
		}
	}
}


$json = json_encode($data);
echo $json;

?>